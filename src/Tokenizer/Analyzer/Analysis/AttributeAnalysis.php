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
 *
 * @phpstan-type _AttributeItems list<array{start: int, end: int, name: string}>
 */
final class AttributeAnalysis
{
    private int $startIndex;
    private int $endIndex;
    private int $openingBracketsIndex;
    private int $closingBracketsIndex;

    /**
     * @var _AttributeItems
     */
    private array $attributes;

    /**
     * @param _AttributeItems $attributes
     */
    public function __construct(int $startIndex, int $endIndex, int $openingBracketsIndex, int $closingBracketsIndex, array $attributes)
    {
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->openingBracketsIndex = $openingBracketsIndex;
        $this->closingBracketsIndex = $closingBracketsIndex;
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

    public function getOpeningBracketsIndex(): int
    {
        return $this->openingBracketsIndex;
    }

    public function getClosingBracketsIndex(): int
    {
        return $this->closingBracketsIndex;
    }

    /**
     * @return _AttributeItems
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
