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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class DivMenuTest extends KernelTestCase
{
    private DivGenerator $divGenerator;

    public function setUp(): void
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);

        $closureTranslator = function($arg) {
            return 'trans_'.$arg;
        };

        $translatorMock->method('trans')
            ->willReturnCallback($closureTranslator);

        $routerMock = $this->createMock(RouterInterface::class);

        $closureRouter = function($arg) {
            return 'route_'.$arg;
        };

        $routerMock->method('generate')
            ->willReturnCallback($closureRouter);

        static::bootKernel();
        $this->divGenerator = new DivGenerator($translatorMock, $routerMock);
    }

    public function testWithoutPages(): void
    {
        $menuTree = ['pages' => []];
        $result = $this->divGenerator->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        $this->assertMatchesRegularExpression('/<div class="navbar">\s*<\/div>/', $result);
    }

    public function testWithSimpleMenu(): void
    {
        $menuTree = [
            'pages' => [
                'home' => [
                    'path' => '#'
                ],
                'contact' => [
                    'path' => 'my_path',
                    'pages' => []
                ]
            ]
        ];
        $result = $this->divGenerator->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        $this->assertMatchesRegularExpression(
            '/<div class="navbar">\s*<a href="route_#">trans_home<\/a>\s*<a href="route_my_path">trans_contact<\/a>\s*<\/div>/',
            $result
        );
    }

    public function testGenerateMenuWithSubmenuAndDivider()
    {
        $menuTree = [
            'pages' => [
                'home' => [
                    'path' => '#'
                ],
                'admin' => [
                    'path' => 'admin_index',
                    'pages' => [
                        'admin_user_create' => [
                            'path' => 'user_create'
                        ],
                        'admin_user_edit' => [
                            'path' => 'user_edit'
                        ],
                        'divider' => [],
                        'admin_page_create' => [
                            'path' => 'page_create'
                        ]
                    ]
                ],
                'contact' => [
                    'path' => 'my_path',
                    'pages' => []
                ]
            ]
        ];
        $result = $this->divGenerator->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        $this->assertMatchesRegularExpression(
            '/<div class="navbar">\s*<a href="route_#">trans_home<\/a>\s*<div class="subnav">\s*'.
            '<button class="subnavbtn">trans_admin <i class="fa fa-caret-down"><\/i><\/button>\s*<div class="subnav-content">'.
            '\s*<a href="route_user_create">trans_admin_user_create<\/a>\s*<a href="route_user_edit">'.
            'trans_admin_user_edit<\/a>\s*<div class="byte-artist-menu-divider" ><\/div><a href="route_page_create">'.
            'trans_admin_page_create<\/a>\s*<\/div>\s*<\/div><a href="route_my_path">trans_contact<\/a>\s*<\/div>/',
            $result
        );
    }

    public function testGenerateMultipleSubMenus()
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
                                            'pages' => []
                                        ],
                                        'user_edit' => [
                                            'path' => 'user_edit',
                                            'pages' => []
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $result = $this->divGenerator->generate($menuTree, self::$kernel->getContainer()->get('twig'));

        $this->assertMatchesRegularExpression(
            '/<div class="navbar">\s*'.
            '<div class="subnav">\s*'.
            '<button class="subnavbtn">trans_home <i class="fa fa-caret-down"><\/i><\/button>\s*'.
            '<div class="subnav-content">\s*'.
            '<div class="subnav">\s*'.
            '<button class="subnavbtn">trans_admin <i class="fa fa-caret-down"><\/i><\/button>\s*'.
            '<div class="subnav-content">\s*'.
            '<div class="subnav">\s*<button class="subnavbtn">trans_user '.
            '<i class="fa fa-caret-down"><\/i><\/button>\s*'.
            '<div class="subnav-content">\s*'.
            '<a href="route_user_create">trans_user_create<\/a>\s*'.
            '<a href="route_user_edit">trans_user_edit<\/a>\s*'.
            '<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/',
            $result
        );
    }
}
