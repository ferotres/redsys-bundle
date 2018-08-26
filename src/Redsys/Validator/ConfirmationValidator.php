<?php

namespace Ferotres\RedsysBundle\Redsys;


use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;
use Ferotres\RedsysBundle\Redsys\Validator\OrderResponseValidator;

/**
 * Class ConfirmationValidator
 * @package Ferotres\RedsysBundle\Redsys
 */
final class ConfirmationValidator implements OrderResponseValidatorInterface
{
    /**
     * @var RedsysRedirection
     */
    private $redsysRedirection;

    public function __construct(RedsysRedirection $redsysRedirection)
    {
        $this->redsysRedirection = $redsysRedirection;
    }

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