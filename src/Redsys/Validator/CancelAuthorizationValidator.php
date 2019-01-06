<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Class CancelAuthorizationValidator.
 */
final class CancelAuthorizationValidator implements OrderResponseValidatorInterface
{
    /**
     * @param RedsysResponse $redsysResponse
     *
     * @return bool
     */
    public function validate(RedsysResponse $redsysResponse): bool
    {
        $valid = false;
        $responseCode = $redsysResponse->responseCode();
        if ('0400' == $responseCode) {
            $valid = true;
        }

        return $valid;
    }
}
