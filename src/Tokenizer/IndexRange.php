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

namespace PhpCsFixer\Tokenizer;

/**
 * Class for storing start index and end index pair withing one variable.
 *
 * @author Michael Vorisek <https://github.com/mvorisek>
 *
 * @final
 */
class IndexRange
{
    /** @var 0|positive-int */
    public int $start;

    /** @var 0|positive-int */
    public int $end;

    /**
     * @param 0|positive-int $start
     * @param 0|positive-int $end
     */
    public function __construct(int $start = null, int $end = null)
    {
        if (null !== $start) {
            $this->start = $start;
        }

        if (null !== $end) {
            $this->end = $end;
        }
    }

    public function count(): int
    {
        return 1 + $this->end - $this->start;
    }
}
