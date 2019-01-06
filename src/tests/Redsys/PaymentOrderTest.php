<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
