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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\StartEndTokenAwareAnalysis;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis
 */
final class TypeAnalysisTest extends TestCase
{
    public function testStartEndTokenAwareAnalysis()
    {
        $analysis = new TypeAnalysis('string', 1, 2);
        $this->assertInstanceOf(StartEndTokenAwareAnalysis::class, $analysis);
    }

    public function testName()
    {
        $analysis = new TypeAnalysis('string', 1, 2);
        $this->assertSame('string', $analysis->getName());
    }

    public function testStartIndex()
    {
        $analysis = new TypeAnalysis('string', 1, 2);
        $this->assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex()
    {
        $analysis = new TypeAnalysis('string', 1, 2);
        $this->assertSame(2, $analysis->getEndIndex());
    }

    /**
     * @dataProvider provideScalarCases
     */
    public function testScalar($type, $expected)
    {
        $analysis = new TypeAnalysis($type, 1, 2);
        $this->assertSame($expected, $analysis->isScalar());
    }

    public function provideScalarCases()
    {
        return [
            ['array', true],
            ['bool', true],
            ['int', true],
            ['iteratable', true],
            ['float', true],
            ['string', true],
            ['other', false],
        ];
    }
}
