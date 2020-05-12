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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\MatchAnalysis;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\MatchAnalysis
 *
 * @internal
 */
final class MatchAnalysisTest extends TestCase
{
    public function testMatchAnalysis(): void
    {
        $analysis = new MatchAnalysis(10, 11, 15, null);

        static::assertSame(10, $analysis->getIndex());
        static::assertSame(11, $analysis->getOpenIndex());
        static::assertSame(15, $analysis->getCloseIndex());
        static::assertNull($analysis->getDefaultAnalysis());
    }

    public function testMatchAnalysis2(): void
    {
        $defaultAnalysis = new DefaultAnalysis(45, 48);

        $analysis = new MatchAnalysis(22, 26, 290, $defaultAnalysis);

        static::assertSame(22, $analysis->getIndex());
        static::assertSame(26, $analysis->getOpenIndex());
        static::assertSame(290, $analysis->getCloseIndex());
        static::assertSame($defaultAnalysis, $analysis->getDefaultAnalysis());
    }
}
