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
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2);
        $this->assertInstanceOf(StartEndTokenAwareAnalysis::class, $analysis);
    }

    public function testFullName()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2);
        $this->assertSame('Full\NamespaceName', $analysis->getFullName());
    }

    public function testAliased()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2);
        $this->assertFalse($analysis->isAliased());

        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', true, 1, 2);
        $this->assertTrue($analysis->isAliased());
    }

    public function testShortName()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2);
        $this->assertSame('NamespaceName', $analysis->getShortName());
    }

    public function testStartIndex()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2);
        $this->assertSame(1, $analysis->getStartIndex());
    }

    public function testEndIndex()
    {
        $analysis = new NamespaceUseAnalysis('Full\NamespaceName', 'NamespaceName', false, 1, 2);
        $this->assertSame(2, $analysis->getEndIndex());
    }
}
