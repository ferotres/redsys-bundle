<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys\Services;

use Ferotres\RedsysBundle\Redsys\Exception\RedsysException;
use Ferotres\RedsysBundle\Redsys\PaymentOrder;
use Ferotres\RedsysBundle\Redsys\Redsys;
use Ferotres\RedsysBundle\Redsys\RedsysOrder;
use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Class RedsysRedirection.
 */
final class RedsysRedirection extends Redsys
{
    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return RedsysOrder|mixed
     *
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    public function createAuthorization(PaymentOrder $paymentOrder)
    {
        $paymentOrder->setType(PaymentOrder::BLOCK_PAYMENT);
        $redsysData = $this->getRedsysData($paymentOrder);
        $redsysOrder = $this->getRedsysOrder($redsysData);

        return $redsysOrder;
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function confirmAuthorization(PaymentOrder $paymentOrder)
    {
        if (!$paymentOrder->authCode()) {
            throw new RedsysException('For confirm authorization, authCode is required');
        }

        $paymentOrder->setType(PaymentOrder::CONFIRM_PAYMENT);
        $response = $this->sendRequest($paymentOrder);

        return $response;
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancelAuthorization(PaymentOrder $paymentOrder)
    {
        if (!$paymentOrder->authCode()) {
            throw new RedsysException('For cancel authorization, authCode is required');
        }

        $paymentOrder->setType(PaymentOrder::CANCEL_PAYMENT);
        $response = $this->sendRequest($paymentOrder);

        return $response;
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return RedsysOrder|mixed
     *
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    public function createPayment(PaymentOrder $paymentOrder)
    {
        $paymentOrder->setType(PaymentOrder::DIRECT_PAYMENT);
        $redsysData = $this->getRedsysData($paymentOrder);
        $redsysOrder = $this->getRedsysOrder($redsysData);

        return $redsysOrder;
    }

    /**
     * @param RedsysResponse $response
     *
     * @return bool
     *
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    public function validatePaymentResponse(RedsysResponse $response): bool
    {
        $app = $this->getApp($response->app());
        $terminal = $this->findTerminalByNumber((int) $response->terminal(), $app['terminals']);
        $signature = $this->redsysHelper->createSignature($terminal['secret'], $response->params(), $response->order());
        $signature = strtr($signature, '+/', '-_');

        $valid = false;
        if ($signature == $response->signature()) {
            $valid = true;
        }

        return $valid;
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest(PaymentOrder $paymentOrder)
    {
        $redsysData = $this->getRedsysData($paymentOrder);
        $redsysOrder = $this->getRedsysOrder($redsysData);

        return $this->client->request('POST', $this->url, ['form_params' => $redsysOrder->toArray()['formData']]);
    }

    /**
     * @param array $redsysData
     *
     * @return RedsysOrder
     */
    protected function getRedsysOrder(array $redsysData): RedsysOrder
    {
        $secret = $this->terminal['secret'];
        $encodedData = $this->redsysHelper->createMerchantParameters($redsysData);
        $signature = $this->redsysHelper->createSignature($secret, $encodedData, $redsysData['DS_MERCHANT_ORDER']);

        return new RedsysOrder($this->url, self::VERSION, $signature, $encodedData);
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return array
     *
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    protected function getRedsysData(PaymentOrder $paymentOrder): array
    {
        $redsysData = parent::getBasicRedsysData($paymentOrder);
        $language = $this->getCorrespondenceLanguage($paymentOrder->locale());

        $redsysData['DS_MERCHANT_MERCHANTURL'] = $this->notification;
        $redsysData['DS_MERCHANT_CONSUMERLANGUAGE'] = $language;

        if (!in_array($paymentOrder->type(), [paymentOrder::CONFIRM_PAYMENT, paymentOrder::CANCEL_PAYMENT])) {
            $redsysData['DS_MERCHANT_URLOK'] = $this->successUrl;
            $redsysData['DS_MERCHANT_URLKO'] = $this->errorUrl;
        }

        if (in_array($paymentOrder->type(), [paymentOrder::CONFIRM_PAYMENT, paymentOrder::CANCEL_PAYMENT])) {
            $redsysData['DS_MERCHANT_AUTHORISATIONCODE'] = $paymentOrder->authCode();
        }

        return $redsysData;
    }
}
