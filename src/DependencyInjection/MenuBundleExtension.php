<?php

namespace ByteArtist\MenuBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MenuBundleExtension extends Extension
{
    function load(array $configs, ContainerBuilder $containerBuilder)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
        
      // Load the bundle's service declarations 
#      $loader = new YamlFileLoader($containerBuilder, $configDir);
#      $loader->load('services.yaml');      // Apply our config schema to the given app's configs
#      $schema = new ConfigSchema();
#      $options = $this->processConfiguration($schema, $configs);      // Set our own "storageDir" argument with the app's configs
#      $repo = $containerBuilder->getDefinition(DocumentRepository::class);
#      $repo->replaceArgument(0, $options['storageDir']);
    }

    function getAlias()
    {
        return 'menu_bundle';
    }
}