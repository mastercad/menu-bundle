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

namespace ByteArtist\MenuBundle\Provider;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class RouteProvider
{
    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * RouteProvider CTOR.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Provide possible route, regarding given $route string.
     *
     * @param string|null $route
     *
     * @return string
     */
    public function provide(?string $route): string
    {
        if (null === $route) {
            return '#';
        }

        if (str_contains($route, '/')
            || str_contains($route, '#')
        ) {
            return $route;
        }

        try {
            return $this->router->generate($route);
        } catch (RouteNotFoundException $exception) {
            return $route;
        }
    }
}
