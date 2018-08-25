<?php

namespace Ferotres\RedsysBundle\Redsys;

/**
 * Class RedsysResponse
 * @package CoreBiz\Redsys
 */
final class RedsysResponse
{
    /** @var string  */
    private $signature;
    /** @var string  */
    private $params;
    /** @var string   */
    private $order;
    /** @var string  */
    private $currencyCode;
    /** @var string  */
    private $terminal;
    /** @var string  */
    private $amount;
    /** @var string  */
    private $countryCode;
    /** @var string  */
    private $responseCode;
    /** @var string  */
    private $authCode;
    /** @var  string */
    private $cardBrand;
    /** @var  string */
    private $merchantCode;
    /** @var  bool */
    private $ces;
    /** @var  string */
    private $type;
    /** @var  string */
    private $app;

    /**
     * RedsysResponse constructor.
     * @param string $signature
     * @param string $responseParams
     */
    public function __construct( string $signature, string $responseParams, $app = null)
    {
        $this->signature = $signature;
        $this->params    = $responseParams;
        $this->app       = $app;
        $this->extractParams();
    }

    /**
     *
     */
    private function extractParams()
    {
        $jsonData = base64_decode($this->params);
        $data     = json_decode($jsonData, true);

        $this->order        = $data['Ds_Order'];
        $this->amount       = $data['Ds_Amount'] ?? null;
        $this->terminal     = $data['Ds_Terminal'];
        $this->countryCode  = $data['Ds_Card_Country'];
        $this->currencyCode = $data['Ds_Currency'];
        $this->responseCode = $data['Ds_Response'];
        $this->authCode     = $data['Ds_AuthorisationCode'] ?? null;
        $this->cardBrand    = $data['Ds_Card_Brand'] ?? null;
        $this->merchantCode = $data['Ds_MerchantCode'];
        $this->type         = $data['Ds_TransactionType'];
        $this->ces          = $data['Ds_SecurePayment'] ?? false;
    }

    /**
     * @return string
     */
    public function signature(): string
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function params(): string
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function order()
    {
        return $this->order;
    }

    /**
     * @return mixed
     */
    public function currencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @return mixed
     */
    public function terminal()
    {
        return $this->terminal;
    }

    /**
     * @return mixed
     */
    public function amount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function countryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return mixed
     */
    public function responseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return mixed
     */
    public function authCode()
    {
        return $this->authCode;
    }

    /**
     * @return mixed
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * @return string
     */
    public function ces()
    {
        return $this->ces;
    }

}