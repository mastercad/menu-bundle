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

namespace ByteArtist\MenuBundle\Test\Unit\Factory;

use ByteArtist\MenuBundle\Factory\MenuFactory;
use ByteArtist\MenuBundle\Generator\BootstrapGenerator;
use ByteArtist\MenuBundle\Generator\DivGenerator;
use ByteArtist\MenuBundle\Generator\ListGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class MenuFactoryTest extends TestCase
{
    private MenuFactory $menuFactory;

    protected function setUp(): void
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $routerMock = $this->createMock(RouterInterface::class);

        $this->menuFactory = new MenuFactory($translatorMock, $routerMock);
    }

    public function testCreateDivGenerator(): void
    {
        static::assertInstanceOf(DivGenerator::class, $this->menuFactory->create(MenuFactory::MENU_TYPE_DIV));
    }

    public function testCreateListGenerator(): void
    {
        static::assertInstanceOf(ListGenerator::class, $this->menuFactory->create(MenuFactory::MENU_TYPE_LIST));
    }

    public function testCreateListGeneratorByDefaultWithoutValidSelection(): void
    {
        static::assertInstanceOf(ListGenerator::class, $this->menuFactory->create('test'));
    }

    public function testCreateListGeneratorByDefault(): void
    {
        static::assertInstanceOf(ListGenerator::class, $this->menuFactory->create(MenuFactory::MENU_TYPE_DEFAULT));
    }

    public function testCreateBootstrapGenerator(): void
    {
        static::assertInstanceOf(BootstrapGenerator::class, $this->menuFactory->create(MenuFactory::MENU_TYPE_BOOTSTRAP));
    }
}
