<?php

namespace Ferotres\RedsysBundle\Redsys;

use Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException;

/**
 * Class PaymentOrder
 * @package CoreBiz\Redsys
 */
final class PaymentOrder
{
    const VALID_CURRENCIES  = ['EUR','USD','GBP','AUD'];
    const VALID_LOCALES     = ['ES','EN','IT','NL','DE','FR','EL','RU'];

    /** Order types */
    const BLOCK_PAYMENT     = 'O';
    const DIRECT_PAYMENT    = '0';
    const WS_DIRECT_PAYMENT = 'A';
    const CONFIRM_PAYMENT   = 'P';
    const CANCEL_PAYMENT    = 'Q';

    /** @var int  */
    private $amount;
    /** @var string  */
    private $currency;
    /** @var string  */
    private $titular;
    /** @var string  */
    private $description;
    /** @var string  */
    private $locale;
    /** @var string  */
    private $authCode;
    /** @var string  */
    private $type;
    /** @var bool  */
    private $ces;
    /** @var  string */
    private $app;
    /** @var  string */
    private $order;
    /** @var array */
    private $routeParams;

    /**
     * PaymentOrder constructor.
     * @param string $app
     * @param string $currency
     * @param string $locale
     * @param string $order
     * @param float $amount
     * @param string $titular
     * @param string $description
     * @param string|null $authCode
     * @param bool $ces
     * @param array $routeParams
     * @throws PaymentOrderException
     */
    private function __construct(
        string $app,
        string $currency,
        string $locale,
        string $order,
        float  $amount,
        string $titular,
        string $description,
        string $authCode = null,
        bool   $ces      = false,
        array  $routeParams = []

    ) {
        $this->app = $app;
        $this->currency    = strtoupper($currency);
        $this->locale      = strtoupper($locale);;
        $this->order       = $order;
        $this->amount      = $amount;
        $this->titular     = $titular;
        $this->description = $description;
        $this->authCode    = $authCode;
        $this->ces         = $ces;
        $this->routeParams = $routeParams;
        $this->isValidPaymentOrderData();
    }

    /**
     * @param string $app
     * @param string $currency
     * @param string $locale
     * @param string $order
     * @param float $amount
     * @param string $titular
     * @param string $description
     * @param string|null $authCode
     * @param bool $ces
     * @param array $routeParams
     * @return PaymentOrder
     * @throws PaymentOrderException
     */
    public static function create(
        string $app,
        string $currency,
        string $locale,
        string $order,
        float  $amount,
        string $titular,
        string $description,
        string $authCode = null,
        bool   $ces      = false,
        array  $routeParams = []
    ) {
        return new self($app, $currency, $locale, $order, $amount, $titular, $description, $authCode, $ces, $routeParams);
    }

    /**
     * @throws PaymentOrderException
     */
    private function isValidPaymentOrderData()
    {
        if(!$this->amount > 0){
            throw new PaymentOrderException("The amount must be grather than 0");
        }

        if(!$this->app()){
            throw new PaymentOrderException("The app name is required");
        }

        if(!in_array($this->locale(), self::VALID_LOCALES)){
            throw new PaymentOrderException("The locale is not valid");
        }

        if(!in_array($this->currency(), self::VALID_CURRENCIES)){
            throw new PaymentOrderException("The currency is not valid");
        }
    }

    /**
     * @return int
     */
    public function amount(): int
    {
        return (string)$this->amount;
    }

    /**
     * @return string
     */
    public function currency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function titular(): string
    {
        return $this->titular;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function order(): string
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function authCode(): ?string
    {
        return $this->authCode;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function ces() :bool
    {
        return $this->ces;
    }

    /**
     * @return string
     */
    public function app() :string
    {
        return $this->app;
    }

    /**
     * @return array
     */
    public function routeParams():array
    {
        return $this->routeParams;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
}