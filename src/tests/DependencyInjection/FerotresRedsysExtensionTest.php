<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Tests\DependencyInjection;

use Ferotres\RedsysBundle\DependencyInjection\FerotresRedsysExtension;
use Ferotres\RedsysBundle\Tests\Redsys\Config;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class FerotresRedsysExtensionTest.
 */
class FerotresRedsysExtensionTest extends TestCase
{
    /**
     * @throws \Exception
     * @test
     */
    public function whenUrlIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array(), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenMerchantNameIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['merchant_name']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenMerchantCodeIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['merchant_code']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenSuccessRouteIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['success']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenErrorRouteIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['error']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenTerminalSecretIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['terminals'][0]['secret']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenTerminalCesIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['terminals'][0]['ces']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenTerminalNumIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['terminals'][0]['num']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenTerminalIsoCurrencyIsNotDefinedThrowsException()
    {
        $loader = new FerotresRedsysExtension();

        $config = Config::getConfig();
        unset($config['shops']['test']['terminals'][0]['iso_currency']);
        $this->expectException(InvalidConfigurationException::class);
        $loader->load(array($config, $config), new ContainerBuilder());
    }

    /**
     * @throws \Exception
     * @test
     */
    public function whenConfigIsFilledLoadServiceDefinition()
    {
        $loader = new FerotresRedsysExtension();
        $container = new ContainerBuilder();
        $config = Config::getConfig();

        $loader->load(array($config, $config), $container);

        $definition = $container->getDefinition('ferotres_redsys.redirection');

        $arguments = $definition->getArguments();

        $this->assertSame('ferotres_redsys.url_factory', (string) $arguments[0]);
        $this->assertTrue(is_array($arguments[1]));
    }
}
