<?php

namespace Pipirima\PimcoreFtpExportBundle\DependencyInjection;

use Pipirima\PimcoreFtpExportBundle\PimcoreFtpExportBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(PimcoreFtpExportBundle::BUNDLE_CODE);

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $treeBuilder->getRootNode()->ignoreExtraKeys(false);

/*
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('alerts')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('email')->end()
                            ->scalarNode('class')->end()
                            ->arrayNode('fields')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
*/
        return $treeBuilder;
    }
}
