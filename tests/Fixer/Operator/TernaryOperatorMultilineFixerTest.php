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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Fixer\Operator\TernaryOperatorMultilineFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gwalchmei <darainas2@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\RemoveCommentsFixer
 */
final class TernaryOperatorMultilineFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideNoMultilineFixCases
     */
    public function testNoMultilineFix($expected, $input = null)
    {
        $this->fixer->configure(['strategy' => TernaryOperatorMultilineFixer::STRATEGY_NO_MULTI_LINE]);
        $this->doTest($expected, $input);
    }

    public function provideNoMultilineFixCases()
    {
        return [
            ['<?php $x = isset($a) ? $a : 0;'],
            [
                '<?php
$x = isset($a) ? $a : 0;',
                '<?php
$x = isset($a)
    ? $a : 0;',
            ],
            [
                '<?php
$x = isset($a) ? $a : 0;',
                '<?php
$x = isset($a) ?
    $a : 0;',
            ],
            [
                '<?php
$x = isset($a) ? $a : 0;',
                '<?php
$x = isset($a) ? $a
    : 0;',
            ],
            [
                '<?php
$x = isset($a) ? $a : 0;',
                '<?php
$x = isset($a) ? $a :
    0;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideOperatorsAtEndFixCases
     */
    public function testOperatorsAtEndFix($expected, $input = null)
    {
        $this->fixer->configure(['strategy' => TernaryOperatorMultilineFixer::STRATEGY_OPERATORS_AT_END]);
        $this->doTest($expected, $input);
    }

    public function provideOperatorsAtEndFixCases()
    {
        return [
            [
                '<?php
$x = isset($a) ?
    $a :
    0;',
            ],
            [
                '<?php
return is_array($a) ?
    array_map(function ($i) { return $i * 2; }, $a) :
    NULL;',
            ],
            [
                '<?php
$x = $this->foo(1, 0,
    true ?
        0 :
        1
);',
            ],
            ['<?php $x = $foo ?: 1;'],
            [
                '<?php
$x = isset($a) ?
    $a :
    0;',
                '<?php
$x = isset($a) ? $a: 0;',
            ],
            [
                '<?php
$x = isset($a) ?
    $a :
    0;',
                '<?php
$x = isset($a)
    ? $a: 0;',
            ],
            [
                '<?php
$x = isset($a) ?
    $a :
    0;',
                '<?php
$x = isset($a) ?
    $a: 0;',
            ],
            [
                '<?php
$x = isset($a) ?
    $a :
    0;',
                '<?php
$x = isset($a) ? $a
    : 0;',
            ],
            [
                '<?php
$x = isset($a) ?
    $a :
    0;',
                '<?php
$x = isset($a) ? $a :
    0;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideOperatorsAtBeginningFixCases
     */
    public function testOperatorsAtBeginningFix($expected, $input = null)
    {
        $this->fixer->configure(['strategy' => TernaryOperatorMultilineFixer::STRATEGY_OPERATORS_AT_BEGINNING]);
        $this->doTest($expected, $input);
    }

    public function provideOperatorsAtBeginningFixCases()
    {
        return [
            // no fix needed
            [
                '<?php
$x = isset($a)
    ? $a
    : 0;',
            ],
            [
                '<?php
return is_array($a)
    ? array_map(function ($i) { return $i * 2; }, $a)
    : NULL;',
            ],
            [
                '<?php
$x = $this->foo(1, 0,
    true
        ? 0
        : 1
);',
            ],
            [
                '<?php
$x = isset($a)
    ? $a
    : isset($b)
        ? $b
        : 0;',
            ],
            [
                '<?php
$this->foo(
    isset($a)
        ? $a
        : 0,
    isset($b)
        ? $b
        : 0
);',
            ],
            ['<?php $x = $foo ?: 1;'],
            // fix needed
            [
                '<?php
$x = isset($a)
    ? $a
    : 0;',
                '<?php
$x = isset($a) ? $a : 0;',
            ],
            [
                '<?php
$x = isset($a)
    ? $a
    : 0;',
                '<?php
$x = isset($a)
    ? $a : 0;',
            ],
            [
                '<?php
$x = isset($a)
    ? $a
    : 0;',
                '<?php
$x = isset($a) ?
    $a : 0;',
            ],
            [
                '<?php
$x = isset($a)
    ? $a
    : 0;',
                '<?php
$x = isset($a) ? $a
    : 0;',
            ],
            [
                '<?php
$x = isset($a)
    ? $a
    : 0;',
                '<?php
$x = isset($a) ? $a :
    0;',
            ],
            // failing case
            [
                '<?php
$x = isset($a)
    ? isset($b)
        ? $a + $b
        : $a
    : 0;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideIgnoreSingleLineFixCases
     */
    public function testIgnoreSingleLineFix($expected, $input = null)
    {
        $this->fixer->configure(['strategy' => TernaryOperatorMultilineFixer::STRATEGY_OPERATORS_AT_BEGINNING, 'ignore-single-line' => true]);
        $this->doTest($expected, $input);
    }

    public function provideIgnoreSingleLineFixCases()
    {
        return [
            ['<?php $x = isset($a) ? $a : 0;'],
            [
                '<?php
$x = isset($a) ? $a : 0;
$y = isset($b)
    ? $b
    : 1;',
            ],
            [
                '<?php
$x = isset($a)
    ? $a
    : 0;
$x = isset($a) ? $a : 0;',
                '<?php
$x = isset($a) ?
    $a : 0;
$x = isset($a) ? $a : 0;',
            ],
        ];
    }
}
