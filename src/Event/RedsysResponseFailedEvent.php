<?php

namespace Ferotres\RedsysBundle\Event;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RedsysResponseFailedEvent
 * @package Ferotres\RedsysBundle\Event
 */
final class RedsysResponseFailedEvent  extends Event
{
    /**  @var array */
    private $params;
    /** @var RedsysResponse */
    private $redsysResponse;
    /**  @var bool */
    private $validated;
    /** @var \Throwable */
    private $exception;

    /**
     * RedsysResponseFailedEvent constructor.
     * @param RedsysResponse|null $redsysResponse
     * @param array $params
     * @param bool $validated
     * @param \Throwable|null $exception
     */
    public function __construct(
        ?RedsysResponse $redsysResponse,
        array $params         = [],
        bool $validated       = false,
        \Throwable $exception = null
    ) {
        $this->redsysResponse = $redsysResponse;
        $this->params         = $params;
        $this->validated      = $validated;
        $this->exception      = $exception;
    }

    /**
     * @param $method
     * @param $properties
     * @return mixed|null
     */
    public function __call($method, $properties)
    {
        $property = lcfirst(str_replace('get', '', $method));
        return $this->params[$property] ?? null;
    }

    /**
     * @return bool
     */
    public function isValidated():bool
    {
        return $this->validated;
    }

    /**
     * @return \Throwable
     */
    public function exception()
    {
        return $this->exception;
    }
}