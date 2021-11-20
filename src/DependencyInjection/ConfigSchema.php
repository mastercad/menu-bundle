<?php

namespace ByteArtist\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigSchema implements ConfigurationInterface
{    
    public function getConfigTreeBuilder()    
    {      
        $treeBuilder = new TreeBuilder('menu_bundle');
        
        $rootNode = $treeBuilder->getRootNode();
        
        $treeBuilder->getRootNode()
            ->children()
            ->end();
          
        return $treeBuilder;
    }
}
