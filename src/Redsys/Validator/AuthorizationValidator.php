<?php

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;


/**
 * Class AuthorizationValidator
 * @package Ferotres\RedsysBundle\Redsys
 */
final class AuthorizationValidator implements OrderResponseValidatorInterface
{

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