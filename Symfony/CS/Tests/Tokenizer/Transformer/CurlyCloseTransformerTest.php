<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer\Transformer;

use Symfony\CS\Tests\Tokenizer\AbstractTransformerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class CurlyCloseTransformerTest extends AbstractTransformerTestBase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->makeTest($source, $expectedTokens);
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
                    7  => 'T_CURLY_OPEN',
                    13 => 'CT_CURLY_CLOSE',
                ),
            ),
            array(
                '<?php echo "I\'d like an {${beers::$ale}}\n";',
                array(
                    5  => 'T_CURLY_OPEN',
                    12 => 'CT_CURLY_CLOSE',
                ),
            ),
        );
    }
}
