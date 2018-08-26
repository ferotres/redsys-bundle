<?php

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Class ConfirmationValidator
 * @package Ferotres\RedsysBundle\Redsys
 */
final class ConfirmationValidator implements OrderResponseValidatorInterface
{

    public function validate(RedsysResponse $redsysResponse): bool
    {
        $valid = false;
        $responseCode = $redsysResponse->responseCode();
        if ($responseCode == '0900') {
            $valid = true;
        }
        return $valid;
    }
}