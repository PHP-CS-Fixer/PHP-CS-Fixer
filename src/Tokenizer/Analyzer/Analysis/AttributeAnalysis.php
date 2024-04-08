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
    private int $openingBracketIndex;
    private int $closingBracketIndex;

    /**
     * @var _AttributeItems
     */
    private array $attributes;

    /**
     * @param _AttributeItems $attributes
     */
    public function __construct(int $startIndex, int $endIndex, int $openingBracketIndex, int $closingBracketIndex, array $attributes)
    {
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->openingBracketIndex = $openingBracketIndex;
        $this->closingBracketIndex = $closingBracketIndex;
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

    public function getOpeningBracketIndex(): int
    {
        return $this->openingBracketIndex;
    }

    public function getClosingBracketIndex(): int
    {
        return $this->closingBracketIndex;
    }

    /**
     * @return _AttributeItems
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
