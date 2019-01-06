<?php

namespace Ferotres\RedsysBundle\Redsys\Exception;

/**
 * Class RedsysCallbackException
 * @package CoreBiz\Redsys\Exception
 */
class RedsysCallbackException extends \Exception
{
    /**
     * RedsysCallbackException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}