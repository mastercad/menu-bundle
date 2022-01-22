<?php

declare(strict_types=1);

/*
 * This file is part of Menu Bundle.
 *
 * © Andreas Kempe <andreas.kempe@byte-artist.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ByteArtist\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class for menu bundle.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Provides the config tree definition.
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('menu');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('type')->defaultValue('default')->end()
            ->scalarNode('brand_name')->end()
            ->scalarNode('use_orig_css')->defaultValue(true)->end()
            ->scalarNode('use_orig_js')->defaultValue(true)->end()
            ->arrayNode('pages')->defaultValue([])
            ->arrayPrototype()
            ->children()
            ->scalarNode('path')->defaultValue('#')->end()
            ->arrayNode('pages')
            ->arrayPrototype()
            ->children()
            ->scalarNode('path')->defaultValue('#')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
