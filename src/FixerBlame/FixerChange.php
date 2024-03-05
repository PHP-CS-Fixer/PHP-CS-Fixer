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

namespace PhpCsFixer\FixerBlame;

final class FixerChange
{
    private string $fixerName;

    private int $line;

    private int $char;

    public function __construct(
        string $fixerName,
        int $line,
        int $char = 0
    ) {
        $this->fixerName = $fixerName;
        $this->line = $line;
        $this->char = $char;
    }

    public function __toString()
    {
        return sprintf('line: % 2d char: % 2d fixer: %s', $this->line, $this->char, $this->fixerName);
    }

    public function getFixerName(): string
    {
        return $this->fixerName;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getChar(): int
    {
        return $this->char;
    }
}
