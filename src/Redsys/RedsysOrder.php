<?php

namespace Ferotres\RedsysBundle\Redsys;

use Ferotres\RedsysBundle\Entity\Payment;

/**
 * Class RedsysFormData
 * @package CoreBiz\Redsys
 */
final class RedsysOrder
{
    /** @var string  */
    private $url;
    /** @var string  */
    private $version;
    /** @var string  */
    private $signature;
    /** @var string  */
    private $orderData;
    /** @var  Payment */
    private $payment;

    /**
     * RedsysFormData constructor.
     * @param $url
     * @param $version
     * @param $signature
     * @param $orderData
     */
    public function __construct(string $url, string $version, string $signature, string $orderData)
    {
        $this->url       = $url;
        $this->version   = $version;
        $this->signature = $signature;
        $this->orderData = $orderData;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function version(): string
    {
        return $this->version;
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
    public function orderData(): string
    {
        return $this->orderData;
    }

    /**
     * @return Payment
     */
    public function payment()
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'formData'=>[
                'Ds_SignatureVersion'   =>  $this->version(),
                'Ds_MerchantParameters' => $this->orderData(),
                'Ds_Signature'          => $this->signature(),
            ],
            'url' => $this->url
        ];
    }

    /**
     * @return string
     */
    public function toHtmlForm(): string
    {
        $form  = '<form id="redsys_pay_form" onload="alert(\'hola\');" method="POST" action="'.$this->url().'">';
        $form .= '<input type="hidden" name="Ds_SignatureVersion" value="'.$this->version().'"/>';
        $form .= '<input type="hidden" name="Ds_MerchantParameters" value="'.$this->orderData().'"/>';
        $form .= '<input type="hidden" name="Ds_Signature" value="'.$this->signature().'"/>';
        $form .= '</form>';
        $form .= '<script type="application/x-javascript">window.onload = function() {document.getElementById("redsys_pay_form").submit();}</script>';
        return $form;
    }

}