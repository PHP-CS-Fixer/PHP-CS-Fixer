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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SapcePossum
 *
 * @internal
 */
final class SwitchCaseCurlyBraceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                    switch($a) {
                        case 0; '.'
                        '.'
                        case 1 : /**/
                        '.'
                        case 2 :  // a
                        '.'
                        default : '.'
                            $a = function() {};
                        '.'
                    }
                ',
                '<?php
                    switch($a) {
                        case 0; {
                        }
                        case 1 : /**/{
                        }
                        case 2 : {{{ // a
                        }}}
                        default : {
                            $a = function() {};
                        }
                    }
                ',
            ),
            array(
'<?php switch ($foo): ?>
<?php case 1:  ?>
        ...
<?php  endswitch ?>
                ',
'<?php switch ($foo): ?>
<?php case 1: { ?>
        ...
<?php } endswitch ?>
                ',
            ),
        );
    }
}
