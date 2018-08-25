<?php

namespace Ferotres\RedsysBundle\Controller;
use Ferotres\RedsysBundle\Entity\RedsysOrderTrace;
use Ferotres\RedsysBundle\FerotresRedsysEvents;
use Ferotres\RedsysBundle\Event\RedsysResponseEvent;
use Ferotres\RedsysBundle\Event\RedsysResponseFailedEvent;
use Ferotres\RedsysBundle\Redsys\RedsysResponse;
use Ferotres\RedsysBundle\Redsys\Services\RedsysRedirection;
use Ferotres\RedsysBundle\Repository\RedsysOrderTraceRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RedsysController
 * @package Ferotres\RedsysBundle\Controller
 */
final class RedsysController extends AbstractController
{
    /** @var RedsysRedirection */
    private $redsysRedirection;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var RedsysOrderTraceRepositoryInterface */
    private $redsysOrderTraceRepository;

    /**
     * RedsysController constructor.
     * @param RedsysRedirection $redsysRedirection
     * @param EventDispatcherInterface $eventDispatcher
     * @param RedsysOrderTraceRepositoryInterface $redsysOrderTraceRepository
     */
    public function __construct(
        RedsysRedirection $redsysRedirection,
        EventDispatcherInterface $eventDispatcher,
        RedsysOrderTraceRepositoryInterface $redsysOrderTraceRepository
    ) {
        $this->redsysRedirection          = $redsysRedirection;
        $this->eventDispatcher            = $eventDispatcher;
        $this->redsysOrderTraceRepository = $redsysOrderTraceRepository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function notification(Request $request)
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->setContent("OK!");
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('max-age', 0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        $redsysResponse = null;
        $validated      = false;
        parse_str($request->getQueryString(), $params);

        try {
            $redsysResponse = new RedsysResponse(
                $request->get("Ds_Signature"),
                $request->get("Ds_MerchantParameters"),
                $params['app']
            );
            $validated = $this->redsysRedirection->validatePaymentResponse($redsysResponse);

            $event = new RedsysResponseEvent($redsysResponse, $params, $validated);

            /** @var RedsysOrderTrace $trace */
            $trace = $this->redsysOrderTraceRepository->find($params['trace']);

            $trace->setResponseCode($redsysResponse->responseCode());
            $trace->setAuthCode($redsysResponse->authCode());
            $trace->setValidated($validated);

            $this->redsysOrderTraceRepository->save($trace);

            $this->eventDispatcher->dispatch(FerotresRedsysEvents::REDSYS_RESPONSE, $event);


        } catch (\Throwable $exception) {

            if(!$redsysResponse instanceof RedsysResponse) {
                $redsysResponse = null;
            }

            if(!is_array($params)) {
                $params = [];
            }

            $event = new RedsysResponseFailedEvent($redsysResponse, $params, $validated, $exception);

            $this->eventDispatcher->dispatch(FerotresRedsysEvents::REDSYS_RESPONSE_FAILED, $event);

            $response->setStatusCode(500);
            $response->setContent("Internal Server Error");
        }

        return $response;
    }
}