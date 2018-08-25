<?php

namespace Ferotres\RedsysBundle\Redsys;

use Ferotres\RedsysBundle\Redsys\Exception\RedsysCallbackException;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysConfigException;
use Ferotres\RedsysBundle\Redsys\Exception\RedsysException;
use Ferotres\RedsysBundle\Redsys\Helper\RedsysHelper;
use Ferotres\RedsysBundle\Redsys\Services\UrlFactory;
use Ferotres\RedsysBundle\Repository\RedsysOrderTraceRepositoryInterface;

/**
 * Class Redsys
 * @package CoreBiz\Redsys
 */
abstract class Redsys
{
    const VERSION = 'HMAC_SHA256_V1';

    /** @var RedsysHelper  */
    protected $redsysHelper;
    /**  @var UrlFactory */
    protected $urlFactory;

    /** @var  array */
    protected $apps;
    /** @var  string */
    protected $url;
    /** @var string */
    protected $successUrl;
    /** @var string */
    protected $errorUrl;
    /** @var string */
    protected $notification;
    /** @var array */
    protected $terminal;
    /** @var RedsysOrderTraceRepositoryInterface */
    protected $redsysOrderTraceRepository;

    /**
     * Redsys constructor.
     * @param array $config
     * @param UrlFactory $urlFactory
     * @param RedsysOrderTraceRepositoryInterface $redsysOrderTraceRepository
     */
    public function __construct(
        array $config = [],
        UrlFactory $urlFactory,
        RedsysOrderTraceRepositoryInterface $redsysOrderTraceRepository
    ) {
        $this->redsysHelper               = new RedsysHelper();
        $this->urlFactory                 = $urlFactory;
        $this->url                        = $config['url'];
        $this->apps                       = $config['shops'];
        $this->redsysOrderTraceRepository = $redsysOrderTraceRepository;
    }

    /**
     * @param PaymentOrder $paymentOrder
     * @return mixed
     */
    abstract function createAuthorization(PaymentOrder $paymentOrder);

    /**
     * @param PaymentOrder $paymentOrder
     * @return mixed
     */
    abstract function confirmAuthorization(PaymentOrder $paymentOrder);

    /**
     * @param PaymentOrder $paymentOrder
     * @return mixed
     */
    abstract function cancelAuthorization(PaymentOrder $paymentOrder);

    /**
     * @param PaymentOrder $paymentOrder
     * @return mixed
     */
    abstract function createPayment(PaymentOrder $paymentOrder);


    /**
     * @param PaymentOrder $paymentOrder
     * @return array
     * @throws RedsysCallbackException
     * @throws RedsysConfigException
     * @throws RedsysException
     */
    protected function getBasicRedsysData(PaymentOrder $paymentOrder):array
    {
        $app                = $this->getApp($paymentOrder->app());
        $this->terminal     = $this->findTerminal($paymentOrder->currency(), $paymentOrder->ces(), $app['terminals']);
        $currency           = $this->getCorrespondenceIdCurrency($this->terminal['iso_currency']);

        $this->setOrderUrl($app, $paymentOrder);

        return [
            'DS_MERCHANT_AMOUNT'             => (string)$paymentOrder->amount(),
            'DS_MERCHANT_CURRENCY'           => $currency,
            'DS_MERCHANT_ORDER'              => $paymentOrder->order(),
            'DS_MERCHANT_TITULAR'            => $paymentOrder->titular(),
            'DS_MERCHANT_MERCHANTCODE'       => $app['merchant_code'],
            'DS_MERCHANT_NAME'               => $app['merchant_name'],
            'DS_MERCHANT_TERMINAL'           => $this->terminal['num'],
            'DS_MERCHANT_TRANSACTION_TYPE'   => $paymentOrder->type(),
            'DS_MERCHANT_PRODUCTDESCRIPTION' => $paymentOrder->description(),
        ];
    }

    /**
     * @param null $appName
     * @return array
     * @throws RedsysConfigException
     */
    protected function getApp($appName = null) :array
    {
        $app = [];
        $appsCount = count($this->apps);
        if ($appsCount > 1 && $appName) {
            $app = $this->apps[$appName];
        } elseif($appsCount > 0) {
            $appNames = array_keys($this->apps);
            $app      = $this->apps[$appNames[0]];
        } else {
            throw New RedsysConfigException("Redsys App config is needed!");
        }
        return $app;
    }

    /**
     * @param string $currency
     * @param bool $ces
     * @param array $terminals
     * @return array
     * @throws RedsysException
     */
    protected function findTerminal(string $currency, bool $ces, array $terminals) :array
    {
        $terminalsFound = array_filter($terminals, function ($terminal) use ($currency, $ces){
            if ($terminal['iso_currency'] == $currency && $terminal['ces'] == $ces) {
                return true;
            }
        });

        if (count($terminalsFound) == 0) {
            throw new RedsysException("The terminal with currency: ".$currency." was not found!");
        }
        return array_shift($terminalsFound);
    }

    /**
     * @param int $terminalNumber
     * @param array $terminals
     * @return array
     * @throws RedsysException
     */
    protected function findTerminalByNumber(int $terminalNumber, array $terminals) :array
    {
        $terminalsFound = array_filter($terminals, function ($terminal) use ($terminalNumber){
            if ($terminal['num'] == $terminalNumber) {
                return true;
            }
        });

        if (count($terminalsFound) == 0) {
            throw new RedsysException("The terminal with number: ".$terminalNumber." was not found!");
        }
        return array_shift($terminalsFound);
    }

    /**
     * @param string $isoCurrency
     * @return null|string
     */
    protected function getCorrespondenceIdCurrency(string $isoCurrency) :?string
    {
        $currencies = [
            'EUR' => '978',
            'USD' => '840',
            'AUD' => '036',
            'GBP' => '826'
        ];
        return $currencies[$isoCurrency] ?? null;
    }

    /**
     * @param string $locale
     * @return int|null
     */
    protected function getCorrespondenceLanguage(string $locale):?int
    {
        $locales = [
            'ES' => 1,
            'EN' => 2,
            'FR' => 4,
            'DE' => 5,
            'IT' => 7,
            'NL' => 6,
            'EL' => 2,
            'RU' => 2,
        ];
        return $locales[$locale] ?? null;
    }

    /**
     * @param $app
     * @param PaymentOrder $paymentOrder
     * @throws RedsysCallbackException
     */
    protected function setOrderUrl($app, PaymentOrder $paymentOrder)
    {
        try {
            $this->successUrl   = $this->urlFactory->generateUrl($app['success'], $paymentOrder->routeParams());
            $this->errorUrl     = $this->urlFactory->generateUrl($app['error'], $paymentOrder->routeParams());

            $this->notification = $this->urlFactory->generateUrl(
                'ferotres_redsys_notification_url',
                array_merge($paymentOrder->routeParams(), ['app' => $paymentOrder->app()])
            );
        } catch (\Throwable $exception) {
            throw new RedsysCallbackException($exception->getMessage());
        }

    }

}