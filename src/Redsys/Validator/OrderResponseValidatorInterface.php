<?php

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;

/**
 * Interface OrderResponseValidatorInterface
 * @package Ferotres\RedsysBundle\Redsys\Validator
 */
interface OrderResponseValidatorInterface
{
    /**
     * @param RedsysResponse $redsysResponse
     * @return bool
     */
    public function validate(RedsysResponse $redsysResponse): bool;
}