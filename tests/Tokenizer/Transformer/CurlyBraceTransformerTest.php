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
        $this->doTest($source, $expectedTokens);
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
            // tests from CurlyCloseTest
            array(
                '<?php
                    echo "This is {$great}";
                    $a = "a{$b->c()}d";
                    echo "I\'d like an {${beers::$ale}}\n";
                ',
            ),
            // tests from DollarCloseCurlyBracesTest
            array('<?php echo "This is ${great}";'),
            // tests from DynamicPropBraceTest
            array('<?php $foo->{$bar};'),
            // extra tests
            array('<?php if (1) {} class Foo{ } function bar(){ }'),
        );
    }
}
