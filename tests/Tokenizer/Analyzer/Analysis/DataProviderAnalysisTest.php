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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer\Analysis;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DataProviderAnalysis;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\DataProviderAnalysis
 */
final class DataProviderAnalysisTest extends TestCase
{
    public function testGetName(): void
    {
        $analysis = new DataProviderAnalysis('Foo', 1, [2, 3]);
        static::assertSame('Foo', $analysis->getName());
    }

    public function testGetNameIndex(): void
    {
        $analysis = new DataProviderAnalysis('Foo', 1, [2, 3]);
        static::assertSame(1, $analysis->getNameIndex());
    }

    public function testGetUsageIndices(): void
    {
        $analysis = new DataProviderAnalysis('Foo', 1, [2, 3]);
        static::assertSame([2, 3], $analysis->getUsageIndices());
    }
}
