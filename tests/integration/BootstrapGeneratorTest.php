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

use ByteArtist\MenuBundle\Generator\BootstrapGenerator;

/**
 * @internal
 */
final class BootstrapGeneratorTest extends GeneratorTestAbstract
{
    protected string $generatorTestClass = BootstrapGenerator::class;

    public function testMenuWithoutPages(): void
    {
        $menuTree = ['pages' => []];
        $result = str_replace('\n', '', $this->generateGenerator('test')->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        static::assertMatchesRegularExpression(
            '/<nav class="navbar navbar-expand-lg navbar-light bg-light">\s*'.
            '<a class="navbar-brand" href="\/"><\/a>\s*'.
            '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" '.
                'aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\s*'.
                '<span class="navbar-toggler-icon"><\/span>\s*'.
                '<\/button>\s*'.
            '<div class="collapse navbar-collapse" id="navbarSupportedContent">\s*'.
            '<ul class="navbar-nav mr-auto">\s*'.
            '<\/ul>\s*'.
            '<\/div>\s*'.
            '<\/nav>/',
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
        $result = preg_replace('/\\n/', '', $this->generateGenerator('test')->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        static::assertMatchesRegularExpression(
            '/<nav class="navbar navbar-expand-lg navbar-light bg-light">\s*'.
            '<a class="navbar-brand" href="\/"><\/a>\s*'.
            '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" '.
            'aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\s*'.
            '<span class="navbar-toggler-icon"><\/span>\s*'.
            '<\/button>\s*'.
            '<div class="collapse navbar-collapse" id="navbarSupportedContent">\s*'.
            '<ul class="navbar-nav mr-auto">\s*'.
            '<li class="nav-item ">\s*'.
            '<a class="nav-link" href="route_#">trans_home <\/a>\s*'.
            '<\/li>\s*'.
            '<li class="nav-item ">\s*'.
            '<a class="nav-link" href="route_my_path">trans_contact <\/a>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/div>\s*'.
            '<\/nav>/',
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
        $result = str_replace('\n', '', $this->generateGenerator('page_create')->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        static::assertMatchesRegularExpression(
            '/<nav class="navbar navbar-expand-lg navbar-light bg-light">\s*'.
            '<a class="navbar-brand" href="\/">brand<\/a>\s*'.
            '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" '.
            'aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\s*'.
            '<span class="navbar-toggler-icon"><\/span>\s*'.
            '<\/button>\s*'.
            '<div class="collapse navbar-collapse" id="navbarSupportedContent">\s*'.
            '<ul class="navbar-nav mr-auto">\s*'.
            '<li class="nav-item ">\s*'.
            '<a class="nav-link" href="route_#">trans_home <\/a>\s*'.
            '<\/li>\s*'.
            '<li class="nav-item dropdown">\s*'.
            '<a class="nav-link dropdown-toggle active" href="route_admin_index" id="navbaradminId" role="button" '.
            'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\s*'.
            'trans_admin\s*'.
            '<\/a>\s*'.
            '<div class="dropdown-menu" aria-labelledby="navbaradminId">\s*'.
            '<a class="dropdown-item " href="route_user_create">admin_user_create <\/a>'.
            '<a class="dropdown-item " href="route_user_edit">admin_user_edit <\/a>'.
            '<div class="dropdown-divider"><\/div><a class="dropdown-item active" href="route_page_create">admin_page_create <span class="sr-only">\(current\)<\/span><\/a>\s*'.
            '<\/div>\s*'.
            '<\/li>\s*'.
            '<li class="nav-item ">\s*'.
            '<a class="nav-link" href="route_my_path">trans_contact <\/a>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/div>\s*'.
            '<\/nav>/',
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
            '/<nav class="navbar navbar-expand-lg navbar-light bg-light">\s*'.
            '<a class="navbar-brand" href="\/"><\/a>\s*'.
            '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" '.
            'aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\s*'.
            '<span class="navbar-toggler-icon"><\/span>\s*'.
            '<\/button>\s*'.
            '<div class="collapse navbar-collapse" id="navbarSupportedContent">\s*'.
            '<ul class="navbar-nav mr-auto">\s*'.
            '<li class="nav-item dropdown">\s*'.
            '<a class="nav-link dropdown-toggle " href="route_home_index" id="navbarhomeId" role="button" '.
            'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\s*'.
            'trans_home\s*'.
            '<\/a>\s*'.
            '<div class="dropdown-menu" aria-labelledby="navbarhomeId">\s*'.
            '<a class="dropdown-item " href="route_admin_index">admin <\/a>\s*'.
            '<\/div>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/div>\s*'.
            '<\/nav>/',
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
            '/<link href="https:\/\/getbootstrap\.com\/docs\/4\.0\/dist\/css\/bootstrap\.min\.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA\+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW\/dAiS6JXm" crossorigin="anonymous">/',
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
            '/<link href="https:\/\/getbootstrap\.com\/docs\/4\.0\/dist\/css\/bootstrap\.min\.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA\+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW\/dAiS6JXm" crossorigin="anonymous">/',
            $result
        );
    }

    public function testWithJs(): void
    {
        $menuTree = [
            'use_orig_js' => true,
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
            '/<script src="https:\/\/getbootstrap\.com\/docs\/4.0\/dist\/js\/bootstrap\.min\.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe\/JQGiRRSQQxSfFWpi1MquVdAyjUar5\+76PVCmYl" crossorigin="anonymous"><\/script>/',
            $result
        );

        static::assertMatchesRegularExpression(
            '/<script src="https:\/\/code\.jquery\.com\/jquery-3\.2\.1\.slim\.min\.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr\/rE9\/Qpg6aAZGJwFDMVNA\/GpGFF93hXpG5KkN" crossorigin="anonymous"><\/script>/',
            $result
        );
    }

    public function testWithoutJs(): void
    {
        $menuTree = [
            'use_orig_js' => false,
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
            '/<script src="https:\/\/getbootstrap\.com\/docs\/4.0\/dist\/js\/bootstrap\.min\.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe\/JQGiRRSQQxSfFWpi1MquVdAyjUar5\+76PVCmYl" crossorigin="anonymous"><\/script>/',
            $result
        );

        static::assertDoesNotMatchRegularExpression(
            '/<script src="https:\/\/code\.jquery\.com\/jquery-3\.2\.1\.slim\.min\.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr\/rE9\/Qpg6aAZGJwFDMVNA\/GpGFF93hXpG5KkN" crossorigin="anonymous"><\/script>/',
            $result
        );
    }
}
