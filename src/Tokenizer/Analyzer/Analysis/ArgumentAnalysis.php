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
final class ArgumentAnalysis
{
    /**
     * The default value of the argument.
     *
     * @var null|string
     */
    private $default;

    /**
     * The name of the argument.
     *
     * @var string
     */
    private $name;

    /**
     * The index where the name is located in the supplied Tokens object.
     *
     * @var int
     */
    private $nameIndex;

    /**
     * The type analysis of the argument.
     *
     * @var ?TypeAnalysis
     */
    private $typeAnalysis;

    public function __construct(string $name, int $nameIndex, ?string $default, ?TypeAnalysis $typeAnalysis = null)
    {
        $this->name = $name;
        $this->nameIndex = $nameIndex;
        $this->default = $default ?: null;
        $this->typeAnalysis = $typeAnalysis ?: null;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function hasDefault(): bool
    {
        return null !== $this->default;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameIndex(): int
    {
        return $this->nameIndex;
    }

    public function getTypeAnalysis(): ?TypeAnalysis
    {
        return $this->typeAnalysis;
    }

    public function hasTypeAnalysis(): bool
    {
        return null !== $this->typeAnalysis;
    }
}
