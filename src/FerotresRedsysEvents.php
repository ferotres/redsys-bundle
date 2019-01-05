<?php

namespace Ferotres\RedsysBundle;

/**
 * Class FerotresRedsysEvents
 * @package Ferotres\RedsysBundle
 */
final class FerotresRedsysEvents
{
    /**
     * @Event("Ferotres\RedsysBundle\Event\RedsysResponseEvent")
     */
    const REDSYS_RESPONSE_SUCCESS = 'ferotres_redsys.redsys_response_success';

    /**
     * @Event("Ferotres\RedsysBundle\Event\RedsysResponseEvent")
     */
    const REDSYS_RESPONSE_FAILED = 'ferotres_redsys.redsys_response_failed';
}