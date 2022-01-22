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

use ByteArtist\MenuBundle\Provider\RouteProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Interface for all menu generators, ensures generate and CTOR exists like expected.
 */
interface MenuGeneratorInterface
{
    public function __construct(TranslatorInterface $translator, RouteProvider $routeProvider, RequestStack $requestStack);

    public function generate(array $menuTree, Environment $environment);
}
