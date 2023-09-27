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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\BraceTransformer
 */
final class BraceTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                T_CURLY_OPEN,
                CT::T_CURLY_CLOSE,
                T_DOLLAR_OPEN_CURLY_BRACES,
                CT::T_DOLLAR_CLOSE_CURLY_BRACES,
                CT::T_DYNAMIC_PROP_BRACE_OPEN,
                CT::T_DYNAMIC_PROP_BRACE_CLOSE,
                CT::T_DYNAMIC_VAR_BRACE_OPEN,
                CT::T_DYNAMIC_VAR_BRACE_CLOSE,
                CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
                CT::T_GROUP_IMPORT_BRACE_OPEN,
                CT::T_GROUP_IMPORT_BRACE_CLOSE,
            ]
        );
    }

    public static function provideProcessCases(): iterable
    {
        yield 'curly open/close I' => [
            '<?php echo "This is {$great}";',
            [
                5 => T_CURLY_OPEN,
                7 => CT::T_CURLY_CLOSE,
            ],
        ];

        yield 'curly open/close II' => [
            '<?php $a = "a{$b->c()}d";',
            [
                7 => T_CURLY_OPEN,
                13 => CT::T_CURLY_CLOSE,
            ],
        ];

        yield 'dynamic var brace open/close' => [
            '<?php echo "I\'d like an {${beers::$ale}}\n";',
            [
                5 => T_CURLY_OPEN,
                7 => CT::T_DYNAMIC_VAR_BRACE_OPEN,
                11 => CT::T_DYNAMIC_VAR_BRACE_CLOSE,
                12 => CT::T_CURLY_CLOSE,
            ],
        ];

        yield 'dollar curly brace open/close' => [
            '<?php echo "This is ${great}";',
            [
                5 => T_DOLLAR_OPEN_CURLY_BRACES,
                7 => CT::T_DOLLAR_CLOSE_CURLY_BRACES,
            ],
        ];

        yield 'dynamic property brace open/close' => [
            '<?php $foo->{$bar};',
            [
                3 => CT::T_DYNAMIC_PROP_BRACE_OPEN,
                5 => CT::T_DYNAMIC_PROP_BRACE_CLOSE,
            ],
        ];

        yield 'dynamic variable brace open/close' => [
            '<?php ${$bar};',
            [
                2 => CT::T_DYNAMIC_VAR_BRACE_OPEN,
                4 => CT::T_DYNAMIC_VAR_BRACE_CLOSE,
            ],
        ];

        yield 'array index curly brace open/close' => [
            '<?php
                    echo $arr{$index};
                    echo $arr[$index];
                    if (1) {}
                ',
            [
                5 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                7 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
            ],
        ];

        yield 'array index curly brace open/close, after square index' => [
            '<?php $b = [1]{0};
                ',
            [
                8 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                10 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
            ],
        ];

        yield 'array index curly brace open/close, nested' => [
            '<?php
                    echo $nestedArray{$index}{$index2}[$index3]{$index4};
                ',
            [
                5 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                7 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
                8 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                10 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
                14 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                16 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
            ],
        ];

        yield 'array index curly brace open/close, repeated' => [
            '<?php
                    echo $array{0}->foo;
                    echo $collection->items{1}->property;
                ',
            [
                5 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                7 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
                17 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                19 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
            ],
        ];

        yield 'array index curly brace open/close, minimal' => [
            '<?php
                    echo [1]{0};
                    echo array(1){0};
                ',
            [
                7 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                9 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
                18 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                20 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
            ],
        ];

        yield 'mixed' => [
            '<?php echo "This is {$great}";
                    $a = "a{$b->c()}d";
                    echo "I\'d like an {${beers::$ale}}\n";
                ',
            [
                5 => T_CURLY_OPEN,
                7 => CT::T_CURLY_CLOSE,
                17 => T_CURLY_OPEN,
                23 => CT::T_CURLY_CLOSE,
                32 => T_CURLY_OPEN,
                34 => CT::T_DYNAMIC_VAR_BRACE_OPEN,
                38 => CT::T_DYNAMIC_VAR_BRACE_CLOSE,
                39 => CT::T_CURLY_CLOSE,
            ],
        ];

        yield 'do not touch' => [
            '<?php if (1) {} class Foo{ } function bar(){ }',
        ];

        yield 'dynamic property with string with variable' => [
            '<?php $object->{"set_{$name}"}(42);',
            [
                3 => CT::T_DYNAMIC_PROP_BRACE_OPEN,
                6 => T_CURLY_OPEN,
                8 => CT::T_CURLY_CLOSE,
                10 => CT::T_DYNAMIC_PROP_BRACE_CLOSE,
            ],
        ];

        yield 'group import' => [
            '<?php use some\a\{ClassA, ClassB, ClassC as C};',
            [
                7 => CT::T_GROUP_IMPORT_BRACE_OPEN,
                19 => CT::T_GROUP_IMPORT_BRACE_CLOSE,
            ],
        ];

        yield 'nested curly open + close' => [
            '<?php echo "{$foo->{"{$bar}"}}";',
            [
                4 => T_CURLY_OPEN,
                7 => CT::T_DYNAMIC_PROP_BRACE_OPEN,
                9 => T_CURLY_OPEN,
                11 => CT::T_CURLY_CLOSE,
                13 => CT::T_DYNAMIC_PROP_BRACE_CLOSE,
                14 => CT::T_CURLY_CLOSE,
            ],
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcess80Cases
     *
     * @requires PHP 8.0
     */
    public function testProcess80(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                T_CURLY_OPEN,
                CT::T_CURLY_CLOSE,
                T_DOLLAR_OPEN_CURLY_BRACES,
                CT::T_DOLLAR_CLOSE_CURLY_BRACES,
                CT::T_DYNAMIC_PROP_BRACE_OPEN,
                CT::T_DYNAMIC_PROP_BRACE_CLOSE,
                CT::T_DYNAMIC_VAR_BRACE_OPEN,
                CT::T_DYNAMIC_VAR_BRACE_CLOSE,
                CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
                CT::T_GROUP_IMPORT_BRACE_OPEN,
                CT::T_GROUP_IMPORT_BRACE_CLOSE,
            ]
        );
    }

    public static function provideProcess80Cases(): iterable
    {
        yield 'dynamic nullable property brace open/close' => [
            '<?php $foo?->{$bar};',
            [
                3 => CT::T_DYNAMIC_PROP_BRACE_OPEN,
                5 => CT::T_DYNAMIC_PROP_BRACE_CLOSE,
            ],
        ];
    }

    /**
     * @dataProvider provideNotDynamicClassConstantFetchCases
     */
    public function testNotDynamicClassConstantFetch(string $source): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        self::assertFalse(
            $tokens->isAnyTokenKindsFound(
                [
                    CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                    CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
                ]
            )
        );
    }

    public static function provideNotDynamicClassConstantFetchCases(): iterable
    {
        yield 'negatives' => [
            '<?php
                namespace B {$b = Z::B;};

                echo $c::{$static_method}();
                echo Foo::{$static_method}();

                echo Foo::${static_property};
                echo Foo::${$static_property};

                echo Foo::class;

                echo $foo::$bar;
                echo $foo::bar();
                echo foo()::A();

                {$z = A::C;}
            ',
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideDynamicClassConstantFetchCases
     *
     * @requires PHP 8.3
     */
    public function testDynamicClassConstantFetch(array $expectedTokens, string $source): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
        );
    }

    public static function provideDynamicClassConstantFetchCases(): iterable
    {
        yield 'simple' => [
            [
                5 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                7 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            '<?php echo Foo::{$bar};',
        ];

        yield 'static method var, string' => [
            [
                10 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                12 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            "<?php echo Foo::{\$static_method}(){'XYZ'};",
        ];

        yield 'long way of writing `Bar::class`' => [
            [
                5 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                7 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            "<?php echo Bar::{'class'};",
        ];

        yield 'variable variable wrapped, close tag' => [
            [
                5 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                10 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            '<?php echo Foo::{${$var}}?>',
        ];

        yield 'variable variable, comment' => [
            [
                5 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                8 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            '<?php echo Foo::{$$var}/* */;?>',
        ];

        yield 'static, self' => [
            [
                37 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                39 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
                46 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                48 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
                55 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                57 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            '<?php
                class Foo
                {
                    private const X = 1;

                    public function Bar($var): void
                    {
                        echo self::{$var};
                        echo static::{$var};
                        echo static::{"X"};
                    }
                }
            ',
        ];

        yield 'chained' => [
            [
                5 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                7 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
                9 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                11 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            "<?php echo Foo::{'BAR'}::{'BLA'}::{static_method}(1,2) ?>",
        ];

        yield 'mixed chain' => [
            [
                17 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                19 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
                21 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                23 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
                25 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_OPEN,
                27 => CT::T_DYNAMIC_CLASS_CONSTANT_FETCH_CURLY_BRACE_CLOSE,
            ],
            '<?php echo Foo::{\'static_method\'}()::{$$a}(){"const"}::{some_const}::{$other_const}::{$last_static_method}();',
        ];
    }
}
