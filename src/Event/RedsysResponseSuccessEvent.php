<?php

namespace Ferotres\RedsysBundle\Event;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RedsysResponseEvent
 * @package Ferotres\RedsysBundle\Event
 */
final class RedsysResponseSuccessEvent extends Event
{
    /** @var RedsysResponse */
    private $redsysResponse;
    /**  @var array */
    private $params;
    /**  @var bool */
    private $validated;

    /**
     * RedsysResponseEvent constructor.
     * @param RedsysResponse $redsysResponse
     * @param array $params
     * @param bool $validated
     */
    public function __construct(RedsysResponse $redsysResponse, array $params = [], bool $validated = false)
    {
        $this->redsysResponse = $redsysResponse;
        $this->params         = $params;
        $this->validated      = $validated;
    }

    /**
     * @param $method
     * @return mixed|null
     */
    public function __call($method, $properties)
    {
        $property = lcfirst(str_replace('get', '', $method));
        return $this->params[$property] ?? null;
    }

    /**
     * @return RedsysResponse
     */
    public function redsysResponse():RedsysResponse
    {
        return $this->redsysResponse;
    }

    public function isValidated()
    {
        return $this->validated;
    }
}