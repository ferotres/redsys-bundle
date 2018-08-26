<?php
/**
 * Created by PhpStorm.
 * User: antonio
 * Date: 26/08/18
 * Time: 21:09
 */

namespace Ferotres\RedsysBundle\Redsys\Exception;


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