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

use ByteArtist\MenuBundle\Generator\DivGenerator;

/**
 * @internal
 */
final class DivMenuTest extends GeneratorTestAbstract
{
    protected string $generatorTestClass = DivGenerator::class;

    public function testWithoutPages(): void
    {
        $menuTree = ['pages' => []];
        $result = $this->generateGenerator('')->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        static::assertMatchesRegularExpression('/<div class="navbar">\s*<\/div>/', $result);
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
        $result = $this->generateGenerator('my_path')->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        static::assertMatchesRegularExpression(
            '/<div class="navbar">\s*<a href="route_#">trans_home<\/a>\s*<a class="active" href="route_my_path">trans_contact<\/a>\s*<\/div>/',
            $result
        );
    }

    public function testGenerateMenuWithSubmenuAndDivider(): void
    {
        $menuTree = [
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
        $result = $this->generateGenerator('page_create')->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        static::assertMatchesRegularExpression(
            '/<div class="navbar">\s*'.
            '<a href="route_#">trans_home<\/a>\s*'.
            '<div class="subnav">\s*'.
            '<button class="subnavbtn active">trans_admin <i class="fa fa-caret-down"><\/i><\/button>\s*'.
            '<div class="subnav-content">\s*'.
            '<a href="route_user_create">trans_admin_user_create<\/a>\s*'.
            '<a href="route_user_edit">trans_admin_user_edit<\/a>\s*'.
            '<div class="byte-artist-menu-divider" ><\/div><a class="active" href="route_page_create">trans_admin_page_create<\/a>\s*'.
            '<\/div>\s*'.
            '<\/div><a href="route_my_path">trans_contact<\/a>\s*'.
            '<\/div>/',
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
        $result = $this->generateGenerator('user_edit')->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        static::assertMatchesRegularExpression(
            '/<div class="navbar">\s*'.
            '<div class="subnav">\s*'.
            '<button class="subnavbtn active">trans_home <i class="fa fa-caret-down"><\/i><\/button>\s*'.
            '<div class="subnav-content">\s*'.
            '<div class="subnav">\s*'.
            '<button class="subnavbtn active">trans_admin <i class="fa fa-caret-down"><\/i><\/button>\s*'.
            '<div class="subnav-content">\s*'.
            '<div class="subnav">\s*'.
            '<button class="subnavbtn active">trans_user <i class="fa fa-caret-down"><\/i><\/button>\s*'.
            '<div class="subnav-content">\s*'.
            '<a href="route_user_create">trans_user_create<\/a>\s*'.
            '<a class="active" href="route_user_edit">trans_user_edit<\/a>\s*'.
            '<\/div>\s*'.
            '<\/div>\s*'.
            '<\/div>\s*'.
            '<\/div>\s*'.
            '<\/div>\s*'.
            '<\/div>\s*'.
            '<\/div>/',
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
            '/<link rel="stylesheet" href="https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome\/4\.7\.0\/css\/font-awesome\.min\.css">/',
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
            '/<link rel="stylesheet" href="https:\/\/cdnjs.cloudflare.com\/ajax\/libs\/font-awesome\/4\.7\.0\/css\/font-awesome\.min\.css">/',
            $result
        );

        static::assertDoesNotMatchRegularExpression(
            '/<style>.*<\/style>/',
            $result
        );
    }
}
