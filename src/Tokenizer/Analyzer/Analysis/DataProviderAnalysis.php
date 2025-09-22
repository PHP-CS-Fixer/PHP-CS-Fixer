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

use PhpCsFixer\Console\Application;
use PhpCsFixer\Future;

/**
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DataProviderAnalysis
{
    private string $name;

    private int $nameIndex;

    /** @var non-empty-list<array{int, int}> */
    private array $usageIndices;

    /**
     * @param non-empty-list<array{int, int}> $usageIndices
     */
    public function __construct(string $name, int $nameIndex, array $usageIndices)
    {
        if ([] === $usageIndices || !array_is_list($usageIndices)) {
            Future::triggerDeprecation(new \InvalidArgumentException(\sprintf(
                'Parameter "usageIndices" should be a non-empty-list. This will be enforced in version %d.0.',
                Application::getMajorVersion() + 1
            )));
        }

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
     * @return non-empty-list<array{int, int}>
     */
    public function getUsageIndices(): array
    {
        return $this->usageIndices;
    }
}
