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

namespace ByteArtist\MenuBundle\Test\Unit\Twig;

use ByteArtist\MenuBundle\Factory\MenuFactory;
use ByteArtist\MenuBundle\Interfaces\MenuGeneratorInterface;
use ByteArtist\MenuBundle\Provider\RouteProvider;
use ByteArtist\MenuBundle\Twig\MenuExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * @internal
 */
final class MenuExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('get')
            ->with('_route')
            ->willReturn('route')
        ;

        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $menuFactory = new MenuFactory(
            $this->createMock(TranslatorInterface::class),
            $this->createMock(RouteProvider::class),
            $requestStackMock
        );

        $extension = new MenuExtension($menuFactory, []);

        $result = $extension->getFunctions();

        static::assertIsArray($result);
        static::assertArrayHasKey(0, $result);
        static::assertInstanceOf(TwigFunction::class, $result[0]);
    }

    public function testGenerate(): void
    {
        $generatorMock = $this->createMock(MenuGeneratorInterface::class);
        $generatorMock->method('generate')
            ->willReturn('created')
        ;

        $menuFactoryMock = $this->createMock(MenuFactory::class);
        $menuFactoryMock->method('create')
            ->willReturn($generatorMock)
        ;

        $extension = new MenuExtension($menuFactoryMock, []);
        $environmentMock = $this->createMock(Environment::class);

        static::assertSame('created', $extension->generate($environmentMock));
    }
}
