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

final class CodeChange
{
    private string $content;
    private int $change;
    private ?int $newLineNumber;
    private ?int $oldLineNumber;

    public function __construct(string $content, int $change, ?int $newLineNumber = null, ?int $oldLineNumber = null)
    {
        $this->content = $content;
        $this->change = $change;
        $this->newLineNumber = $newLineNumber;
        $this->oldLineNumber = $oldLineNumber;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getChange(): int
    {
        return $this->change;
    }

    public function getNewLineNumber(): ?int
    {
        return $this->newLineNumber;
    }

    public function getOldLineNumber(): ?int
    {
        return $this->oldLineNumber;
    }
}
