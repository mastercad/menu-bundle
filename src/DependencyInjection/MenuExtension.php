<?php

namespace ByteArtist\MenuBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
        
        $configuration = new Configuration($_ENV['APP_DEBUG']);
    
        $config = $this->processConfiguration($configuration, $configs);
        
      // Load the bundle's service declarations 
#      $loader = new YamlFileLoader($containerBuilder, $configDir);
#      $loader->load('services.yaml');      // Apply our config schema to the given app's configs
#      $schema = new ConfigSchema();
#      $options = $this->processConfiguration($schema, $configs);      // Set our own "storageDir" argument with the app's configs
#      $repo = $containerBuilder->getDefinition(DocumentRepository::class);
#      $repo->replaceArgument(0, $options['storageDir']);
    }

#    public function getAlias()
#    {
#        return 'menu_bundle';
#    }
}
