<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle;

/**
 * Class FerotresRedsysEvents.
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
