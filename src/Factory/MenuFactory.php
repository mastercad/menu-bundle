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

namespace ByteArtist\MenuBundle\Factory;

use ByteArtist\MenuBundle\Generator\BootstrapGenerator;
use ByteArtist\MenuBundle\Generator\DivGenerator;
use ByteArtist\MenuBundle\Generator\ListGenerator;
use ByteArtist\MenuBundle\Interfaces\MenuGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Factory to create specific menu generator by given type.
 */
class MenuFactory
{
    public const MENU_TYPE_DEFAULT = 'default';
    public const MENU_TYPE_BOOTSTRAP = 'bootstrap';
    public const MENU_TYPE_LIST = 'list';
    public const MENU_TYPE_DIV = 'div';

    private TranslatorInterface $translator;
    private RouterInterface $router;

    /**
     * Menu factory CTOR.
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * Creates menu generator by given type.
     */
    public function create(string $type): MenuGeneratorInterface
    {
        switch ($type) {
            case static::MENU_TYPE_BOOTSTRAP:
                return new BootstrapGenerator($this->translator, $this->router);

            case static::MENU_TYPE_DIV:
                return new DivGenerator($this->translator, $this->router);

            case static::MENU_TYPE_DEFAULT:
            case static::MENU_TYPE_LIST:
            default:
                return new ListGenerator($this->translator, $this->router);
        }
    }
}
