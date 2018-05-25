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
     *
     * @dataProvider provideNamespaceUsesCases
     */
    public function testUsesFromTokens($code, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        $this->assertSame(serialize($expected), serialize($analyzer->getDeclarationsFromTokens($tokens)));
    }

    public function provideNamespaceUsesCases()
    {
        return [
            ['<?php // no uses', [], []],
            ['<?php use Foo\Bar;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Bar',
                    false,
                    1,
                    6
                ),
            ], [1]],
            ['<?php use Foo\Bar; use Foo\Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Bar',
                    false,
                    1,
                    6
                ),
                new NamespaceUseAnalysis(
                    'Foo\Baz',
                    'Baz',
                    false,
                    8,
                    13
                ),
            ], [1, 8]],
            ['<?php use \Foo\Bar;', [
                new NamespaceUseAnalysis(
                    '\Foo\Bar',
                    'Bar',
                    false,
                    1,
                    7
                ),
            ], [1]],
            ['<?php use Foo\Bar as Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Baz',
                    true,
                    1,
                    10
                ),
            ], [1]],
            ['<?php use Foo\Bar as Baz; use Foo\Buz as Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Baz',
                    true,
                    1,
                    10
                ),
                new NamespaceUseAnalysis(
                    'Foo\Buz',
                    'Baz',
                    true,
                    12,
                    21
                ),
            ], [1, 12]],

            // TODO: How to support these:

            // Function and constant imports
            // ['<?php use function My\count;', [], []],
            // ['<?php use function My\count as myCount;', [], []],
            // ['<?php use const My\Full\CONSTANT;', [], []],

            // Multiple imports on one line:
            // use My\Full\Classname as Another, My\Full\NSname;

            // PHP 7+ code
            // use some\namespace\{ClassA, ClassB, ClassC as C};
            // use function some\namespace\{fn_a, fn_b, fn_c};
            // use const some\namespace\{ConstA, ConstB, ConstC};
        ];
    }
}
