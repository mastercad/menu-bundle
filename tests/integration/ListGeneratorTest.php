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

use ByteArtist\MenuBundle\Generator\ListGenerator;

/**
 * @internal
 */
final class ListGeneratorTest extends GeneratorTestAbstract
{
    protected string $generatorTestClass = ListGenerator::class;

    public function testMenuWithoutPages(): void
    {
        $menuTree = ['pages' => []];
        $result = str_replace('\n', '', $this->generateGenerator('')->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        static::assertMatchesRegularExpression(
            '/<ul class="subnav">\s*<\/ul>/',
            $result
        );
    }

    public function testWithSimpleMenu(): void
    {
        $menuTree = [
            'pages' => [
                'home' => [
                    'path' => '#',
                ],
                'contact' => [
                    'path' => 'my_path',
                    'pages' => [],
                ],
            ],
        ];
        $result = preg_replace('/\\n/', '', $this->generateGenerator('')->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        static::assertMatchesRegularExpression(
            '/<ul id="navbar">\s*'.
            '<li>\s*'.
            '<a href="route_#">trans_home<\/a>\s*'.
            '<\/li><li>\s*'.
            '<a href="route_my_path">trans_contact<\/a>\s*'.
            '<\/li><\/ul>/',
            $result
        );
    }

    public function testGenerateMenuWithSubmenuBrandAndDivider(): void
    {
        $menuTree = [
            'brand_name' => 'brand',
            'pages' => [
                'home' => [
                    'path' => '#',
                ],
                'admin' => [
                    'path' => 'admin_index',
                    'pages' => [
                        'admin_user_create' => [
                            'path' => 'user_create',
                        ],
                        'admin_user_edit' => [
                            'path' => 'user_edit',
                        ],
                        'divider' => [],
                        'admin_page_create' => [
                            'path' => 'page_create',
                        ],
                    ],
                ],
                'contact' => [
                    'path' => 'my_path',
                    'pages' => [],
                ],
            ],
        ];
        $result = str_replace('\n', '', $this->generateGenerator('user_create')->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        static::assertMatchesRegularExpression(
            '/<ul id="navbar">\s*'.
            '<li>\s*'.
            '<a href="route_#">trans_home<\/a>\s*'.
            '<\/li>\s*'.
            '<li class="active">\s*'.
            '<a href="route_admin_index">trans_admin<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li class="active">\s*'.
            '<a href="route_user_create">trans_admin_user_create<\/a>\s*'.
            '<\/li>\s*'.
            '<li>\s*'.
            '<a href="route_user_edit">trans_admin_user_edit<\/a>\s*'.
            '<\/li>\s*'.
            '<hr class="byte-artist-menu-divider" \/><li>\s*'.
            '<a href="route_page_create">trans_admin_page_create<\/a>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/li>\s*'.
            '<li>\s*'.
            '<a href="route_my_path">trans_contact<\/a>\s*'.
            '<\/li>\s*'.
            '<\/ul>/',
            $result
        );
    }

    public function testGenerateMultipleSubMenus(): void
    {
        $menuTree = [
            'pages' => [
                'home' => [
                    'path' => 'home_index',
                    'pages' => [
                        'admin' => [
                            'path' => 'admin_index',
                            'pages' => [
                                'user' => [
                                    'path' => 'user_index',
                                    'pages' => [
                                        'user_create' => [
                                            'path' => 'user_create',
                                            'pages' => [],
                                        ],
                                        'user_edit' => [
                                            'path' => 'user_edit',
                                            'pages' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = str_replace('\n', '', $this->generateGenerator('user_create')->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        static::assertMatchesRegularExpression(
            '/<ul id="navbar">\s*'.
            '<li class="active">\s*'.
            '<a href="route_home_index">trans_home<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li>\s*'.
            '<a href="route_admin_index">trans_admin<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li>\s*'.
            '<a href="route_user_index">trans_user<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li class="active">\s*'.
            '<a href="route_user_create">trans_user_create<\/a>\s*'.
            '<\/li>\s*'.
            '<li>\s*'.
            '<a href="route_user_edit">trans_user_edit<\/a>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/li>\s*'.
            '<\/ul>/',
            $result
        );
    }

    public function testWithCss(): void
    {
        $menuTree = [
            'use_orig_css' => true,
            'pages' => [
                'home' => [
                    'path' => '#',
                ],
                'contact' => [
                    'path' => 'my_path',
                    'pages' => [],
                ],
            ],
        ];
        $result = $this->generateGenerator('my_path')->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        static::assertMatchesRegularExpression(
            '/<style>.*<\/style>/s',
            $result
        );
    }

    public function testWithoutCss(): void
    {
        $menuTree = [
            'use_orig_css' => false,
            'pages' => [
                'home' => [
                    'path' => '#',
                ],
                'contact' => [
                    'path' => 'my_path',
                    'pages' => [],
                ],
            ],
        ];
        $result = $this->generateGenerator('my_path')->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        static::assertDoesNotMatchRegularExpression(
            '/<style>.*<\/style>/',
            $result
        );
    }
}
