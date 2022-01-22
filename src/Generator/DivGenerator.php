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

namespace ByteArtist\MenuBundle\Generator;

use ByteArtist\MenuBundle\Interfaces\MenuGeneratorInterface;
use ByteArtist\MenuBundle\Provider\RouteProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class to generate div style menu.
 */
class DivGenerator implements MenuGeneratorInterface
{
    private TranslatorInterface $translator;

    private RouteProvider $routeProvider;

    private RequestStack $requestStack;

    private string $route;

    private string $topLevelActiveClass;

    /**
     * Div generator CTOR.
     */
    public function __construct(TranslatorInterface $translator, RouteProvider $routeProvider, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->routeProvider = $routeProvider;
        $this->requestStack = $requestStack;
        $this->route = $requestStack->getCurrentRequest()->get('_route');
    }

    /**
     * Generates div style menu by given menu tree and given environment.
     */
    public function generate(array $menuTree, Environment $environment): string
    {
        $menuContent = '';
        foreach ($menuTree['pages'] as $label => $config) {
            $this->topLevelActiveClass = '';
            $menuContent .= $this->generateMenuPartContent($environment, $label, $config);
        }

        return $environment->render(
            '@Menu/menu/div/main.html.twig',
            [
                'menuContent' => $menuContent,
                'useOriginalCss' => $menuTree['use_orig_css'] ?? true,
                'useOriginalJs' => $menuTree['use_orig_js'] ?? true,
            ]
        );
    }

    /**
     * Generates content for menu part by given label, config and environment.
     */
    private function generateMenuPartContent(Environment $environment, string $label, array $config): string
    {
        if ('divider' === $label) {
            return '<div class="byte-artist-menu-divider" ></div>';
        }

        $activeClass = '';
        if (isset($config['path'])
            && $this->route === $config['path']
        ) {
            $activeClass = $this->topLevelActiveClass = 'active';
        }

        if (!\array_key_exists('pages', $config)
            || empty($config['pages'])
        ) {
            return $environment->render(
                '@Menu/menu/div/link.html.twig',
                [
                    'label' => $this->translator->trans($label),
                    'path' => $this->routeProvider->provide($config['path']),
                    'activeClass' => $activeClass,
                ]
            );
        }

        return $environment->render(
            '@Menu/menu/div/subnav.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $this->routeProvider->provide($config['path']),
                'subnavContent' => $this->generateSubMenuContent($environment, $label, $config),
            ]
        );
    }

    /**
     * Generates content for sub menu by given lave, config and environment.
     */
    private function generateSubMenuContent(Environment $environment, string $parentLabel, array $parentConfig): string
    {
        $subnavContentContent = '';
        foreach ($parentConfig['pages'] as $label => $config) {
            $subnavContentContent .= $this->generateMenuPartContent($environment, $label, $config);
        }

        $subnavContent = $environment->render(
            '@Menu/menu/div/button.html.twig',
            [
                'label' => $this->translator->trans($parentLabel),
                'topLevelActiveClass' => $this->topLevelActiveClass,
            ]
        );

        return $subnavContent.$environment->render(
            '@Menu/menu/div/subnav-content.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $this->routeProvider->provide($config['path']),
                'subnavContentContent' => $subnavContentContent,
            ]
        );
    }
}
