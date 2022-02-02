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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis
 */
final class NamespaceAnalysisTest extends TestCase
{
    public function testFullName(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        static::assertSame('Full\NamespaceName', $analysis->getFullName());
    }

    public function testShortName(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        static::assertSame('NamespaceName', $analysis->getShortName());
        static::assertFalse($analysis->isGlobalNamespace());
    }

    public function testStartIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        static::assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        static::assertSame(2, $analysis->getEndIndex());
    }

    public function testScopeStartIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        static::assertSame(1, $analysis->getScopeStartIndex());
    }

    public function testScopeEndIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        static::assertSame(10, $analysis->getScopeEndIndex());
    }

    public function testGlobal(): void
    {
        $analysis = new NamespaceAnalysis('', '', 1, 2, 1, 10);
        static::assertTrue($analysis->isGlobalNamespace());
    }
}
