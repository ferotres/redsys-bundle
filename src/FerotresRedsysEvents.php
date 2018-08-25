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
    const REDSYS_RESPONSE = 'ferotres_redsys.redsys_response';

    /**
     * @Event("Ferotres\RedsysBundle\Event\RedsysResponseEvent")
     */
    const REDSYS_RESPONSE_FAILED = 'ferotres_redsys.redsys_response_failed';
}