<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Redsys\Services;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UrlFactory.
 */
final class UrlFactory implements UrlFactoryInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generateUrl(string $route, array $params = [])
    {
        return $this->urlGenerator->generate($route, $params, 0);
    }
}
