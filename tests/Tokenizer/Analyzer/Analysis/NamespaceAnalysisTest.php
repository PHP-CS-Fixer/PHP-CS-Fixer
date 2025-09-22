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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis
 *
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class NamespaceAnalysisTest extends TestCase
{
    public function testFullName(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        self::assertSame('Full\NamespaceName', $analysis->getFullName());
    }

    public function testShortName(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        self::assertSame('NamespaceName', $analysis->getShortName());
        self::assertFalse($analysis->isGlobalNamespace());
    }

    public function testStartIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        self::assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        self::assertSame(2, $analysis->getEndIndex());
    }

    public function testScopeStartIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        self::assertSame(1, $analysis->getScopeStartIndex());
    }

    public function testScopeEndIndex(): void
    {
        $analysis = new NamespaceAnalysis('Full\NamespaceName', 'NamespaceName', 1, 2, 1, 10);
        self::assertSame(10, $analysis->getScopeEndIndex());
    }

    public function testGlobal(): void
    {
        $analysis = new NamespaceAnalysis('', '', 1, 2, 1, 10);
        self::assertTrue($analysis->isGlobalNamespace());
    }
}
