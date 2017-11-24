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

use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer
 */
final class NamespaceUsesAnalyzerTest extends TestCase
{
    /**
     * @param string $code
     * @param array $expected
     * @dataProvider provideNamespaceUses
     */
    public function testUsesFromTokens($code, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        $this->assertSame($expected, $analyzer->getDeclarationsFromTokens($tokens));
    }

    public function provideNamespaceUses()
    {
        return [
            ['<?php // no uses', []],
            ['<?php use Foo\Bar;', [
                'Bar' => [
                    'fullName' => 'Foo\Bar',
                    'shortName' => 'Bar',
                    'aliased' => false,
                    'start' => 1,
                    'end' => 6,
                ]
            ]],
            ['<?php use Foo\Bar; use Foo\Baz;', [
                'Bar' => [
                    'fullName' => 'Foo\Bar',
                    'shortName' => 'Bar',
                    'aliased' => false,
                    'start' => 1,
                    'end' => 6,
                ],
                'Baz' => [
                    'fullName' => 'Foo\Baz',
                    'shortName' => 'Baz',
                    'aliased' => false,
                    'start' => 8,
                    'end' => 13,
                ]
            ]],
            ['<?php use \Foo\Bar;', [
                'Bar' => [
                    'fullName' => '\Foo\Bar',
                    'shortName' => 'Bar',
                    'aliased' => false,
                    'start' => 1,
                    'end' => 7,
                ]
            ]],
            ['<?php use Foo\Bar as Baz;', [
                'Baz' => [
                    'fullName' => 'Foo\Bar',
                    'shortName' => 'Baz',
                    'aliased' => true,
                    'start' => 1,
                    'end' => 10,
                ],
            ]],
            ['<?php use Foo\Bar as Baz; use Foo\Buz as Baz;', [
                'Baz' => [
                    'fullName' => 'Foo\Buz',
                    'shortName' => 'Baz',
                    'aliased' => true,
                    'start' => 12,
                    'end' => 21,
                ],
            ]],
        ];
    }
}