<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ferotres_redsys');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('url')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('shops')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('merchant_name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('merchant_code')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('success')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('error')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('terminals')
                                ->performNoDeepMerging()
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('secret')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->integerNode('num')
                                            ->isRequired()
                                        ->end()
                                        ->booleanNode('ces')
                                            ->isRequired()
                                        ->end()
                                        ->scalarNode('iso_currency')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                    ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
