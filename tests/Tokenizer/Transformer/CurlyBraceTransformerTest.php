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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\CurlyBraceTransformer
 */
final class CurlyBraceTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = [])
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

    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases70
     * @requires PHP 7.0
     */
    public function testProcess70($source, array $expectedTokens = [])
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

    public function provideProcessCases()
    {
        return [
            [
                '<?php echo "This is {$great}";',
                [
                    5 => T_CURLY_OPEN,
                    7 => CT::T_CURLY_CLOSE,
                ],
            ],
            [
                '<?php $a = "a{$b->c()}d";',
                [
                    7 => T_CURLY_OPEN,
                    13 => CT::T_CURLY_CLOSE,
                ],
            ],
            [
                '<?php echo "I\'d like an {${beers::$ale}}\n";',
                [
                    5 => T_CURLY_OPEN,
                    7 => CT::T_DYNAMIC_VAR_BRACE_OPEN,
                    11 => CT::T_DYNAMIC_VAR_BRACE_CLOSE,
                    12 => CT::T_CURLY_CLOSE,
                ],
            ],

            [
                '<?php echo "This is ${great}";',
                [
                    5 => T_DOLLAR_OPEN_CURLY_BRACES,
                    7 => CT::T_DOLLAR_CLOSE_CURLY_BRACES,
                ],
            ],

            [
                '<?php $foo->{$bar};',
                [
                    3 => CT::T_DYNAMIC_PROP_BRACE_OPEN,
                    5 => CT::T_DYNAMIC_PROP_BRACE_CLOSE,
                ],
            ],

            [
                '<?php ${$bar};',
                [
                    2 => CT::T_DYNAMIC_VAR_BRACE_OPEN,
                    4 => CT::T_DYNAMIC_VAR_BRACE_CLOSE,
                ],
            ],

            [
                '<?php
                    echo $arr{$index};
                    echo $arr[$index];
                    if (1) {}
                ',
                [
                    5 => CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN,
                    7 => CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
                ],
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],

            [
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
            ],
            ['<?php if (1) {} class Foo{ } function bar(){ }'],
        ];
    }

    public function provideProcessCases70()
    {
        return [
            [
                '<?php use some\a\{ClassA, ClassB, ClassC as C};',
                [
                    7 => CT::T_GROUP_IMPORT_BRACE_OPEN,
                    19 => CT::T_GROUP_IMPORT_BRACE_CLOSE,
                ],
            ],
        ];
    }
}
