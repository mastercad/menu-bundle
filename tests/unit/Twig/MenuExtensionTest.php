<?php

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
use ByteArtist\MenuBundle\Twig\MenuExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\TwigFunction;

final class MenuExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $menuFactory = new MenuFactory($this->createMock(TranslatorInterface::class), $this->createMock(RouterInterface::class));

        $extension = new MenuExtension($menuFactory, []);

        $result = $extension->getFunctions();

        self::assertIsArray($result);
        self::assertArrayHasKey(0, $result);
        self::assertInstanceOf(TwigFunction::class, $result[0]);
    }

    public function testGenerate()
    {
        $generatorMock = $this->createMock(MenuGeneratorInterface::class);
        $generatorMock->method('generate')
            ->willReturn('created');

        $menuFactoryMock = $this->createMock(MenuFactory::class);
        $menuFactoryMock->method('create')
            ->willReturn($generatorMock);

        $extension = new MenuExtension($menuFactoryMock, []);
        $environmentMock = $this->createMock(Environment::class);

        static::assertSame('created', $extension->generate($environmentMock));
    }
}
