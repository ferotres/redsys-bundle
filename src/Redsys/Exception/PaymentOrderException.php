<?php

namespace Ferotres\RedsysBundle\Redsys\Exception;


/**
 * Class PaymentOrderException
 * @package CoreBiz\Redsys\Exception
 */
class PaymentOrderException extends \Exception
{
    /**
     * PaymentOrderException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}