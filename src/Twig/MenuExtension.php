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

namespace ByteArtist\MenuBundle\Twig;

use ByteArtist\MenuBundle\Factory\MenuFactory;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Menu extension class for twig.
 */
class MenuExtension extends AbstractExtension
{
    private MenuFactory $factory;

    /**
     * CTOR for menu extension class.
     */
    public function __construct(MenuFactory $factory, array $menuTree)
    {
        $this->factory = $factory;
        $this->menuTree = $menuTree;
    }

    /**
     * Return function definition for menu extension class.
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'menu',
                [$this, 'generate'],
                ['needs_environment' => true]
            ),
        ];
    }

    /**
     * Return generated menu from generator, created by factory and type from config.
     */
    public function generate(Environment $environment)
    {
        return $this->factory->create($this->menuTree['type'] ?? MenuFactory::MENU_TYPE_DEFAULT)
            ->generate($this->menuTree, $environment)
        ;
    }
}
