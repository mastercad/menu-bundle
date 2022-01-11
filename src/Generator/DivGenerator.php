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
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class to generate div style menu.
 */
class DivGenerator implements MenuGeneratorInterface
{
    private TranslatorInterface $translator;

    private RouterInterface $router;

    /**
     * Div generator CTOR.
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * Generates div style menu by given menu tree and given environment.
     */
    public function generate(array $menuTree, Environment $environment): string
    {
        $menuContent = '';
        foreach ($menuTree['pages'] as $label => $config) {
            $menuContent .= $this->generateMenuPartContent($environment, $label, $config);
        }

        return $environment->render(
            '@Menu/menu/div/main.html.twig',
            [
                'menuContent' => $menuContent,
                'useOriginalCss' => $menuTree['use_orig_css'],
                'useOriginalJs' => $menuTree['use_orig_js']
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
        if (!\array_key_exists('pages', $config)
            || empty($config['pages'])
        ) {
            return $environment->render(
                '@Menu/menu/div/link.html.twig',
                [
                    'label' => $this->translator->trans($label),
                    'path' => $config['path'] ? $this->router->generate($config['path']) : '#',
                ]
            );
        }

        return $environment->render(
            '@Menu/menu/div/subnav.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $config['path'] ? $this->router->generate($config['path']) : '#',
                'subnavContent' => $this->generateSubMenuContent($environment, $label, $config),
            ]
        );
    }

    /**
     * Generates content for sub menu by given lave, config and environment.
     */
    private function generateSubMenuContent(Environment $environment, string $label, array $parentConfig): string
    {
        $subnavContent = $environment->render(
            '@Menu/menu/div/button.html.twig',
            [
                'label' => $this->translator->trans($label),
            ]
        );

        $subnavContentContent = '';
        foreach ($parentConfig['pages'] as $label => $config) {
            $subnavContentContent .= $this->generateMenuPartContent($environment, $label, $config);
        }

        return $subnavContent.$environment->render(
            '@Menu/menu/div/subnav-content.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $config['path'] ?? '#',
                'subnavContentContent' => $subnavContentContent,
            ]
        );
    }
}
