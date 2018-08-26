<?php

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;

/**
 * Class CancelAuthorizationValidator
 * @package Ferotres\RedsysBundle\Redsys\Validator
 */
final class CancelAuthorizationValidator implements OrderResponseValidatorInterface
{
    /**
     * @var RedsysRedirection
     */
    private $redsysRedirection;

    /**
     * CancelAuthorizationValidator constructor.
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
        $responseCode = $redsysResponse->responseCode();
        if ($responseCode == '0400') {
            $valid = true;
        }
        return $valid;
    }
}