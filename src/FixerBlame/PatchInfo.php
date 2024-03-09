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

use SebastianBergmann\Diff\Differ;

final class PatchInfo
{
    private int $startKey;
    private int $endKey;
    private int $linesAdded = 0;
    private int $linesRemoved = 0;

    public function countChange(int $changeType): void
    {
        if (Differ::ADDED === $changeType) {
            ++$this->linesAdded;
        }

        if (Differ::REMOVED === $changeType) {
            ++$this->linesRemoved;
        }
    }

    /**
     * @param array<CodeChange> $diffResults
     *
     * @return array<CodeChange>
     */
    public function getPatchContent(array $diffResults): array
    {
        if ($this->startKey === $this->endKey) {
            return [$diffResults[$this->startKey]];
        }

        return \array_slice($diffResults, $this->startKey, $this->endKey - $this->startKey);
    }

    public function getChangeSum(): int
    {
        return $this->linesAdded - $this->linesRemoved;
    }

    public function getStartKey(): int
    {
        return $this->startKey;
    }

    public function setStartKey(int $startKey): void
    {
        $this->startKey = $startKey;
    }

    public function getEndKey(): int
    {
        return $this->endKey;
    }

    public function setEndKey(int $endKey): void
    {
        $this->endKey = $endKey;
    }

    public function getLinesAdded(): int
    {
        return $this->linesAdded;
    }

    public function getLinesRemoved(): int
    {
        return $this->linesRemoved;
    }
}
