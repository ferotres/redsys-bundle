<?php

namespace Ferotres\RedsysBundle\Tests\Redsys\Validator;

use Ferotres\RedsysBundle\Redsys\Exception\InvalidResponseSignature;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysException;
use Ferotres\RedsysBundle\Redsys\Exception\ResponseValidationException;
use Ferotres\RedsysBundle\Redsys\Helper\RedsysHelper;
use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;
use Ferotres\RedsysBundle\Redsys\Services\UrlFactoryInterface;
use Ferotres\RedsysBundle\Redsys\Validator\OrderResponseValidator;
use Ferotres\RedsysBundle\Tests\Redsys\Config;
use PHPUnit\Framework\TestCase;

/**
 * Class OrderResponseValidatorTest
 * @package Ferotres\RedsysBundle\Tests\Redsys\Validator
 */
class OrderResponseValidatorTest extends TestCase
{
    /** @var OrderResponseValidator */
    private $orderResponseValidator;
    /** @var RedsysHelper */
    private $redsysHelper;
    /** @var array */
    private $config;

    protected function setUp()
    {
        $urlFactory = $this->createMock(UrlFactoryInterface::class);
        $this->config = Config::getConfig();
        $redsysRedirection = new RedsysRedirection($urlFactory, $this->config );

        $this->orderResponseValidator = new OrderResponseValidator($redsysRedirection);
        $this->redsysHelper = new RedsysHelper();
    }

    protected function tearDown()
    {
        unset(
            $this->config,
            $this->orderResponseValidator,
            $this->redsysHelper
        );
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenAutorizationFailsReturnFalse()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Response'] = '1000';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $validated = $this->orderResponseValidator->validate($redsysResponse);
        $this->assertFalse($validated);
    }



    /**
     * @throws \Exception
     * @test
     */
    public function WhenAutorizationSuccessReturnTrue()
    {
        $redsysResponse = $this->createRedsysResponse($this->getBasicResponseData());
        $validated = $this->orderResponseValidator->validate($redsysResponse);

        $this->assertTrue($validated);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenConfirmationFailsReturnFalse()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Response'] = '4000';
        $responseData['Ds_TransactionType'] = 'P';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $validated = $this->orderResponseValidator->validate($redsysResponse);
        $this->assertFalse($validated);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenConfirmationSuccessReturnTrue()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Response'] = '0900';
        $responseData['Ds_TransactionType'] = 'P';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $validated = $this->orderResponseValidator->validate($redsysResponse);
        $this->assertTrue($validated);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenCancelationFailsReturnFalse()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Response'] = '4000';
        $responseData['Ds_TransactionType'] = 'Q';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $validated = $this->orderResponseValidator->validate($redsysResponse);
        $this->assertFalse($validated);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenCancelationSuccessReturnTrue()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Response'] = '0400';
        $responseData['Ds_TransactionType'] = 'Q';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $validated = $this->orderResponseValidator->validate($redsysResponse);
        $this->assertTrue($validated);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenPaymentFailsReturnFalse()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Response'] = '4000';
        $responseData['Ds_TransactionType'] = '0';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $validated = $this->orderResponseValidator->validate($redsysResponse);
        $this->assertFalse($validated);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenPaymentSuccessReturnTrue()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Response'] = '0000';
        $responseData['Ds_TransactionType'] = '0';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $validated = $this->orderResponseValidator->validate($redsysResponse);
        $this->assertTrue($validated);
    }


    /**
     * @throws \Exception
     * @test
     */
    public function WhenSignatureValidationFailsThenThrowException()
    {
        $responseData = $this->getBasicResponseData();
        $merchantParameters = $this->redsysHelper->createMerchantParameters($responseData);

        $redsysResponse = new RedsysResponse('abcsddsdssdsdsdsds', $merchantParameters, 'test' );

        $this->expectException(InvalidResponseSignature::class);
        $this->orderResponseValidator->validate($redsysResponse);

    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenTerminalNotExistThenThrowException()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_Terminal'] = 8;
        $redsysResponse = $this->createRedsysResponse($responseData);

        $this->expectException(RedsysException::class);
        $this->orderResponseValidator->validate($redsysResponse);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function WhenPaymentTypeNotExistThenThrowException()
    {
        $responseData = $this->getBasicResponseData();
        $responseData['Ds_TransactionType'] = 'L';
        $redsysResponse = $this->createRedsysResponse($responseData);

        $this->expectException(ResponseValidationException::class);
        $this->orderResponseValidator->validate($redsysResponse);
    }


    /**
     * @param array $responseData
     * @return RedsysResponse
     */
    private function createRedsysResponse(array $responseData): RedsysResponse
    {
        $terminal = $this->config['shops']['test']['terminals'][0];

        $merchantParameters = $this->redsysHelper->createMerchantParameters($responseData);
        $signature = $this->redsysHelper->createSignature($terminal['secret'], $merchantParameters, $responseData['Ds_Order']);
        $signature = strtr($signature, '+/', '-_');
        return  new RedsysResponse($signature, $merchantParameters, 'test' );
    }

    /**
     * @return array
     */
    private function getBasicResponseData() :array
    {
        return [
            'Ds_Response' => '0000',
            'Ds_Order' => '123456',
            'Ds_Card_Country' => 910,
            'Ds_Currency' => 840,
            'Ds_MerchantCode' => '123456',
            'Ds_TransactionType' => 'O',
            'Ds_Terminal' => 1,
        ];
    }
}