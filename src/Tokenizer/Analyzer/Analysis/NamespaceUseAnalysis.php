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
final class NamespaceUseAnalysis implements StartEndTokenAwareAnalysis
{
    public const TYPE_CLASS = 1; // "classy" could be class, interface or trait
    public const TYPE_FUNCTION = 2;
    public const TYPE_CONSTANT = 3;

    /**
     * The fully qualified use namespace.
     */
    private string $fullName;

    /**
     * The short version of use namespace or the alias name in case of aliased use statements.
     */
    private string $shortName;

    /**
     * Is the use statement part of multi-use (`use A, B, C;`, `use A\{B, C};`)?
     */
    private bool $isInMulti;

    /**
     * Is the use statement being aliased?
     */
    private bool $isAliased;

    /**
     * The start index of the namespace declaration in the analyzed Tokens.
     */
    private int $startIndex;

    /**
     * The end index of the namespace declaration in the analyzed Tokens.
     */
    private int $endIndex;

    /**
     * The type of import: class, function or constant.
     */
    private int $type;

    /**
     * @param self::TYPE_* $type
     */
    public function __construct(
        int $type,
        string $fullName,
        string $shortName,
        bool $isAliased,
        bool $isInMulti,
        int $startIndex,
        int $endIndex
    ) {
        $this->type = $type;
        $this->fullName = $fullName;
        $this->shortName = $shortName;
        $this->isAliased = $isAliased;
        $this->isInMulti = $isInMulti;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function isAliased(): bool
    {
        return $this->isAliased;
    }

    public function isInMulti(): bool
    {
        return $this->isInMulti;
    }

    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    public function getEndIndex(): int
    {
        return $this->endIndex;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isClass(): bool
    {
        return self::TYPE_CLASS === $this->type;
    }

    public function isFunction(): bool
    {
        return self::TYPE_FUNCTION === $this->type;
    }

    public function isConstant(): bool
    {
        return self::TYPE_CONSTANT === $this->type;
    }
}
