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

use ByteArtist\MenuBundle\Provider\RouteProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class GeneratorTestAbstract extends KernelTestCase
{
    protected string $generatorTestClass;

    protected function generateGenerator(string $requestRoute)
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);

        $closureTranslator = function ($arg) {
            return 'trans_'.$arg;
        };

        $translatorMock->method('trans')->willReturnCallback($closureTranslator);

        $closureRouteProvider = function ($arg) {
            return 'route_'.$arg;
        };

        $routeProviderMock = $this->createMock(RouteProvider::class);
        $routeProviderMock->method('provide')->willReturnCallback($closureRouteProvider);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('get')
            ->with('_route')
            ->willReturn($requestRoute)
        ;

        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        static::bootKernel();

        return new $this->generatorTestClass($translatorMock, $routeProviderMock, $requestStackMock);
    }
}
