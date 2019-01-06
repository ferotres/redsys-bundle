<?php

namespace Ferotres\RedsysBundle\Tests\Redsys;

use Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException;
use Ferotres\RedsysBundle\Redsys\PaymentOrder;
use Ferotres\RedsysBundle\Redsys\PaymentOrderBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class PaymentOrderBuilderTest
 * @package Ferotres\RedsysBundle\Tests\Redsys
 */
class PaymentOrderBuilderTest extends TestCase
{

    /**
     * @throws PaymentOrderException
     * @test
     */
    public function whenAmountIsNullThenThrowException()
    {

        $this->expectException(PaymentOrderException::class);
        PaymentOrderBuilder::create()
        ->toApp('APP1')
        ->withOrder(1234567)
        ->build();
    }

    /**
     * @throws PaymentOrderException
     * @test
     */
    public function whenAppIsNullThenThrowException()
    {

        $this->expectException(PaymentOrderException::class);
        PaymentOrderBuilder::create()
            ->withOrder(1234567)
            ->withAmount(1000)
            ->build();
    }

    /**
     * @throws PaymentOrderException
     * @test
     */
    public function whenOrderIsNullThenThrowException()
    {

        $this->expectException(PaymentOrderException::class);
        PaymentOrderBuilder::create()
            ->toApp('APP1')
            ->withAmount(1000)
            ->build();
    }

    /**
     * @throws PaymentOrderException
     * @test
     */
    public function whenOrderIsFilledThenReturnPayment()
    {
        $payment = PaymentOrderBuilder::create()
            ->toApp('APP1')
            ->withOrder(1234567)
            ->withAmount(1000)
            ->build();

        $this->assertInstanceOf(PaymentOrder::class, $payment);
    }
}