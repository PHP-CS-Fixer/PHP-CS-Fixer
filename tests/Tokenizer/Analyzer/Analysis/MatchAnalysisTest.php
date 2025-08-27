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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class MatchAnalysisTest extends TestCase
{
    public function testMatchAnalysis(): void
    {
        $analysis = new MatchAnalysis(10, 11, 15, null);

        self::assertSame(10, $analysis->getIndex());
        self::assertSame(11, $analysis->getOpenIndex());
        self::assertSame(15, $analysis->getCloseIndex());
        self::assertNull($analysis->getDefaultAnalysis());
    }

    public function testMatchAnalysis2(): void
    {
        $defaultAnalysis = new DefaultAnalysis(45, 48);

        $analysis = new MatchAnalysis(22, 26, 290, $defaultAnalysis);

        self::assertSame(22, $analysis->getIndex());
        self::assertSame(26, $analysis->getOpenIndex());
        self::assertSame(290, $analysis->getCloseIndex());
        self::assertSame($defaultAnalysis, $analysis->getDefaultAnalysis());
    }
}
