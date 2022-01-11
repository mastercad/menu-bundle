<?php

namespace ByteArtist\MenuBundle\Test\Integration;

use ByteArtist\MenuBundle\Generator\ListGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListGeneratorTest extends KernelTestCase
{
    private ListGenerator $generator;

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
        $this->generator = new ListGenerator($translatorMock, $routerMock);
    }

    public function testMenuWithoutPages()
    {
        $menuTree = ['pages' => []];
        $result = str_replace('\n', '', $this->generator->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        $this->assertMatchesRegularExpression(
            '/<ul class="subnav">\s*<\/ul>/',
            $result
        );
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
        $result = preg_replace('/\\n/', '', $this->generator->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        $this->assertMatchesRegularExpression(
            '/<ul id="navbar" class="">\s*'.
            '<li>\s*'.
            '<a href="route_#">trans_home<\/a>\s*'.
            '<\/li><li>\s*'.
            '<a href="route_my_path">trans_contact<\/a>\s*'.
            '<\/li><\/ul>/',
            $result
        );
    }

    public function testGenerateMenuWithSubmenuBrandAndDivider()
    {
        $menuTree = [
            'brand_name' => 'brand',
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
        $result = str_replace('\n', '', $this->generator->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        $this->assertMatchesRegularExpression(
            '/<ul id="navbar" class="">\s*'.
            '<li>\s*'.
            '<a href="route_#">trans_home<\/a>\s*'.
            '<\/li>\s*'.
            '<li>\s*'.
            '<a href="route_admin_index">trans_admin<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li>\s*'.
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
        $result = str_replace('\n', '', $this->generator->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        $this->assertMatchesRegularExpression(
            '/<ul id="navbar" class="">\s*'.
            '<li>\s*'.
            '<a href="route_home_index">trans_home<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li>\s*'.
            '<a href="route_admin_index">trans_admin<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li>\s*'.
            '<a href="route_user_index">trans_user<\/a>\s*'.
            '<ul class="subnav">\s*'.
            '<li>\s*'.
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
}