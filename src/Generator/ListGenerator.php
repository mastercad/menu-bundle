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
 * Class generates list menu by given config and environment.
 */
class ListGenerator implements MenuGeneratorInterface
{
    private TranslatorInterface $translator;

    private RouteProvider $routeProvider;

    private Environment $environment;

    private RequestStack $requestStack;

    private string $route;

    private string $topLevelActive;

    /**
     * List Generator CTOR.
     */
    public function __construct(TranslatorInterface $translator, RouteProvider $routeProvider, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->routeProvider = $routeProvider;
        $this->requestStack = $requestStack;
        $this->route = $requestStack->getCurrentRequest()->get('_route');
    }

    /**
     * Generates list menu by given menu tree and given environment.
     */
    public function generate(array $menuTree, Environment $environment): string
    {
        $this->environment = $environment;

        return $this->processPages($menuTree['pages']);
    }

    /**
     * Process given pages depending on given level.
     */
    private function processPages(array $pages, int $level = 0): string
    {
        $menuContent = '';
        $template = '@Menu/menu/list/subnav.html.twig';

        foreach ($pages as $label => $config) {
            if (!$level) {
                $this->topLevelActive = '';
                $template = '@Menu/menu/list/main.html.twig';
            }
            $menuContent .= $this->generateMenuPartContent($label, $config, $level);
        }

        return $this->environment->render(
            $template,
            [
                'menuContent' => $menuContent,
                'useOriginalCss' => $pages['use_orig_css'] ?? true,
                'useOriginalJs' => $pages['use_orig_js'] ?? true,
            ]
        );
    }

    /**
     * Generate content for menu part.
     */
    private function generateMenuPartContent(string $label, array $config, int $level): string
    {
        if ('divider' === $label) {
            return '<hr class="byte-artist-menu-divider" />';
        }
        $active = '';
        if (isset($config['path'])
            && $this->route === $config['path']
        ) {
            $active = $this->topLevelActive = 'active';
        }
        $submenuContent = '';
        if (!empty($config['pages'])
            && \is_array($config['pages'])
        ) {
            $submenuContent = $this->processPages($config['pages'], $level + 1);
        }

        return $this->environment->render(
            '@Menu/menu/list/link.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $this->routeProvider->provide($config['path']),
                'active' => 0 === $level ? '' : $active,
                'topLevelActive' => 0 === $level ? $this->topLevelActive : '',
                'submenuContent' => $submenuContent,
            ]
        );
    }
}
