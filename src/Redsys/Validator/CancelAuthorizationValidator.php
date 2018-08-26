<?php

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Class CancelAuthorizationValidator
 * @package Ferotres\RedsysBundle\Redsys\Validator
 */
final class CancelAuthorizationValidator implements OrderResponseValidatorInterface
{

    /**
     * @param RedsysResponse $redsysResponse
     * @return bool
     */
    public function validate(RedsysResponse $redsysResponse): bool
    {
        $valid = false;
        $responseCode = $redsysResponse->responseCode();
        if ($responseCode == '0400') {
            $valid = true;
        }
        return $valid;
    }
}