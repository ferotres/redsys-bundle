<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Tests\Redsys\Services;

use Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysException;
use Ferotres\RedsysBundle\Redsys\PaymentOrderBuilder;
use Ferotres\RedsysBundle\Redsys\RedsysOrder;
use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;
use Ferotres\RedsysBundle\Redsys\Services\UrlFactoryInterface;
use Ferotres\RedsysBundle\Tests\Redsys\Config;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

/**
 * Class RedsysRedirectionTest.
 */
class RedsysRedirectionTest extends TestCase
{
    /** @var array */
    private $config;
    /** @var RedsysRedirection */
    private $redsysRedirection;

    protected function setUp()
    {
        $urlFactory = $this->createMock(UrlFactoryInterface::class);
        $client = $this->createMock(Client::class);
        $this->config = Config::getConfig();
        $client->method('request')->willReturn(true);
        $this->redsysRedirection = new RedsysRedirection($urlFactory, $this->config, $client);
    }

    protected function tearDown()
    {
        unset(
            $this->config,
            $this->redsysRedirection
        );
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @test
     */
    public function whenCreateAuthorizationSuccess()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->build();

        $redsysOrder = $this->redsysRedirection->createAuthorization($paymentOrder);

        $this->assertInstanceOf(RedsysOrder::class, $redsysOrder);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @test
     */
    public function whenCreateAuthorizationUsingCesSuccess()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->usingCes(true)
            ->build();

        $redsysOrder = $this->redsysRedirection->createAuthorization($paymentOrder);

        $this->assertInstanceOf(RedsysOrder::class, $redsysOrder);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @test
     */
    public function whenCreatePaymentSuccess()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->build();

        $redsysOrder = $this->redsysRedirection->createPayment($paymentOrder);

        $this->assertInstanceOf(RedsysOrder::class, $redsysOrder);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @test
     */
    public function whenCreatePaymentUsingCesSuccess()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->usingCes(true)
            ->build();

        $redsysOrder = $this->redsysRedirection->createPayment($paymentOrder);

        $this->assertInstanceOf(RedsysOrder::class, $redsysOrder);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @test
     */
    public function whenConfirmAuthorizationSuccess()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->withAuthCode('1234')
            ->build();

        $response = $this->redsysRedirection->confirmAuthorization($paymentOrder);

        $this->asserttrue($response);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @test
     */
    public function whenConfirmAuthorizationWithouAuthCodeThenThrowsException()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->build();

        $this->expectException(RedsysException::class);
        $this->redsysRedirection->confirmAuthorization($paymentOrder);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @test
     */
    public function whenCancelAuthorizationSuccess()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withAuthCode('1234')
            ->build();

        $response = $this->redsysRedirection->cancelAuthorization($paymentOrder);

        $this->asserttrue($response);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @test
     */
    public function whenCancelAuthorizationWithouAuthCodeThenThrowsException()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->build();

        $this->expectException(RedsysException::class);
        $this->redsysRedirection->cancelAuthorization($paymentOrder);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @test
     */
    public function whenTerminalNotExistThrowException()
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('AUD')
            ->build();

        $this->expectException(RedsysException::class);
        $this->redsysRedirection->createAuthorization($paymentOrder);
    }

    /**
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\PaymentOrderException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException
     * @throws \Ferotres\RedsysBundle\Redsys\Exception\RedsysException
     * @test
     */
    public function whenAppNotConfiguredThrowException()
    {
        $urlFactory = $this->createMock(UrlFactoryInterface::class);
        unset($this->config['shops']['test']);
        $redsysRedirection = new RedsysRedirection($urlFactory, $this->config);

        $paymentOrder = PaymentOrderBuilder::create()
            ->withAmount(1000)
            ->toApp('test4')
            ->withOrder('123456')
            ->withPaymentHolder('testman')
            ->withDescription('Test')
            ->withLocale('es')
            ->withCurrency('EUR')
            ->build();

        $this->expectException(RedsysConfigException::class);
        $redsysRedirection->createAuthorization($paymentOrder);
    }
}
