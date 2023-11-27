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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\MethodAnalysis;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\MethodAnalysis
 *
 * @internal
 */
final class MethodAnalysisTest extends TestCase
{
    public function testMethodAnalysis(): void
    {
        $analysis = new MethodAnalysis(T_PROTECTED, false, true, false);

        self::assertTrue($analysis->hasVisibilityDeclared());
        self::assertFalse($analysis->isPublicVisibilityDeclared());
        self::assertTrue($analysis->isProtectedVisibilityDeclared());
        self::assertFalse($analysis->isPrivateVisibilityDeclared());
        self::assertFalse($analysis->isStatic());
        self::assertTrue($analysis->isAbstract());
        self::assertFalse($analysis->isFinal());
    }
}
