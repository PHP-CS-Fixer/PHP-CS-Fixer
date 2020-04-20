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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ClassAnalysis;

/**
 * @author Pol Dellaiera <pol.dellaiera@protonmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\ClassAnalysis
 */
final class ClassAnalysisTest extends TestCase
{
    /**
     * @dataProvider provideClassAnalysisCases
     *
     * @param int   $start
     * @param int   $class
     * @param int   $open
     * @param array $extends
     * @param array $implements
     * @param bool  $anonymous
     */
    public function testClassAnalysis($start, $class, $open, $extends, $implements, $anonymous)
    {
        $analysis = new ClassAnalysis($start, $class, $open, $extends, $implements, $anonymous);

        static::assertSame(
            [
                'start' => $start,
                'classy' => $class,
                'open' => $open,
                'extends' => $extends,
                'implements' => $implements,
                'anonymous' => $anonymous,
            ],
            $analysis->toArray()
        );
    }

    public function provideClassAnalysisCases()
    {
        return [
            [1, 2, 3, [], [], true],
            [3, 2, 1, ['foo' => 'bar'], ['bar' => 'foo'], false],
        ];
    }
}
