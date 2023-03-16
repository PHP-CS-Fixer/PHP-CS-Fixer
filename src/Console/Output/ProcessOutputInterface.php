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

namespace PhpCsFixer\Console\Output;

/**
 * @internal
 */
interface ProcessOutputInterface
{
    public const OUTPUT_TYPE_NONE = 'none';
    public const OUTPUT_TYPE_DOTS = 'dots';
    public const OUTPUT_TYPES = [
        self::OUTPUT_TYPE_NONE,
        self::OUTPUT_TYPE_DOTS,
    ];

    public function printLegend(): void;
}
