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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis
 *
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class ArgumentAnalysisTest extends TestCase
{
    public function testWithoutName(): void
    {
        $analysis = new ArgumentAnalysis(null, null, null, null);
        self::assertNull($analysis->getName());
        self::assertNull($analysis->getNameIndex());
    }

    public function testName(): void
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        self::assertSame('$name', $analysis->getName());
        self::assertSame(1, $analysis->getNameIndex());
    }

    public function testDefault(): void
    {
        $analysis = new ArgumentAnalysis('$name', 1, 'default', null);
        self::assertTrue($analysis->hasDefault());
        self::assertSame('default', $analysis->getDefault());
    }

    public function testNoDefaultFound(): void
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        self::assertFalse($analysis->hasDefault());
        self::assertNull($analysis->getDefault());
    }

    public function testType(): void
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, new TypeAnalysis('string', 1, 4));
        self::assertTrue($analysis->hasTypeAnalysis());
        self::assertSame('string', $analysis->getTypeAnalysis()->getName());
        self::assertSame(1, $analysis->getTypeAnalysis()->getStartIndex());
        self::assertSame(4, $analysis->getTypeAnalysis()->getEndIndex());
    }

    public function testNoTypeFound(): void
    {
        $analysis = new ArgumentAnalysis('$name', 1, null, null);
        self::assertFalse($analysis->hasTypeAnalysis());
        self::assertNull($analysis->getTypeAnalysis());
    }
}
