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
 * Class generates menu in bootstrap style.
 */
class BootstrapGenerator implements MenuGeneratorInterface
{
    private TranslatorInterface $translator;

    private RouteProvider $routeProvider;

    private RequestStack $requestStack;

    private string $route;

    private string $topLevelActiveClass;

    /**
     * Bootstrap generator CTOR.
     */
    public function __construct(TranslatorInterface $translator, RouteProvider $routeProvider, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->routeProvider = $routeProvider;
        $this->requestStack = $requestStack;
        $this->route = $requestStack->getCurrentRequest()->get('_route');
    }

    /**
     * Generates bootstrap style menu by given menu tree and environment.
     */
    public function generate(array $menuTree, Environment $environment): string
    {
        $menuContent = '';
        $this->topLevelActiveClass = '';
        foreach ($menuTree['pages'] as $label => $config) {
            $menuContent .= $this->generateMenuPartContent($environment, $label, $config);
        }

        return $environment->render(
            '@Menu/menu/bootstrap/main.html.twig',
            [
                'menuContent' => $menuContent,
                'brandName' => $menuTree['brand_name'] ?? '',
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
        $active = '';
        $activeClass = '';
        $this->topLevelActiveClass = '';
        if ('divider' === $label) {
            return '<div class="dropdown-divider"></div>';
        }
        if (isset($config['path'])
            && $this->route === $config['path']
        ) {
            $active = '<span class="sr-only">(current)</span>';
            $activeClass = $this->topLevelActiveClass = 'active';
        }
        if (!\array_key_exists('pages', $config)
            || empty($config['pages'])
        ) {
            return $environment->render(
                '@Menu/menu/bootstrap/link.html.twig',
                [
                    'label' => $this->translator->trans($label),
                    'path' => $this->routeProvider->provide($config['path']),
                    'active' => $active,
                    'activeClass' => $activeClass,
                ]
            );
        }

        return $environment->render(
            '@Menu/menu/bootstrap/subnav.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $this->routeProvider->provide($config['path']),
                'dropDownId' => $label.'Id',
                'subnavContent' => $this->generateSubMenuContent($environment, $label, $config),
                'topLevelActiveClass' => $this->topLevelActiveClass,
            ]
        );
    }

    /**
     * Generates content for sub menu by given label, config and environment.
     */
    private function generateSubMenuContent(Environment $environment, string $parentLabel, array $parentConfig): string
    {
        $subnavContentContent = '';
        foreach ($parentConfig['pages'] as $label => $config) {
            $active = '';
            $activeClass = '';
            if (isset($config['path'])
                && $this->route === $config['path']
            ) {
                $active = '<span class="sr-only">(current)</span>';
                $activeClass = $this->topLevelActiveClass = 'active';
            }
            if ('divider' === $label) {
                $subnavContentContent .= '<div class="dropdown-divider"></div>';
            } else {
                $subnavContentContent .= '<a class="dropdown-item '.$activeClass.'" href="'.
                    $this->routeProvider->provide($config['path']).'">'.$label.' '.$active.'</a>';
            }
        }

        return $environment->render(
            '@Menu/menu/bootstrap/subnav-content.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $this->routeProvider->provide($config['path']),
                'dropDownId' => $parentLabel.'Id',
                'subnavContentContent' => $subnavContentContent,
            ]
        );
    }
}
