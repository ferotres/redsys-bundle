<?php

namespace Ferotres\RedsysBundle\Redsys;

use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;
use Ferotres\RedsysBundle\Redsys\Validator\OrderResponseValidatorInterface;

/**
 * Class AuthorizationValidator
 * @package Ferotres\RedsysBundle\Redsys
 */
final class AuthorizationValidator implements OrderResponseValidatorInterface
{
    /**
     * @var RedsysRedirection
     */
    private $redsysRedirection;

    /**
     * AuthorizationValidator constructor.
     * @param RedsysRedirection $redsysRedirection
     */
    public function __construct(RedsysRedirection $redsysRedirection)
    {
        $this->redsysRedirection = $redsysRedirection;
    }

    /**
     * @param RedsysResponse $redsysResponse
     * @return bool
     */
    public function validate(RedsysResponse $redsysResponse): bool
    {
        $valid = false;
        $responseCode = (int)$redsysResponse->responseCode();
        if ($responseCode  < 99) {
            $valid = true;
        }
        return $valid;
    }
}