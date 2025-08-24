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

namespace PhpCsFixer\Console\Output\Progress;

/**
 * @internal
 */
final class ProgressOutputType
{
    public const NONE = 'none';
    public const DOTS = 'dots';
    public const BAR = 'bar';

    public const ALL = [
        self::BAR,
        self::DOTS,
        self::NONE,
    ];
}
