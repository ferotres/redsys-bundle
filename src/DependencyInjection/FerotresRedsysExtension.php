<?php

namespace Ferotres\RedsysBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/**
 * Class FerotresRedsysExtension
 * @package Ferotres\RedsysBundle\DependencyInjection
 */
class FerotresRedsysExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('ferotres_redsys.redirection');
        $definition->setArgument(0, $config);
        $definition->setArgument(1, new Reference('ferotres_redsys.url_factory'));
        $definition->setArgument(2, new Reference('ferotres_redsys.redsys_order_trace_repository'));
    }
}