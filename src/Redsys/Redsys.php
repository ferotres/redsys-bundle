<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys;

use Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysException;
use Ferotres\RedsysBundle\Redsys\Helper\RedsysHelper;
use Ferotres\RedsysBundle\Redsys\Services\UrlFactory;
use Ferotres\RedsysBundle\Redsys\Services\UrlFactoryInterface;
use GuzzleHttp\Client;

/**
 * Class Redsys.
 */
abstract class Redsys
{
    const VERSION = 'HMAC_SHA256_V1';

    /** @var RedsysHelper */
    protected $redsysHelper;
    /** @var UrlFactory */
    protected $urlFactory;

    /** @var array */
    protected $apps;
    /** @var string */
    protected $url;
    /** @var string */
    protected $successUrl;
    /** @var string */
    protected $errorUrl;
    /** @var string */
    protected $notification;
    /** @var array */
    protected $terminal;
    /** @var Client */
    protected $client;

    /**
     * Redsys constructor.
     *
     * @param UrlFactoryInterface $urlFactory
     * @param array               $config
     */
    public function __construct(
        UrlFactoryInterface $urlFactory,
        array $config = [],
        ?Client $client = null
    ) {
        $this->redsysHelper = new RedsysHelper();
        $this->urlFactory = $urlFactory;
        $this->url = $config['url'];
        $this->apps = $config['shops'];
        $this->client = $client;
        if (!$client) {
            $this->client = new Client();
        }
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return mixed
     */
    abstract public function createAuthorization(PaymentOrder $paymentOrder);

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return mixed
     */
    abstract public function confirmAuthorization(PaymentOrder $paymentOrder);

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return mixed
     */
    abstract public function cancelAuthorization(PaymentOrder $paymentOrder);

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return mixed
     */
    abstract public function createPayment(PaymentOrder $paymentOrder);

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @return array
     *
     * @throws RedsysCallbackException
     * @throws RedsysConfigException
     * @throws RedsysException
     */
    protected function getBasicRedsysData(PaymentOrder $paymentOrder): array
    {
        $app = $this->getApp($paymentOrder->app());
        $this->terminal = $this->findTerminal($paymentOrder->currency(), $paymentOrder->ces(), $app['terminals']);
        $currency = $this->getCorrespondenceIdCurrency($this->terminal['iso_currency']);

        $this->setOrderUrl($app, $paymentOrder);

        return [
            'DS_MERCHANT_AMOUNT' => (string) $paymentOrder->amount(),
            'DS_MERCHANT_CURRENCY' => $currency,
            'DS_MERCHANT_ORDER' => $paymentOrder->order(),
            'DS_MERCHANT_TITULAR' => $paymentOrder->paymentHolder(),
            'DS_MERCHANT_MERCHANTCODE' => $app['merchant_code'],
            'DS_MERCHANT_NAME' => $app['merchant_name'],
            'DS_MERCHANT_TERMINAL' => $this->terminal['num'],
            'DS_MERCHANT_TRANSACTION_TYPE' => $paymentOrder->type(),
            'DS_MERCHANT_PRODUCTDESCRIPTION' => $paymentOrder->description(),
        ];
    }

    /**
     * @param null $appName
     *
     * @return array
     *
     * @throws RedsysConfigException
     */
    protected function getApp($appName = null): array
    {
        $app = [];
        $appsCount = count($this->apps);
        if ($appsCount > 1 && $appName) {
            $app = $this->apps[$appName];
        } elseif ($appsCount > 0) {
            $appNames = array_keys($this->apps);
            $app = $this->apps[$appNames[0]];
        } else {
            throw new RedsysConfigException('Redsys App config is needed!');
        }

        return $app;
    }

    /**
     * @param string $currency
     * @param bool   $ces
     * @param array  $terminals
     *
     * @return array
     *
     * @throws RedsysException
     */
    protected function findTerminal(string $currency, bool $ces, array $terminals): array
    {
        $terminalsFound = array_filter($terminals, function ($terminal) use ($currency, $ces) {
            if ($terminal['iso_currency'] == $currency && $terminal['ces'] == $ces) {
                return true;
            }
        });

        if (0 == count($terminalsFound)) {
            throw new RedsysException('The terminal with currency: '.$currency.' was not found!');
        }

        return array_shift($terminalsFound);
    }

    /**
     * @param int   $terminalNumber
     * @param array $terminals
     *
     * @return array
     *
     * @throws RedsysException
     */
    protected function findTerminalByNumber(int $terminalNumber, array $terminals): array
    {
        $terminalsFound = array_filter($terminals, function ($terminal) use ($terminalNumber) {
            if ($terminal['num'] == $terminalNumber) {
                return true;
            }
        });

        if (0 == count($terminalsFound)) {
            throw new RedsysException('The terminal with number: '.$terminalNumber.' was not found!');
        }

        return array_shift($terminalsFound);
    }

    /**
     * @param string $isoCurrency
     *
     * @return string|null
     */
    protected function getCorrespondenceIdCurrency(string $isoCurrency): ?string
    {
        $currencies = [
            'EUR' => '978', // Euros
            'USD' => '840', // Dolares
            'AUD' => '036', // Dolares Australianos
            'GBP' => '826', // Libras
            'JPY' => '392', // Yen
            'ARS' => '032', // Peso Argentino
            'CAD' => '124', // Dolar Canadiense
            'INR' => '356', // Rupia
            'MXN' => '484', // Peso Mexicano
            'PEN' => '604', // Sol Peruano
            'CHF' => '756', // Franco suizo
            'BRL' => '986', // Real Brasileño
            'VEF' => '937', // Bolivar Venezolano
            'TRY' => '949', // Lira Turca
        ];

        return $currencies[$isoCurrency] ?? null;
    }

    /**
     * @param string $locale
     *
     * @return int|null
     */
    protected function getCorrespondenceLanguage(string $locale): ?int
    {
        $locales = [
            'ES' => 1,  // Castellano
            'EN' => 2,  // Ingles
            'CA' => 3,  // Catalán
            'FR' => 4,  // Frances
            'DE' => 5,  // Aleman
            'NL' => 6,  // Holandes
            'IT' => 7,  // Italiano
            'SV' => 8,  // Sueco
            'PT' => 9,  // Portugues
            'PL' => 11, // Polaco
            'GL' => 12, // Gallego
            'EU' => 13, // Euskera
        ];

        return $locales[$locale] ?? null;
    }

    /**
     * @param $app
     * @param PaymentOrder $paymentOrder
     *
     * @throws RedsysCallbackException
     */
    protected function setOrderUrl($app, PaymentOrder $paymentOrder)
    {
        try {
            $this->successUrl = $this->urlFactory->generateUrl($app['success'], $paymentOrder->routeParams());
            $this->errorUrl = $this->urlFactory->generateUrl($app['error'], $paymentOrder->routeParams());
            $this->notification = $this->urlFactory->generateUrl(
                'ferotres_redsys_notification_url',
                array_merge($paymentOrder->routeParams(), ['app' => $paymentOrder->app()])
            );
        } catch (\Throwable $exception) {
            throw new RedsysCallbackException($exception->getMessage());
        }
    }
}
