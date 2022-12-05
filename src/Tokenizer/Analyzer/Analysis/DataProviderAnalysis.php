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
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class DataProviderAnalysis
{
    /** @var string */
    private $name;

    /** @var int */
    private $nameIndex;

    /** @var array<int> */
    private $usageIndices;

    /**
     * @param array<int> $usageIndices
     */
    public function __construct(string $name, int $nameIndex, array $usageIndices)
    {
        $this->name = $name;
        $this->nameIndex = $nameIndex;
        $this->usageIndices = $usageIndices;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameIndex(): int
    {
        return $this->nameIndex;
    }

    /**
     * @return array<int>
     */
    public function getUsageIndices(): array
    {
        return $this->usageIndices;
    }
}
