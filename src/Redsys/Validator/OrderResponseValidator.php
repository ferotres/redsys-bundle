<?php

namespace Ferotres\RedsysBundle\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\AuthorizationValidator;
use Ferotres\RedsysBundle\Redsys\ConfirmationValidator;
use Ferotres\RedsysBundle\Redsys\Exception\InvalidResponseSignature;
use Ferotres\RedsysBundle\Redsys\Exception\ResponseValidationException;
use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;

/**
 * Class OrderResponseValidatorResolver
 * @package Ferotres\RedsysBundle\Redsys\Validator
 */
final class OrderResponseValidator
{
    /** @var RedsysRedirection */
    private $redsysRedirection;

    /**
     * OrderResponseValidatorResolver constructor.
     * @param RedsysRedirection $redsysRedirection
     */
    public function __construct(RedsysRedirection $redsysRedirection)
    {
        $this->redsysRedirection = $redsysRedirection;
    }

    /**
     * @param RedsysResponse $redsysResponse
     * @return bool
     * @throws \Exception
     */
    public function validate(RedsysResponse $redsysResponse) :bool
    {
        $this->isValidSignature($redsysResponse);
        $validatorDefinition = $this->getValidatorService($redsysResponse->type());
        /** @var OrderResponseValidatorInterface $validator */
        $validator = new $validatorDefinition;
        return $validator->validate($redsysResponse);
    }

    /**
     * @param $orderType
     * @return mixed
     * @throws \Exception
     */
    private function getValidatorService($orderType)
    {
        $validators = [
            'O' => AuthorizationValidator::class,
            '0' => DirectPaymentValidator::class,
            'P' => ConfirmationValidator::class,
            'Q' => CancelAuthorizationValidator::class
        ];
        $validator = $validators[$orderType] ?? null;
        if (!$validator) {
            throw new ResponseValidationException("Validator not exist for this order");
        }
        return $validator;
    }

    /**
     * @param RedsysResponse $redsysResponse
     * @throws InvalidResponseSignature
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     */
    private function isValidSignature(RedsysResponse $redsysResponse)
    {
        if (!$this->redsysRedirection->validatePaymentResponse($redsysResponse)) {
            throw new InvalidResponseSignature("The signature of response is diferent that expected!");
        }
    }
}