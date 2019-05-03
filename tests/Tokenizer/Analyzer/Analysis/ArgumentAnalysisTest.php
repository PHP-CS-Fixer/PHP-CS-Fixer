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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis
 */
final class ArgumentAnalysisTest extends TestCase
{
    public function testName()
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        static::assertSame('$name', $analysis->getName());
        static::assertSame(1, $analysis->getNameIndex());
    }

    public function testDefault()
    {
        $analysis = new ArgumentAnalysis('$name', 1, 'default', null);
        static::assertTrue($analysis->hasDefault());
        static::assertSame('default', $analysis->getDefault());
    }

    public function testNoDefaultFound()
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        static::assertFalse($analysis->hasDefault());
        static::assertNull($analysis->getDefault());
    }

    public function testType()
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, new TypeAnalysis('string', 1, 4));
        static::assertTrue($analysis->hasTypeAnalysis());
        static::assertSame('string', $analysis->getTypeAnalysis()->getName());
        static::assertSame(1, $analysis->getTypeAnalysis()->getStartIndex());
        static::assertSame(4, $analysis->getTypeAnalysis()->getEndIndex());
    }

    public function testNoTypeFound()
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        static::assertFalse($analysis->hasDefault());
        static::assertNull($analysis->getDefault());
    }
}
