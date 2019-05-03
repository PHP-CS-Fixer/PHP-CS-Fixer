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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\StartEndTokenAwareAnalysis;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis
 */
final class NamespaceUseAnalysisTest extends TestCase
{
    public function testStartEndTokenAwareAnalysis()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertInstanceOf(StartEndTokenAwareAnalysis::class, $analysis);
    }

    public function testFullName()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame('Full\NamespaceName', $analysis->getFullName());
    }

    public function testAliased()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertFalse($analysis->isAliased());

        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', true, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertTrue($analysis->isAliased());
    }

    public function testShortName()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame('NamespaceName', $analysis->getShortName());
    }

    public function testStartIndex()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame(2, $analysis->getEndIndex());
    }

    public function testTypeCheck()
    {
        $class = new NamespaceUseAnalysis('Foo\Bar', 'Baz', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        $function = new NamespaceUseAnalysis('Foo\Bar', 'Baz', false, 1, 2, NamespaceUseAnalysis::TYPE_FUNCTION);
        $constant = new NamespaceUseAnalysis('Foo\Bar', 'Baz', false, 1, 2, NamespaceUseAnalysis::TYPE_CONSTANT);

        static::assertTrue($class->isClass());
        static::assertFalse($class->isFunction());
        static::assertFalse($class->isConstant());

        static::assertFalse($function->isClass());
        static::assertTrue($function->isFunction());
        static::assertFalse($function->isConstant());

        static::assertFalse($constant->isClass());
        static::assertFalse($constant->isFunction());
        static::assertTrue($constant->isConstant());
    }
}
