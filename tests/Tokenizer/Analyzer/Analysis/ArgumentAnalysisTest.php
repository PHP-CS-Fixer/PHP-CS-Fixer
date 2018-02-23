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
        $this->assertSame('$name', $analysis->getName());
        $this->assertSame(1, $analysis->getNameIndex());
    }

    public function testDefault()
    {
        $analysis = new ArgumentAnalysis('$name', 1, 'default', null);
        $this->assertTrue($analysis->hasDefault());
        $this->assertSame('default', $analysis->getDefault());
    }

    public function testNoDefaultFound()
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        $this->assertFalse($analysis->hasDefault());
        $this->assertNull($analysis->getDefault());
    }

    public function testType()
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, new TypeAnalysis('string', 1, 4));
        $this->assertTrue($analysis->hasTypeAnalysis());
        $this->assertSame('string', $analysis->getTypeAnalysis()->getName());
        $this->assertSame(1, $analysis->getTypeAnalysis()->getStartIndex());
        $this->assertSame(4, $analysis->getTypeAnalysis()->getEndIndex());
    }

    public function testNoTypeFound()
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        $this->assertFalse($analysis->hasDefault());
        $this->assertNull($analysis->getDefault());
    }
}
