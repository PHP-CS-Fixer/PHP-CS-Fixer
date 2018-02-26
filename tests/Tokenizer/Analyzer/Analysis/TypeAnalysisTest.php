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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\StartEndTokenAwareAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;

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
     * @dataProvider provideReservedCases
     *
     * @param mixed $type
     * @param mixed $expected
     */
    public function testReserved($type, $expected)
    {
        $analysis = new TypeAnalysis($type, 1, 2);
        $this->assertSame($expected, $analysis->isReservedType());
    }

    public function provideReservedCases()
    {
        return [
            ['array', true],
            ['bool', true],
            ['callable', true],
            ['int', true],
            ['iteratable', true],
            ['float', true],
            ['mixed', true],
            ['numeric', true],
            ['object', true],
            ['resource', true],
            ['self', true],
            ['string', true],
            ['void', true],
            ['other', false],
        ];
    }
}
