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
            new MenuBundle(),
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

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes->import('@ByteArtist/Menu/Controller/', '/', 'annotation');
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
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
