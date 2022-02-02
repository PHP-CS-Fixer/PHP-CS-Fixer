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
final class NamespaceAnalysis implements StartEndTokenAwareAnalysis
{
    /**
     * The fully qualified namespace name.
     *
     * @var string
     */
    private $fullName;

    /**
     * The short version of the namespace.
     *
     * @var string
     */
    private $shortName;

    /**
     * The start index of the namespace declaration in the analyzed Tokens.
     *
     * @var int
     */
    private $startIndex;

    /**
     * The end index of the namespace declaration in the analyzed Tokens.
     *
     * @var int
     */
    private $endIndex;

    /**
     * The start index of the scope of the namespace in the analyzed Tokens.
     *
     * @var int
     */
    private $scopeStartIndex;

    /**
     * The end index of the scope of the namespace in the analyzed Tokens.
     *
     * @var int
     */
    private $scopeEndIndex;

    public function __construct(string $fullName, string $shortName, int $startIndex, int $endIndex, int $scopeStartIndex, int $scopeEndIndex)
    {
        $this->fullName = $fullName;
        $this->shortName = $shortName;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->scopeStartIndex = $scopeStartIndex;
        $this->scopeEndIndex = $scopeEndIndex;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    public function getEndIndex(): int
    {
        return $this->endIndex;
    }

    public function getScopeStartIndex(): int
    {
        return $this->scopeStartIndex;
    }

    public function getScopeEndIndex(): int
    {
        return $this->scopeEndIndex;
    }

    public function isGlobalNamespace(): bool
    {
        return '' === $this->getFullName();
    }
}
