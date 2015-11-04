<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Adam Marczuk <adam@marczuk.info>
 */
class ArrayElementSpaceFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider testFixProvider
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function testFixProvider()
    {
        return array(
            //old style array
            array(
                '<?php $x = array(1, "2", 3);',
                '<?php $x = array( 1 ,  "2",3);',
            ),
            //short array
            array(
                '<?php $x = [1, "2", 3, $y];',
                '<?php $x = [ 1 ,  "2",3 ,$y];',
            ),
            // associative array (old)
            array(
                '<?php $x = array(\'a\' => $a, "b" => \'b\', 3 => $this->foo(), \'d\' => 30);',
                '<?php $x = array(  \'a\' =>$a , "b"=>  \'b\',3=>$this->foo()  , \'d\' =>30  );',
            ),
            // associative array (short)
            array(
                '<?php $x = [\'a\' => $a, "b" => \'b\', 3 => $this->foo(), \'d\' => 30];',
                '<?php $x = [  \'a\' =>$a , "b"=>  \'b\',3=>$this->foo()  , \'d\' =>30  ];',
            ),
            // nested arrays
            array(
                '<?php $x = [\'a\' => $a, "b" => \'b\', 3 => [5, 6, 7], \'d\' => array(1, 2, 3, 4)];',
                '<?php $x = [  \'a\' =>$a , "b"=>  \'b\',3=> [   5 ,6,  7 ]  , \'d\'=>  array(  1,  2,3 ,4)  ];',
            ),
            // multi line array
            array(
                '<?php $x = [\'a\' => $a,
                    "b" => \'b\',
                    3 => $this->foo(), 
                    \'d\' => 30];',
                '<?php $x = [  \'a\' =>$a ,
                    "b"=>  
                \'b\',
                    3=>$this->foo()  , 
                    \'d\' =>30  ];',
            ),
        );
    }
}
