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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis
 *
 * @internal
 */
final class SwitchAnalysisTest extends TestCase
{
    public function testSwitchAnalysis(): void
    {
        $analysis = new SwitchAnalysis(10, 11, 15, [], null);

        self::assertSame(10, $analysis->getIndex());
        self::assertSame(11, $analysis->getOpenIndex());
        self::assertSame(15, $analysis->getCloseIndex());
        self::assertSame([], $analysis->getCases());
        self::assertNull($analysis->getDefaultAnalysis());
    }

    public function testSwitchAnalysis2(): void
    {
        $caseAnalysis = new CaseAnalysis(20, 21);
        $defaultAnalysis = new DefaultAnalysis(45, 48);

        $analysis = new SwitchAnalysis(15, 17, 190, [$caseAnalysis], $defaultAnalysis);

        self::assertSame(15, $analysis->getIndex());
        self::assertSame(17, $analysis->getOpenIndex());
        self::assertSame(190, $analysis->getCloseIndex());
        self::assertSame([$caseAnalysis], $analysis->getCases());
        self::assertSame($defaultAnalysis, $analysis->getDefaultAnalysis());
    }
}
