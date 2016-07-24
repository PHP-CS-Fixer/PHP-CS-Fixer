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

use PhpCsFixer\Test\AbstractTransformerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class CurlyBraceTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                'T_CURLY_OPEN',
                'CT_CURLY_CLOSE',
                'T_DOLLAR_OPEN_CURLY_BRACES',
                'CT_DOLLAR_CLOSE_CURLY_BRACES',
                'CT_DYNAMIC_PROP_BRACE_OPEN',
                'CT_DYNAMIC_PROP_BRACE_CLOSE',
                'CT_DYNAMIC_VAR_BRACE_OPEN',
                'CT_DYNAMIC_VAR_BRACE_CLOSE',
                'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                'CT_GROUP_IMPORT_BRACE_OPEN',
                'CT_GROUP_IMPORT_BRACE_CLOSE',
            )
        );
    }

    /**
     * @dataProvider provideProcessCases70
     * @requires PHP 7.0
     */
    public function testProcess70($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                'T_CURLY_OPEN',
                'CT_CURLY_CLOSE',
                'T_DOLLAR_OPEN_CURLY_BRACES',
                'CT_DOLLAR_CLOSE_CURLY_BRACES',
                'CT_DYNAMIC_PROP_BRACE_OPEN',
                'CT_DYNAMIC_PROP_BRACE_CLOSE',
                'CT_DYNAMIC_VAR_BRACE_OPEN',
                'CT_DYNAMIC_VAR_BRACE_CLOSE',
                'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                'CT_GROUP_IMPORT_BRACE_OPEN',
                'CT_GROUP_IMPORT_BRACE_CLOSE',
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php echo "This is {$great}";',
                array(
                    5 => 'T_CURLY_OPEN',
                    7 => 'CT_CURLY_CLOSE',
                ),
            ),
            array(
                '<?php $a = "a{$b->c()}d";',
                array(
                    7 => 'T_CURLY_OPEN',
                    13 => 'CT_CURLY_CLOSE',
                ),
            ),
            array(
                '<?php echo "I\'d like an {${beers::$ale}}\n";',
                array(
                    5 => 'T_CURLY_OPEN',
                    7 => 'CT_DYNAMIC_VAR_BRACE_OPEN',
                    11 => 'CT_DYNAMIC_VAR_BRACE_CLOSE',
                    12 => 'CT_CURLY_CLOSE',
                ),
            ),

            array(
                '<?php echo "This is ${great}";',
                array(
                    5 => 'T_DOLLAR_OPEN_CURLY_BRACES',
                    7 => 'CT_DOLLAR_CLOSE_CURLY_BRACES',
                ),
            ),

            array(
                '<?php $foo->{$bar};',
                array(
                    3 => 'CT_DYNAMIC_PROP_BRACE_OPEN',
                    5 => 'CT_DYNAMIC_PROP_BRACE_CLOSE',
                ),
            ),

            array(
                '<?php ${$bar};',
                array(
                    2 => 'CT_DYNAMIC_VAR_BRACE_OPEN',
                    4 => 'CT_DYNAMIC_VAR_BRACE_CLOSE',
                ),
            ),

            array(
                '<?php
                    echo $arr{$index};
                    echo $arr[$index];
                    if (1) {}
                ',
                array(
                    5 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    7 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php
                    echo $nestedArray{$index}{$index2}[$index3]{$index4};
                ',
                array(
                    5 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    7 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                    8 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    10 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                    14 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    16 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php
                    echo $array{0}->foo;
                    echo $collection->items{1}->property;
                ',
                array(
                    5 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    7 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                    17 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    19 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php
                    echo [1]{0};
                    echo array(1){0};
                ',
                array(
                    7 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    9 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                    18 => 'CT_ARRAY_INDEX_CURLY_BRACE_OPEN',
                    20 => 'CT_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                ),
            ),

            array(
                '<?php echo "This is {$great}";
                    $a = "a{$b->c()}d";
                    echo "I\'d like an {${beers::$ale}}\n";
                ',
                array(
                    5 => 'T_CURLY_OPEN',
                    7 => 'CT_CURLY_CLOSE',
                    17 => 'T_CURLY_OPEN',
                    23 => 'CT_CURLY_CLOSE',
                    32 => 'T_CURLY_OPEN',
                    34 => 'CT_DYNAMIC_VAR_BRACE_OPEN',
                    38 => 'CT_DYNAMIC_VAR_BRACE_CLOSE',
                    39 => 'CT_CURLY_CLOSE',
                ),
            ),
            array('<?php if (1) {} class Foo{ } function bar(){ }'),
        );
    }

    public function provideProcessCases70()
    {
        return array(
            array(
                '<?php use some\a\{ClassA, ClassB, ClassC as C};',
                array(
                    7 => 'CT_GROUP_IMPORT_BRACE_OPEN',
                    19 => 'CT_GROUP_IMPORT_BRACE_CLOSE',
                ),
            ),
        );
    }
}
