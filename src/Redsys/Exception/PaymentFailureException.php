<?php

namespace Ferotres\RedsysBundle\Redsys\Exception;

/**
 * Class PaymentFailureException
 * @package Ferotres\RedsysBundle\Redsys\Exception
 */
class PaymentFailureException extends \Exception
{
    /**
     * RedsysException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}