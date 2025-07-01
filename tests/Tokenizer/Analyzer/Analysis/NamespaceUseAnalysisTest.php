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
        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, false, 11, 21);
        self::assertSame('Full\NamespaceName', $analysis->getFullName());
    }

    public function testAliased(): void
    {
        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, false, 12, 22);
        self::assertFalse($analysis->isAliased());

        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', true, false, 1, 2);
        self::assertTrue($analysis->isAliased());
    }

    public function testIsInMulti(): void
    {
        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, false, 12, 22);
        self::assertFalse($analysis->isInMulti());

        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, true, 1, 20, 5, 10);
        self::assertTrue($analysis->isInMulti());
    }

    public function testChunkStartIndexAndChunkEndIndexAreRequiredWhenIsInMulti(): void
    {
        $this->expectException(\LogicException::class);

        new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, true, 1, 20);
    }

    public function testShortName(): void
    {
        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, false, 1, 2);
        self::assertSame('NamespaceName', $analysis->getShortName());
    }

    public function testStartIndex(): void
    {
        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, false, 1, 2);
        self::assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex(): void
    {
        $analysis = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Full\NamespaceName', 'NamespaceName', false, false, 1, 72);
        self::assertSame(72, $analysis->getEndIndex());
    }

    public function testTypeCheck(): void
    {
        $class = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Foo\Bar', 'Baz', false, false, 1, 2);
        $function = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_FUNCTION, 'Foo\Bar', 'Baz', false, false, 1, 2);
        $constant = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CONSTANT, 'Foo\Bar', 'Baz', false, false, 1, 2);

        self::assertTrue($class->isClass());
        self::assertFalse($class->isFunction());
        self::assertFalse($class->isConstant());
        self::assertSame(NamespaceUseAnalysis::TYPE_CLASS, $class->getType());

        self::assertFalse($function->isClass());
        self::assertTrue($function->isFunction());
        self::assertFalse($function->isConstant());
        self::assertSame(NamespaceUseAnalysis::TYPE_FUNCTION, $function->getType());

        self::assertFalse($constant->isClass());
        self::assertFalse($constant->isFunction());
        self::assertTrue($constant->isConstant());
        self::assertSame(NamespaceUseAnalysis::TYPE_CONSTANT, $constant->getType());
    }
}
