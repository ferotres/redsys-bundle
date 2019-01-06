<?php

namespace Ferotres\RedsysBundle\Redsys\Services;


/**
 * Class UrlFactory
 * @package Ferotres\RedsysBundle\Redsys\Services
 */
interface UrlFactoryInterface
{
    public function generateUrl(string $route, array $params = []);
}