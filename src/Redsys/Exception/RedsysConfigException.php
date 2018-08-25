<?php

namespace Ferotres\RedsysBundle\Redsys\Exception;

/**
 * Class RedsysConfigException
 * @package CoreBiz\Redsys\Exception
 */
class RedsysConfigException extends \Exception
{
    /**
     * RedsysConfigException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}