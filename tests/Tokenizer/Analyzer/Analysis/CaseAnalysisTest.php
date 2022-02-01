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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis
 *
 * @internal
 */
final class CaseAnalysisTest extends TestCase
{
    public function testCase(): void
    {
        $analysis = new CaseAnalysis(12, 20);

        static::assertSame(12, $analysis->getIndex());
        static::assertSame(20, $analysis->getColonIndex());
    }
}
