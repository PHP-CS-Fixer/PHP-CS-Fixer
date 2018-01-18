<?php

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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\FunctionReturnTypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\StartEndTokenAwareAnalysis;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\FunctionReturnTypeAnalysis
 */
final class FunctionReturnTypeAnalysisTest extends TestCase
{
    public function testStartEndTokenAwareAnalysis()
    {
        $analysis = new FunctionReturnTypeAnalysis('string', 1, 2);
        $this->assertInstanceOf(StartEndTokenAwareAnalysis::class, $analysis);
    }

    public function testType()
    {
        $analysis = new FunctionReturnTypeAnalysis('string', 1, 2);
        $this->assertSame('string', $analysis->getType());
    }

    public function testStartIndex()
    {
        $analysis = new FunctionReturnTypeAnalysis('string', 1, 2);
        $this->assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex()
    {
        $analysis = new FunctionReturnTypeAnalysis('string', 1, 2);
        $this->assertSame(2, $analysis->getEndIndex());
    }
}
