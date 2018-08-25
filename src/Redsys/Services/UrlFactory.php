<?php
/**
 * Created by PhpStorm.
 * User: antonio
 * Date: 25/08/18
 * Time: 8:58
 */

namespace Ferotres\RedsysBundle\Redsys\Services;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UrlFactory
 * @package Ferotres\RedsysBundle\Redsys\Services
 */
final class UrlFactory
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