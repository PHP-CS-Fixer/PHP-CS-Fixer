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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer
 */
final class NamespaceUsesAnalyzerTest extends TestCase
{
    /**
     * @param string $code
     * @param array  $expected
     * @param bool   $skipMultipleUseStatement
     *
     * @dataProvider provideNamespaceUsesCases
     */
    public function testUsesFromTokens($code, $expected, $skipMultipleUseStatement = true)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        static::assertSame(serialize($expected), serialize($analyzer->getDeclarationsFromTokens($tokens, $skipMultipleUseStatement)));
    }

    public function provideNamespaceUsesCases()
    {
        return [
            ['<?php // no uses', [], true],
            ['<?php use Foo\Bar;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Bar',
                    false,
                    1,
                    6,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], true],
            ['<?php use Foo\Bar; use Foo\Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Bar',
                    false,
                    1,
                    6,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
                new NamespaceUseAnalysis(
                    'Foo\Baz',
                    'Baz',
                    false,
                    8,
                    13,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], true],
            ['<?php use \Foo\Bar;', [
                new NamespaceUseAnalysis(
                    '\Foo\Bar',
                    'Bar',
                    false,
                    1,
                    7,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], true],
            ['<?php use Foo\Bar as Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Baz',
                    true,
                    1,
                    10,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], true],
            ['<?php use Foo\Bar as Baz; use Foo\Buz as Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Baz',
                    true,
                    1,
                    10,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
                new NamespaceUseAnalysis(
                    'Foo\Buz',
                    'Baz',
                    true,
                    12,
                    21,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], true],
            ['<?php use function My\count;', [
                new NamespaceUseAnalysis(
                    'My\count',
                    'count',
                    false,
                    1,
                    8,
                    NamespaceUseAnalysis::TYPE_FUNCTION
                ),
            ], true],
            ['<?php use function My\count as myCount;', [
                new NamespaceUseAnalysis(
                    'My\count',
                    'myCount',
                    true,
                    1,
                    12,
                    NamespaceUseAnalysis::TYPE_FUNCTION
                ),
            ], true],
            ['<?php use const My\Full\CONSTANT;', [
                new NamespaceUseAnalysis(
                    'My\Full\CONSTANT',
                    'CONSTANT',
                    false,
                    1,
                    10,
                    NamespaceUseAnalysis::TYPE_CONSTANT
                ),
            ], true],

            ['<?php use Foo\Bar, Foo\Baz;', [], true],
            ['<?php use Foo\Bar, Foo\Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Bar',
                    false,
                    1,
                    6,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
                new NamespaceUseAnalysis(
                    'Foo\Baz',
                    'Baz',
                    false,
                    7,
                    11,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], false],
            ['<?php use Foo\Bar as Baz, Some\ClassA as Alias,No\Space;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Baz',
                    true,
                    1,
                    10,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
                new NamespaceUseAnalysis(
                    'Some\ClassA',
                    'Alias',
                    true,
                    11,
                    19,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
                new NamespaceUseAnalysis(
                    'No\Space',
                    'Space',
                    false,
                    20,
                    23,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], false],
            ['<?php use function some\a\fn_a as alias, some\a\fn_b as b;', [
                new NamespaceUseAnalysis(
                    'some\a\fn_a',
                    'alias',
                    true,
                    1,
                    14,
                    NamespaceUseAnalysis::TYPE_FUNCTION
                ),
                new NamespaceUseAnalysis(
                    'some\a\fn_b',
                    'b',
                    true,
                    15,
                    25,
                    NamespaceUseAnalysis::TYPE_FUNCTION
                ),
            ], false],
            ['<?php use const some\a\ConstA as A, ConstB;', [
                new NamespaceUseAnalysis(
                    'some\a\ConstA',
                    'A',
                    true,
                    1,
                    14,
                    NamespaceUseAnalysis::TYPE_CONSTANT
                ),
                new NamespaceUseAnalysis(
                    'ConstB',
                    'ConstB',
                    false,
                    15,
                    17,
                    NamespaceUseAnalysis::TYPE_CONSTANT
                ),
            ], false],

            // TODO: How to support these:

            // PHP 7+ code
            // use some\namespace\{ClassA, ClassB, ClassC as C};
            // use function some\namespace\{fn_a, fn_b, fn_c};
            // use const some\namespace\{ConstA, ConstB, ConstC};
        ];
    }
}
