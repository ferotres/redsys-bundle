<?php
/**
 * Created by PhpStorm.
 * User: antonio
 * Date: 25/08/18
 * Time: 18:08
 */

namespace Ferotres\RedsysBundle\Entity;


class RedsysOrderTrace
{
    /** @var int */
    private $id;
    /** @var string */
    private $orderNumber;
    /** @var string */
    private $shop;
    /** @var int */
    private $currency;
    /** @var integer*/
    private $terminal;
    /** @var float */
    private $amount;
    /** @var boolean */
    private $ces;
    /** @var string */
    private $responseCode;
    /** @var string */
    private $orderType;
    /** @var string */
    private $authCode;
    /** @var string */
    private $token;
    /** @var bool */
    private $validated = 0;
    /** @var \DateTime */
    private $createdAt;
    /** @var \DateTime */
    private $updatedAt;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return string
     */
    public function getShop(): string
    {
        return $this->shop;
    }

    /**
     * @param string $shop
     */
    public function setShop(string $shop): void
    {
        $this->shop = $shop;
    }

    /**
     * @return int
     */
    public function getCurrency(): int
    {
        return $this->currency;
    }

    /**
     * @param int $currency
     */
    public function setCurrency(int $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getTerminal(): int
    {
        return $this->terminal;
    }

    /**
     * @param int $terminal
     */
    public function setTerminal(int $terminal): void
    {
        $this->terminal = $terminal;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return bool
     */
    public function isCes(): bool
    {
        return $this->ces;
    }

    /**
     * @param bool $ces
     */
    public function setCes(bool $ces): void
    {
        $this->ces = $ces;
    }

    /**
     * @return string
     */
    public function getResponseCode(): string
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     */
    public function setResponseCode(string $responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @return string
     */
    public function getOrderType(): string
    {
        return $this->orderType;
    }

    /**
     * @param string $orderType
     */
    public function setOrderType(string $orderType): void
    {
        $this->orderType = $orderType;
    }

    /**
     * @return string
     */
    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     */
    public function setAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->validated;
    }

    /**
     * @param bool $validated
     */
    public function setValidated(bool $validated): void
    {
        $this->validated = $validated;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTime("now");
    }
}