<?php
/**
 * Created by PhpStorm.
 * User: antonio-xps
 * Date: 6/01/19
 * Time: 1:47
 */

namespace Ferotres\RedsysBundle\Tests\Redsys;


use Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException;
use Ferotres\RedsysBundle\Redsys\PaymentOrder;
use PHPUnit\Framework\TestCase;

class PaymentOrderTest extends TestCase
{
    /**
     * @throws PaymentOrderException
     * @test
     */
    public function whenAmountIsZeroThenTrowException()
    {
        $this->expectException(PaymentOrderException::class);
        $paymentOrder = PaymentOrder::create(
            'test',
            'EUR',
            'ES',
            '123456',
            0,
            'test',
            'test'
        );
    }

    /**
     * @throws PaymentOrderException
     * @test
     */
    public function whenLocaleNotExistThenThrowException()
    {
        $this->expectException(PaymentOrderException::class);
        $paymentOrder = PaymentOrder::create(
            'test',
            'EUR',
            'TT',
            '123456',
            50,
            'test',
            'test'
        );
    }

    /**
     * @throws PaymentOrderException
     * @test
     */
    public function whenCurrencyeNotExistThenThrowException()
    {
        $this->expectException(PaymentOrderException::class);
        $paymentOrder = PaymentOrder::create(
            'test',
            'TTT',
            'ES',
            '123456',
            50,
            'test',
            'test'
        );
    }
}