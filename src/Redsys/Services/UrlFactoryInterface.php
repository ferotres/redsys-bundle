<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys\Services;

/**
 * Class UrlFactory.
 */
interface UrlFactoryInterface
{
    public function generateUrl(string $route, array $params = []);
}
