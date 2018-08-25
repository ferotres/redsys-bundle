<?php

namespace Ferotres\RedsysBundle\Redsys\Services;

use Ferotres\RedsysBundle\Entity\RedsysOrderTrace;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysException;
use Ferotres\RedsysBundle\Redsys\PaymentOrder;
use Ferotres\RedsysBundle\Redsys\Redsys;
use Ferotres\RedsysBundle\Redsys\RedsysOrder;
use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use GuzzleHttp\Client;


/**
 * Class RedsysRedirection
 * @package CoreBiz\Redsys
 */
final class RedsysRedirection extends Redsys
{
    /**
     * @param PaymentOrder $paymentOrder
     * @return RedsysOrder|mixed
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    function createAuthorization(PaymentOrder $paymentOrder)
    {
        $paymentOrder->setType(PaymentOrder::BLOCK_PAYMENT);
        $redsysData  = $this->getRedsysData($paymentOrder);
        $trace       = $this->registerOrder($redsysData, $paymentOrder);
        $redsysData['DS_MERCHANT_MERCHANTURL'] .= '&trace='.$trace->getId();
        $redsysOrder = $this->getRedsysOrder($redsysData);
        return $redsysOrder;
    }

    /**
     * @param PaymentOrder $paymentOrder
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    function confirmAuthorization(PaymentOrder $paymentOrder)
    {
        if (!$paymentOrder->authCode()) {
            throw new RedsysException("For confirm authorization, authCode is required");
        }

        $paymentOrder->setType(PaymentOrder::CONFIRM_PAYMENT);
        $response = $this->sendRequest($paymentOrder);
        return $response;
    }

    /**
     * @param PaymentOrder $paymentOrder
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    function cancelAuthorization(PaymentOrder $paymentOrder)
    {
        if (!$paymentOrder->authCode()) {
            throw new RedsysException("For cancel authorization, authCode is required");
        }

        $paymentOrder->setType(PaymentOrder::CANCEL_PAYMENT);
        $response = $this->sendRequest($paymentOrder);
        return $response;
    }

    /**
     * @param PaymentOrder $paymentOrder
     * @return RedsysOrder|mixed
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    public function createPayment(PaymentOrder $paymentOrder)
    {
        $paymentOrder->setType(PaymentOrder::DIRECT_PAYMENT);
        $redsysData  = $this->getRedsysData($paymentOrder);
        $trace          = $this->registerOrder($redsysData, $paymentOrder);
        $redsysData['DS_MERCHANT_MERCHANTURL'] .= '&trace='.$trace->getId();
        $redsysOrder = $this->getRedsysOrder($redsysData);
        return $redsysOrder;
    }

    /**
     * @param RedsysResponse $response
     * @return bool
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    public function validatePaymentResponse(RedsysResponse $response): bool
    {
        $app       = $this->getApp($response->app());
        $terminal  = $this->findTerminalByNumber((int)$response->terminal(), $app['terminals']);
        $signature = $this->redsysHelper->createSignature($terminal['secret'], $response->params(), $response->order());
        $signature = strtr($signature, '+/', '-_');

        $valid = false;
        if($signature == $response->signature()){
            $valid = true;
        }
        return $valid;
    }

    /**
     * @param PaymentOrder $paymentOrder
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    protected function sendRequest(PaymentOrder $paymentOrder)
    {
        $redsysData     = $this->getRedsysData($paymentOrder);
        $trace          = $this->registerOrder($redsysData, $paymentOrder);
        $redsysData['DS_MERCHANT_MERCHANTURL'] .= '&trace='.$trace->getId();
        $redsysOrder    = $this->getRedsysOrder($redsysData);

        $client = new Client();
        return $client->request('POST', $this->url, ['form_params' => $redsysOrder->toArray()['formData'] ]);
    }

    /**
     * @param array $redsysData
     * @return RedsysOrder
     */
    protected function getRedsysOrder(array $redsysData) :RedsysOrder
    {

        $secret      = $this->terminal['secret'];
        $encodedData = $this->redsysHelper->createMerchantParameters( $redsysData );
        $signature   = $this->redsysHelper->createSignature($secret, $encodedData, $redsysData['DS_MERCHANT_ORDER'] );

        return new RedsysOrder($this->url,self::VERSION, $signature, $encodedData);
    }

    /**
     * @param PaymentOrder $paymentOrder
     * @return array
     * @throws RedsysException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     */
    protected function getRedsysData(PaymentOrder $paymentOrder):array
    {
        $redsysData = parent::getBasicRedsysData($paymentOrder);
        $language   = $this->getCorrespondenceLanguage($paymentOrder->locale());

        $redsysData['DS_MERCHANT_MERCHANTURL']      = $this->notification;
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

    /**
     * @param array $redsysData
     * @param PaymentOrder $paymentOrder
     * @return RedsysOrderTrace
     */
    public function registerOrder(array $redsysData, PaymentOrder $paymentOrder)
    {
        $trace = new RedsysOrderTrace();
        $trace->setAmount($redsysData['DS_MERCHANT_AMOUNT']);
        $trace->setCes($paymentOrder->ces());
        $trace->setCurrency($redsysData['DS_MERCHANT_CURRENCY']);
        $trace->setOrderNumber($redsysData['DS_MERCHANT_ORDER']);
        $trace->setOrderType($redsysData['DS_MERCHANT_TRANSACTION_TYPE']);
        $trace->setShop($paymentOrder->app());
        $trace->setTerminal($redsysData['DS_MERCHANT_TERMINAL']);

        $this->redsysOrderTraceRepository->save($trace);
        return $trace;
    }
}