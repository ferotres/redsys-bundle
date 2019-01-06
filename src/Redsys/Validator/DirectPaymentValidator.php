<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Class DirectPaymentValidator.
 */
class DirectPaymentValidator implements OrderResponseValidatorInterface
{
    public function validate(RedsysResponse $redsysResponse): bool
    {
        $valid = false;
        $responseCode = (int) $redsysResponse->responseCode();
        if ($responseCode < 99) {
            $valid = true;
        }

        return $valid;
    }
}
