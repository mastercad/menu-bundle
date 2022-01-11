<?php

namespace ByteArtist\MenuBundle\Test\Integration;

use ByteArtist\MenuBundle\Generator\BootstrapGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BootstrapGeneratorTest extends KernelTestCase
{
    private BootstrapGenerator $generator;

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
        $this->generator = new BootstrapGenerator($translatorMock, $routerMock);
    }

    public function testMenuWithoutPages()
    {
        $menuTree = ['pages' => []];
        $result = str_replace('\n', '', $this->generator->generate($menuTree, self::$kernel->getContainer()->get('twig')));

        $this->assertMatchesRegularExpression(
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
            '/<nav class="navbar navbar-expand-lg navbar-light bg-light">\s*'.
            '<a class="navbar-brand" href="\/"><\/a>\s*'.
            '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" '.
            'aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\s*'.
            '<span class="navbar-toggler-icon"><\/span>\s*'.
            '<\/button>\s*'.
            '<div class="collapse navbar-collapse" id="navbarSupportedContent">\s*'.
            '<ul class="navbar-nav mr-auto">\s*'.
            '<li class="nav-item active">\s*'.
            '<a class="nav-link" href="route_#">trans_home<span class="sr-only">\(current\)<\/span><\/a>\s*'.
            '<\/li>\s*'.
            '<li class="nav-item active">\s*'.
            '<a class="nav-link" href="route_my_path">trans_contact<span class="sr-only">\(current\)<\/span><\/a>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/div>\s*'.
            '<\/nav>/',
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
            '/<nav class="navbar navbar-expand-lg navbar-light bg-light">\s*'.
            '<a class="navbar-brand" href="\/">brand<\/a>\s*'.
            '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" '.
            'aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\s*'.
            '<span class="navbar-toggler-icon"><\/span>\s*'.
            '<\/button>\s*'.
            '<div class="collapse navbar-collapse" id="navbarSupportedContent">\s*'.
            '<ul class="navbar-nav mr-auto">\s*'.
            '<li class="nav-item active">\s*'.
            '<a class="nav-link" href="route_#">trans_home<span class="sr-only">\(current\)<\/span><\/a>\s*'.
            '<\/li>\s*'.
            '<li class="nav-item dropdown">\s*'.
            '<a class="nav-link dropdown-toggle" href="route_admin_index" id="navbaradminId" role="button" '.
            'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\s*'.
            'trans_admin\s*'.
            '<\/a>\s*'.
            '<div class="dropdown-menu" aria-labelledby="navbaradminId">\s*'.
            '<a class="dropdown-item" href="user_create">admin_user_create<\/a>'.
            '<a class="dropdown-item" href="user_edit">admin_user_edit<\/a>'.
            '<div class="dropdown-divider"><\/div><a class="dropdown-item" href="page_create">admin_page_create<\/a>\s*'.
            '<\/div>\s*'.
            '<\/li>\s*'.
            '<li class="nav-item active">\s*'.
            '<a class="nav-link" href="route_my_path">trans_contact<span class="sr-only">\(current\)<\/span><\/a>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/div>\s*'.
            '<\/nav>/',
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
            '/<nav class="navbar navbar-expand-lg navbar-light bg-light">\s*'.
            '<a class="navbar-brand" href="\/"><\/a>\s*'.
            '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" '.
            'aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">\s*'.
            '<span class="navbar-toggler-icon"><\/span>\s*'.
            '<\/button>\s*'.
            '<div class="collapse navbar-collapse" id="navbarSupportedContent">\s*'.
            '<ul class="navbar-nav mr-auto">\s*'.
            '<li class="nav-item dropdown">\s*'.
            '<a class="nav-link dropdown-toggle" href="route_home_index" id="navbarhomeId" role="button" '.
            'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\s*'.
            'trans_home\s*'.
            '<\/a>\s*'.
            '<div class="dropdown-menu" aria-labelledby="navbarhomeId">\s*'.
            '<a class="dropdown-item" href="admin_index">admin<\/a>\s*'.
            '<\/div>\s*'.
            '<\/li>\s*'.
            '<\/ul>\s*'.
            '<\/div>\s*'.
            '<\/nav>/',
            $result
        );
    }
}