<?php

namespace Ferotres\RedsysBundle\Redsys\Exception;

/**
 * Class RedsysException
 * @package CoreBiz\Redsys\Exception
 */
class RedsysException extends \Exception
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