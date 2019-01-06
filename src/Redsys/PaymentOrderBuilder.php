<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys;

use Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException;

/**
 * Class PaumentOrderBuilder.
 */
final class PaymentOrderBuilder
{
    /** @var array */
    private $paymentOrderData;

    /**
     * PaymentOrderBuilder constructor.
     */
    private function __construct()
    {
        $this->paymentOrderData = array(
            'app' => null,
            'currency' => 'EUR',
            'locale' => 'ES',
            'order' => null,
            'amount' => null,
            'titular' => '',
            'description' => '',
            'authCode' => null,
            'ces' => false,
            'userParams' => array(),
        );
    }

    /**
     * @return PaymentOrderBuilder
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @return PaymentOrder
     *
     * @throws PaymentOrderException
     */
    public function build()
    {
        if (is_null($this->paymentOrderData['app'])
            || is_null($this->paymentOrderData['order'])
            || is_null($this->paymentOrderData['amount'])
        ) {
            throw new PaymentOrderException('app|amount|order are required parameters');
        }

        return PaymentOrder::create(
            $this->paymentOrderData['app'],
            $this->paymentOrderData['currency'],
            $this->paymentOrderData['locale'],
            $this->paymentOrderData['order'],
            $this->paymentOrderData['amount'],
            $this->paymentOrderData['titular'],
            $this->paymentOrderData['description'],
            $this->paymentOrderData['authCode'],
            $this->paymentOrderData['ces'],
            $this->paymentOrderData['userParams']
        );
    }

    public function toApp(string $appName)
    {
        $this->paymentOrderData['app'] = $appName;

        return $this;
    }

    public function withCurrency(string $currency)
    {
        $this->paymentOrderData['currency'] = $currency;

        return $this;
    }

    public function withLocale(string $locale)
    {
        $this->paymentOrderData['locale'] = $locale;

        return $this;
    }

    public function withOrder(string $order)
    {
        $this->paymentOrderData['order'] = $order;

        return $this;
    }

    public function withAmount(float $amount)
    {
        $this->paymentOrderData['amount'] = $amount;

        return $this;
    }

    public function withTitular(string $titular)
    {
        $this->paymentOrderData['titular'] = $titular;

        return $this;
    }

    public function withDescription(string $description)
    {
        $this->paymentOrderData['description'] = $description;

        return $this;
    }

    public function withAuthCode(string $authCode)
    {
        $this->paymentOrderData['authCode'] = $authCode;

        return $this;
    }

    public function usingCes(bool $ces)
    {
        $this->paymentOrderData['ces'] = $ces;

        return $this;
    }

    public function addUserParams(array $userParams)
    {
        $this->paymentOrderData['userParams'] = $userParams;

        return $this;
    }
}
