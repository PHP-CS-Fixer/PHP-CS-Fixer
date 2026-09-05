<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Compat\Symfony\Component\Console\Style;

class_alias(
    method_exists(\Symfony\Component\Console\Style\SymfonyStyle::class, 'outlineSuccess') ? \Symfony\Component\Console\Style\SymfonyStyle::class : SymfonyStyleCompat::class,
    SymfonyStyle::class,
);
