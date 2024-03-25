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

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class AttributeAnalysis
{
    private int $startIndex;
    private int $endIndex;

    /**
     * @var list<array{start: int, end: int, name: string}>
     */
    private array $attributes;

    /**
     * @param list<array{start: int, end: int, name: string}> $attributes
     */
    public function __construct(int $startIndex, int $endIndex, array $attributes)
    {
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->attributes = $attributes;
    }

    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    public function getEndIndex(): int
    {
        return $this->endIndex;
    }

    /**
     * @return list<array{start: int, end: int, name: string}>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
