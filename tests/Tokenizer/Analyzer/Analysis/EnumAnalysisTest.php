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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\EnumAnalysis;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\EnumAnalysis
 *
 * @internal
 */
final class EnumAnalysisTest extends TestCase
{
    public function testEnumAnalysis(): void
    {
        $analysis = new EnumAnalysis(10, 11, 15, []);

        static::assertSame(10, $analysis->getIndex());
        static::assertSame(11, $analysis->getOpenIndex());
        static::assertSame(15, $analysis->getCloseIndex());
        static::assertSame([], $analysis->getCases());
    }

    public function testEnumAnalysis2(): void
    {
        $caseAnalysis = new CaseAnalysis(20, 21);

        $analysis = new EnumAnalysis(15, 17, 190, [$caseAnalysis]);

        static::assertSame(15, $analysis->getIndex());
        static::assertSame(17, $analysis->getOpenIndex());
        static::assertSame(190, $analysis->getCloseIndex());
        static::assertSame([$caseAnalysis], $analysis->getCases());
    }
}
