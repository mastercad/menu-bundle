<?php

namespace ByteArtist\MenuBundle\Test\Integration;

use ByteArtist\MenuBundle\MenuBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return array
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new MenuBundle()
        ];
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir().'/MenuBundle/cache'.$this->environment;
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir().'/MenuBundle/logs';
    }

    /**
     * @param RouteCollectionBuilder $routes
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->import('@ByteArtist/Menu/Controller/', '/', 'annotation');
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @param LoaderInterface  $loader
     */
    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => 'my$ecret',
            'test' => null,
//            'templating' => false,
            'assets' => false,
            'profiler' => [
                'collect' => false,
            ],
        ]);

        $containerBuilder->loadFromExtension(
            'twig',
            ['debug' => '%kernel.debug%', 'strict_variables' => '%kernel.debug%']
        );
    }
}
