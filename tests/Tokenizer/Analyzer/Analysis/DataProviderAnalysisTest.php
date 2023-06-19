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
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\DataProviderAnalysis
 *
 * @internal
 */
final class DataProviderAnalysisTest extends TestCase
{
    public function testDataProviderAnalysis(): void
    {
        $analysis = new DataProviderAnalysis('Foo', 1, [2, 3]);

        self::assertSame('Foo', $analysis->getName());
        self::assertSame(1, $analysis->getNameIndex());
        self::assertSame([2, 3], $analysis->getUsageIndices());
    }
}
