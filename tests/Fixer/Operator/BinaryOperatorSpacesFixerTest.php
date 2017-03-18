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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class BinaryOperatorSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            // assignment tests
            array(
                '<?php $a = 1 //
                    || 2;
                ',
            ),
            array(
                '<?php $a =
                    2;',
            ),
            array(
                '<?php $a = 7;',
            ),
            array(
                '<?php $a = 7;',
                '<?php $a=7;',
            ),
            array(
                '<?php $a = 7;',
                '<?php $a= 7;',
            ),
            array(
                '<?php $a = 7;',
                '<?php $a =7;',
            ),
            array(
                '<?php $a =   7;',
            ),
            array(
                '<?php $a   = 7;',
            ),
            array(
                '<?php $a   =   7;',
            ),
            // DOUBLE_ARROW tests
            array(
                '<?php $a = array("b" => "c", );',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=> "c", );',
            ),
            array(
                '<?php $a = array("b" =>   "c", );',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=>"c", );',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b" =>"c", );',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=> "c", );',
            ),
            array(
                '<?php $a = array("b" =>   "c", );',
            ),
            array(
                '<?php $a = array("b"   => "c", );',
            ),
            array(
                '<?php $a = array("b"   =>   "c", );',
            ),
            // ignore for declare_equal_normalize
            array(
                '<?php declare(ticks=1);',
            ),
            array(
                '<?php declare(ticks =1);',
            ),
            array(
                '<?php declare(ticks= 1);',
            ),
            array(
                '<?php declare(ticks = 1);',
            ),
            array(
                '<?php declare(ticks =  1);',
            ),
            array(
                '<?php $a = 1;declare(ticks =  1);$b = 1;',
                '<?php $a=1;declare(ticks =  1);$b=1;',
            ),
        );
    }
}
