<?php
/**
 * Created by PhpStorm.
 * User: antonio
 * Date: 26/08/18
 * Time: 17:30
 */

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Class DirectPaymentValidator
 * @package Ferotres\RedsysBundle\Redsys\Validator
 */
class DirectPaymentValidator implements OrderResponseValidatorInterface
{
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