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

namespace ByteArtist\MenuBundle\Test\Unit\Provider;

use ByteArtist\MenuBundle\Provider\RouteProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 */
final class RouteProviderTest extends TestCase
{
    /**
     * @param mixed  $route
     * @param string $expectation
     *
     * @dataProvider returnValueIfNoValidRouteStringProvider
     */
    public function testReturnValueIfNoValidRouteString($route, $expectation): void
    {
        $routeProvider = new RouteProvider($this->createMock(RouterInterface::class));

        static::assertSame($expectation, $routeProvider->provide($route));
    }

    public function returnValueIfNoValidRouteStringProvider()
    {
        yield 'return # as default if null given' => [
            'route' => null,
            'expectation' => '#',
        ];

        yield 'return same if uri' => [
            'route' => '/this/is/test/uri',
            'expectation' => '/this/is/test/uri',
        ];

        yield 'return same if / is present' => [
            'route' => 'test/this',
            'expectation' => 'test/this',
        ];

        yield 'return same if # is present' => [
            'route' => 'this#is#test',
            'expectation' => 'this#is#test',
        ];

        yield 'return same if route is #' => [
            'route' => '#',
            'expectation' => '#',
        ];
    }

    public function testReturnRouteString(): void
    {
        $closureRouteProvider = function ($arg) {
            return 'route_'.$arg;
        };
        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->method('generate')
            ->willReturnCallback($closureRouteProvider)
        ;

        $routeProvider = new RouteProvider($routerMock);

        static::assertSame('route_test_route', $routeProvider->provide('test_route'));
    }

    public function testNotExistingRouteWillReturnRoute(): void
    {
        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->method('generate')
            ->willThrowException(new RouteNotFoundException())
        ;

        $routeProvider = new RouteProvider($routerMock);
        static::assertSame('route_not_found', $routeProvider->provide('route_not_found'));
    }
}
