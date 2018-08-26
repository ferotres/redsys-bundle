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

        $serviceDefinition = $this->getValidatorService($redsysResponse->type());
        /** @var OrderResponseValidator $service */
        $service   = new $serviceDefinition($this->redsysRedirection);
        $validated = $service->validate($redsysResponse);
        return true;
    }

    /**
     * @param $orderType
     * @return mixed
     * @throws \Exception
     */
    private function getValidatorService($orderType)
    {
        $services = [
            'O' => AuthorizationValidator::class,
            '0' => DirectPaymentValidator::class,
            'P' => ConfirmationValidator::class,
            'Q' => CancelAuthorizationValidator::class
        ];
        $service = $services[$orderType] ?? null;
        if (!$service) {
            throw new ResponseValidationException("Validator not exist for this order");
        }
        return $service;
    }

    /**
     * @param RedsysResponse $redsysResponse
     * @throws InvalidResponseSignature
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     */
    private function isValidSignature(RedsysResponse $redsysResponse)
    {
        $valid = $this->redsysRedirection->validatePaymentResponse($redsysResponse);
        if (!$valid) {
            throw new InvalidResponseSignature("The signature of response is diferent that expected!");
        }
    }
}