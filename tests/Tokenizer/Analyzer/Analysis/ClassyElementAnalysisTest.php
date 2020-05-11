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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ClassyElementAnalysis;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\ClassyElementAnalysis
 */
final class ClassyElementAnalysisTest extends TestCase
{
    public function testClassIndexAnalysis()
    {
        $analysis = new ClassyElementAnalysis(ClassyElementAnalysis::TYPE_CONSTANT, 1234);

        static::assertSame(1234, $analysis->getClassIndex());
    }

    public function testConstantAnalysis()
    {
        $analysis = new ClassyElementAnalysis(ClassyElementAnalysis::TYPE_CONSTANT, 1);

        static::assertTrue($analysis->isConstant());
        static::assertFalse($analysis->isMethod());
        static::assertFalse($analysis->isProperty());
    }

    public function testMethodAnalysis()
    {
        $analysis = new ClassyElementAnalysis(ClassyElementAnalysis::TYPE_METHOD, 1);

        static::assertFalse($analysis->isConstant());
        static::assertTrue($analysis->isMethod());
        static::assertFalse($analysis->isProperty());
    }

    public function testPropertyAnalysis()
    {
        $analysis = new ClassyElementAnalysis(ClassyElementAnalysis::TYPE_PROPERTY, 1);

        static::assertFalse($analysis->isConstant());
        static::assertFalse($analysis->isMethod());
        static::assertTrue($analysis->isProperty());
    }
}
