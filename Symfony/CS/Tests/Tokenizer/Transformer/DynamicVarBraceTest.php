<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer\Transformer;

use Symfony\CS\Tests\Tokenizer\AbstractTransformerTestBase;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class DynamicVarBraceTest extends AbstractTransformerTestBase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $tokens = Tokens::fromCode($source);

        $this->assertSame(
            count($expectedTokens),
            array_sum(array_map(
                function ($item) { return count($item); },
                $tokens->findGivenKind(array_map(function ($name) { return constant($name); }, $expectedTokens))
            ))
        );

        foreach ($expectedTokens as $index => $name) {
            $this->assertSame(constant($name), $tokens[$index]->getId());
            $this->assertSame($name, $tokens[$index]->getName());
        }
    }

    public function provideProcessCases()
    {
        return array(
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
            array('<?php if (1) {} class Foo{ } function bar{ }'),
        );
    }
}
