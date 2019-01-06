<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Tests\Event;

use Ferotres\RedsysBundle\Event\RedsysResponseSuccessEvent;
use Ferotres\RedsysBundle\Redsys\Helper\RedsysHelper;
use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use PHPUnit\Framework\TestCase;

/**
 * Class RedsysResponseSuccessEventTest.
 */
class RedsysResponseSuccessEventTest extends TestCase
{
    /**
     * @test
     */
    public function whenPropertyNotExistReturnNull()
    {
        $event = new RedsysResponseSuccessEvent($this->createRedsysResponse(), array(), true);

        $this->assertNull($event->getProperty());
    }

    /**
     * @test
     */
    public function whenPropertyExistReturnValue()
    {
        $event = new RedsysResponseSuccessEvent($this->createRedsysResponse(), array('idOrder' => 100), true);
        $this->assertSame(100, $event->getIdOrder());
        $this->assertTrue($event->isValidated());
        $this->assertInstanceOf(RedsysResponse::class, $event->redsysResponse());
    }

    /**
     * @param array $responseData
     *
     * @return RedsysResponse
     */
    private function createRedsysResponse(): RedsysResponse
    {
        $redsysHelper = new RedsysHelper();
        $merchantParameters = $redsysHelper->createMerchantParameters($this->getBasicResponseData());

        return  new RedsysResponse('a123456', $merchantParameters, 'test');
    }

    /**
     * @return array
     */
    private function getBasicResponseData(): array
    {
        return array(
            'Ds_Response' => '0000',
            'Ds_Order' => '123456',
            'Ds_Card_Country' => 910,
            'Ds_Currency' => 840,
            'Ds_MerchantCode' => '123456',
            'Ds_TransactionType' => 'O',
            'Ds_Terminal' => 1,
        );
    }
}
