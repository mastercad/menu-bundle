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

namespace ByteArtist\MenuBundle\Test\Unit\DependencyInjection;

use ByteArtist\MenuBundle\DependencyInjection\MenuExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class MenuExtensionTest extends TestCase
{
    /**
     * @var MenuExtension
     */
    private $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = $this->createExtension();
    }

    public function testGetConfigWithDefaultValues(): void
    {
        $container = $this->createContainerBuilder();
        $this->extension->load([], $container);

        static::assertTrue($container->hasParameter('menu.tree'));
        $menuTree = $container->getParameter('menu.tree');
        static::assertSame('default', $menuTree['type']);

        $expected = [];
        static::assertArrayHasKey('pages', $menuTree);
        static::assertSame($expected, $menuTree['pages']);
    }

    public function testGetConfigWithOneLayer(): void
    {
        $configs = [
            'type' => 'default',
            'pages' => [
                'val1' => ['path' => 'array_value_1'],
                'val2' => ['path' => 'array_value_2'],
            ],
        ];
        $container = $this->createContainerBuilder();
        $this->extension->load([$configs], $container);

        static::assertTrue($container->hasParameter('menu.tree'));
        $menuTree = $container->getParameter('menu.tree');
        static::assertSame('default', $menuTree['type']);

        $expected = [
            'val1' => ['path' => 'array_value_1', 'pages' => []],
            'val2' => ['path' => 'array_value_2', 'pages' => []],
        ];

        static::assertArrayHasKey('pages', $menuTree);
        static::assertSame($expected, $menuTree['pages']);
    }

    protected function createExtension(): MenuExtension
    {
        return new MenuExtension();
    }

    private function createContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }
}
