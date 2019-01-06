<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Controller;

use Ferotres\RedsysBundle\Event\RedsysResponseFailedEvent;
use Ferotres\RedsysBundle\Event\RedsysResponseSuccessEvent;
use Ferotres\RedsysBundle\FerotresRedsysEvents;
use Ferotres\RedsysBundle\Redsys\Exception\PaymentFailureException;
use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;
use Ferotres\RedsysBundle\Redsys\Validator\OrderResponseValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RedsysController.
 */
final class RedsysController extends AbstractController
{
    /** @var RedsysRedirection */
    private $redsysRedirection;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var OrderResponseValidator */
    private $orderResponseValidator;

    /**
     * RedsysController constructor.
     *
     * @param RedsysRedirection        $redsysRedirection
     * @param EventDispatcherInterface $eventDispatcher
     * @param OrderResponseValidator   $orderResponseValidator
     */
    public function __construct(
        RedsysRedirection $redsysRedirection,
        EventDispatcherInterface $eventDispatcher,
        OrderResponseValidator $orderResponseValidator
    ) {
        $this->redsysRedirection = $redsysRedirection;
        $this->eventDispatcher = $eventDispatcher;
        $this->orderResponseValidator = $orderResponseValidator;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function notification(Request $request)
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->setContent('OK!');
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('max-age', 0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        $redsysResponse = null;
        $validated = false;
        parse_str($request->getQueryString(), $params);

        try {
            $redsysResponse = new RedsysResponse(
                $request->get('Ds_Signature'),
                $request->get('Ds_MerchantParameters'),
                $params['app']
            );
            $validated = $this->orderResponseValidator->validate($redsysResponse);

            if (!$validated) {
                throw new PaymentFailureException('Payment failure!');
            }

            $event = new RedsysResponseSuccessEvent($redsysResponse, $params, $validated);

            $this->eventDispatcher->dispatch(FerotresRedsysEvents::REDSYS_RESPONSE_SUCCESS, $event);
        } catch (\Throwable $exception) {
            if (!$redsysResponse instanceof RedsysResponse) {
                $redsysResponse = null;
            }

            if (!is_array($params)) {
                $params = array();
            }

            $event = new RedsysResponseFailedEvent($redsysResponse, $params, $validated, $exception);

            $this->eventDispatcher->dispatch(FerotresRedsysEvents::REDSYS_RESPONSE_FAILED, $event);

            $response->setStatusCode(500);
            $response->setContent('Internal Server Error');
        }

        return $response;
    }
}
