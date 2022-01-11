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

namespace ByteArtist\MenuBundle\Interfaces;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Interface for all menu generators, ensures generate and CTOR exists like expected.
 */
interface MenuGeneratorInterface
{
    public function __construct(TranslatorInterface $translator, RouterInterface $router);

    public function generate(array $menuTree, Environment $environment);
}
