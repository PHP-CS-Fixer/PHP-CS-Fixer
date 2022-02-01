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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis
 */
final class NamespaceUseAnalysisTest extends TestCase
{
    public function testFullName(): void
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 11, 21, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame('Full\NamespaceName', $analysis->getFullName());
    }

    public function testAliased(): void
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 12, 22, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertFalse($analysis->isAliased());

        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', true, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertTrue($analysis->isAliased());
    }

    public function testShortName(): void
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame('NamespaceName', $analysis->getShortName());
    }

    public function testStartIndex(): void
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex(): void
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 72, NamespaceUseAnalysis::TYPE_CLASS);
        static::assertSame(72, $analysis->getEndIndex());
    }

    public function testTypeCheck(): void
    {
        $class = new NamespaceUseAnalysis('Foo\Bar', 'Baz', false, 1, 2, NamespaceUseAnalysis::TYPE_CLASS);
        $function = new NamespaceUseAnalysis('Foo\Bar', 'Baz', false, 1, 2, NamespaceUseAnalysis::TYPE_FUNCTION);
        $constant = new NamespaceUseAnalysis('Foo\Bar', 'Baz', false, 1, 2, NamespaceUseAnalysis::TYPE_CONSTANT);

        static::assertTrue($class->isClass());
        static::assertFalse($class->isFunction());
        static::assertFalse($class->isConstant());
        static::assertSame(NamespaceUseAnalysis::TYPE_CLASS, $class->getType());

        static::assertFalse($function->isClass());
        static::assertTrue($function->isFunction());
        static::assertFalse($function->isConstant());
        static::assertSame(NamespaceUseAnalysis::TYPE_FUNCTION, $function->getType());

        static::assertFalse($constant->isClass());
        static::assertFalse($constant->isFunction());
        static::assertTrue($constant->isConstant());
        static::assertSame(NamespaceUseAnalysis::TYPE_CONSTANT, $constant->getType());
    }
}
