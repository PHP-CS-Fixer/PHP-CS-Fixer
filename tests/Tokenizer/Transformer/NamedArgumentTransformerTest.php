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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\NamedArgumentTransformer
 */
final class NamedArgumentTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     *
     * @requires PHP 8.0
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens);
    }

    public static function provideProcessCases(): iterable
    {
        yield 'function call' => [
            '<?php foo(test: 1);',
            [
                3 => CT::T_NAMED_ARGUMENT_NAME,
                4 => CT::T_NAMED_ARGUMENT_COLON,
            ],
        ];

        yield 'dynamic function 2x' => [
            '<?php $foo(foo: 1, bar: 2, 3,);',
            [
                3 => CT::T_NAMED_ARGUMENT_NAME,
                4 => CT::T_NAMED_ARGUMENT_COLON,
                9 => CT::T_NAMED_ARGUMENT_NAME,
                10 => CT::T_NAMED_ARGUMENT_COLON,
            ],
        ];

        yield 'method' => [
            '<?php
                class Bar {
                    public function a($foo){}
                }

                $foo = new Bar();
                $foo->a(foo: 1);
            ',
            [
                36 => CT::T_NAMED_ARGUMENT_NAME,
                37 => CT::T_NAMED_ARGUMENT_COLON,
            ],
        ];

        yield 'nested' => [
            '<?php
    foo(test: static function() {
        bar(test: 1);
    },);
',
            [
                4 => CT::T_NAMED_ARGUMENT_NAME,
                5 => CT::T_NAMED_ARGUMENT_COLON,
                17 => CT::T_NAMED_ARGUMENT_NAME,
                18 => CT::T_NAMED_ARGUMENT_COLON,
            ],
        ];
    }

    /**
     * @dataProvider provideDoNotChangeCases
     */
    public function testDoNotChange(string $source): void
    {
        static::assertNotChange($source);
    }

    public static function provideDoNotChangeCases(): iterable
    {
        yield 'switch/case/constants' => [
            '<?php
                define(\'FOO\', 123);
                define(\'BAR\', 123);

                $a = $guard = 123;

                switch($a) {
                    case FOO:
                        echo 456;
                        break;

                    case 3 + FOO:
                        echo 789;
                        break;

                    case ($guard ? BAR : 2):
                        echo 456;
                        break;
                }

                foo(1 , $a3 ? BAR : 2);
                $a1 = [1, BAR ? 1 : 2];
                $a2 = [1, (BAR) ? 1 : 2];
            ',
        ];

        yield 'goto' => [
            '<?php
                define(\'FOO\', 123);
                $guard = 1;

                {
                    beginning:
                    echo $guard ? 1 + FOO : 2;
                    echo $guard ? 1 : 2;
                }
            ',
        ];

        yield 'return type' => ['<?php function foo(): array { return []; }'];
    }

    private static function assertNotChange(string $source): void
    {
        Tokens::clearCache();

        foreach (Tokens::fromCode($source) as $token) {
            static::assertFalse($token->isGivenKind([
                CT::T_NAMED_ARGUMENT_NAME,
                CT::T_NAMED_ARGUMENT_COLON,
            ]));
        }
    }
}
