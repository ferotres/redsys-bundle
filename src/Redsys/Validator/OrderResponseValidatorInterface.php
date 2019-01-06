<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Interface OrderResponseValidatorInterface.
 */
interface OrderResponseValidatorInterface
{
    /**
     * @param RedsysResponse $redsysResponse
     *
     * @return bool
     */
    public function validate(RedsysResponse $redsysResponse): bool;
}
