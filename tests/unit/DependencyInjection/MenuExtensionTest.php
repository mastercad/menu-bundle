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

    public function setUp(): void
    {
        parent::setUp();

        $this->extension = $this->createExtension();
    }

    public function testGetConfigWithDefaultValues()
    {
        $container = $this->createContainerBuilder();
        $this->extension->load([], $container);

        $this->assertTrue($container->hasParameter("menu.tree"));
        $menuTree = $container->getParameter("menu.tree");
        $this->assertEquals("default", $menuTree["type"]);

        $expected = [];
        $this->assertArrayHasKey("pages", $menuTree);
        $this->assertEquals($expected, $menuTree["pages"]);
    }

    public function testGetConfigWithOneLayer()
    {
        $configs = [
            "type"     => "default",
            "pages" => [
                "val1" => ["path" => "array_value_1"],
                "val2" => ["path" => "array_value_2"],
            ],
        ];
        $container = $this->createContainerBuilder();
        $this->extension->load([$configs], $container);

        $this->assertTrue($container->hasParameter("menu.tree"));
        $menuTree = $container->getParameter("menu.tree");
        $this->assertEquals('default', $menuTree["type"]);

        $expected = [
            "val1" => ["path" => "array_value_1", "pages" => []],
            "val2" => ["path" => "array_value_2", "pages" => []],
        ];

        $this->assertArrayHasKey("pages", $menuTree);
        $this->assertEquals($expected, $menuTree["pages"]);
    }

    /**
     * @return MenuExtension
     */
    protected function createExtension(): MenuExtension
    {
        return new MenuExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function createContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }
}
