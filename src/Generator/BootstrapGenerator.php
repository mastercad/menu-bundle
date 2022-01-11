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
 * Class generates menu in bootstrap style.
 */
class BootstrapGenerator implements MenuGeneratorInterface
{
    private TranslatorInterface $translator;

    private RouterInterface $router;

    /**
     * Bootstrap generator CTOR.
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * Generates bootstrap style menu by given menu tree and environment.
     */
    public function generate(array $menuTree, Environment $environment): string
    {
        $menuContent = '';
        foreach ($menuTree['pages'] as $label => $config) {
            $menuContent .= $this->generateMenuPartContent($environment, $label, $config);
        }

        return $environment->render(
            '@Menu/menu/bootstrap/main.html.twig',
            [
                'menuContent' => $menuContent,
                'brandName' => $menuTree['brand_name'] ?? '',
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
            return '<div class="dropdown-divider"></div>';
        }
        if (!\array_key_exists('pages', $config)
            || empty($config['pages'])
        ) {
            return $environment->render(
                '@Menu/menu/bootstrap/link.html.twig',
                [
                    'label' => $this->translator->trans($label),
                    'path' => $config['path'] ? $this->router->generate($config['path']) : '#',
                ]
            );
        }

        return $environment->render(
            '@Menu/menu/bootstrap/subnav.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $config['path'] ? $this->router->generate($config['path']) : '#',
                'dropDownId' => $label.'Id',
                'subnavContent' => $this->generateSubMenuContent($environment, $label, $config),
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
            if ('divider' === $label) {
                $subnavContentContent .= '<div class="dropdown-divider"></div>';
            } else {
                $subnavContentContent .= '<a class="dropdown-item" href="'.($config['path'] ?? '#').'">'.$label.'</a>';
            }
        }

        return $environment->render(
            '@Menu/menu/bootstrap/subnav-content.html.twig',
            [
                'label' => $this->translator->trans($label),
                'path' => $config['path'] ?? '#',
                'dropDownId' => $parentLabel.'Id',
                'subnavContentContent' => $subnavContentContent,
            ]
        );
    }
}
