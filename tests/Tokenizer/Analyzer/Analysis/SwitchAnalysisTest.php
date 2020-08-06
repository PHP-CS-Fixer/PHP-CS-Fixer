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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers  \PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis
 *
 * @internal
 */
final class SwitchAnalysisTest extends TestCase
{
    public function testCasesStart()
    {
        $analysis = new SwitchAnalysis(10, 20, []);
        static::assertSame(10, $analysis->getCasesStart());
    }

    public function testCasesEnd()
    {
        $analysis = new SwitchAnalysis(10, 20, []);
        static::assertSame(20, $analysis->getCasesEnd());
    }

    public function testCases()
    {
        $cases = [new CaseAnalysis(12), new CaseAnalysis(16)];

        $analysis = new SwitchAnalysis(10, 20, $cases);
        static::assertSame($cases, $analysis->getCases());
    }
}
