<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys;

/**
 * Class RedsysFormData.
 */
final class RedsysOrder
{
    /** @var string */
    private $url;
    /** @var string */
    private $version;
    /** @var string */
    private $signature;
    /** @var string */
    private $orderData;

    /**
     * RedsysFormData constructor.
     *
     * @param $url
     * @param $version
     * @param $signature
     * @param $orderData
     */
    public function __construct(string $url, string $version, string $signature, string $orderData)
    {
        $this->url = $url;
        $this->version = $version;
        $this->signature = $signature;
        $this->orderData = $orderData;
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
     * @return array
     */
    public function toArray(): array
    {
        return array(
            'formData' => array(
                'Ds_SignatureVersion' => $this->version(),
                'Ds_MerchantParameters' => $this->orderData(),
                'Ds_Signature' => $this->signature(),
            ),
            'url' => $this->url,
        );
    }
}
