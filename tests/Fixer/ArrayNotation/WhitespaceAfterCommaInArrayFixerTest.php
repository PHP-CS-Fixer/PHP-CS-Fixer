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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Adam Marczuk <adam@marczuk.info>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer
 */
final class WhitespaceAfterCommaInArrayFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider testFixProvider
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testFixProvider()
    {
        return [
            //old style array
            [
                '<?php $x = array( 1 , "2", 3);',
                '<?php $x = array( 1 ,"2",3);',
            ],
            //old style array with comments
            [
                '<?php $x = array /* comment */ ( 1 ,  "2", 3);',
                '<?php $x = array /* comment */ ( 1 ,  "2",3);',
            ],

            //short array
            [
                '<?php $x = [ 1 ,  "2", 3 , $y];',
                '<?php $x = [ 1 ,  "2",3 ,$y];',
            ],
            // don't change function calls
            [
                '<?php $x = [1, "2", getValue(1,2  ,3 ) , $y];',
                '<?php $x = [1, "2",getValue(1,2  ,3 ) ,$y];',
            ],
            // don't change function declarations
            [
                '<?php $x = [1,  "2", function( $x ,$y) { return $x + $y; }, $y];',
                '<?php $x = [1,  "2",function( $x ,$y) { return $x + $y; },$y];',
            ],
            // don't change function declarations but change array inside
            [
                '<?php $x = [1,  "2", "c" => function( $x ,$y) { return [$x , $y]; }, $y ];',
                '<?php $x = [1,  "2","c" => function( $x ,$y) { return [$x ,$y]; },$y ];',
            ],
            // associative array (old)
            [
                '<?php $x = array("a" => $a , "b" =>  "b", 3=>$this->foo(),  "d" => 30  );',
                '<?php $x = array("a" => $a , "b" =>  "b",3=>$this->foo(),  "d" => 30  );',
            ],
            // associative array (short)
            [
                '<?php $x = [  "a" => $a ,  "b"=>"b", 3 => $this->foo(), "d" =>30];',
                '<?php $x = [  "a" => $a ,  "b"=>"b",3 => $this->foo(), "d" =>30];',
            ],
            // nested arrays
            [
                '<?php $x = ["a" => $a, "b" => "b", 3=> [5, 6,  7] , "d" => array(1,  2, 3 , 4)];',
                '<?php $x = ["a" => $a, "b" => "b",3=> [5,6,  7] , "d" => array(1,  2,3 ,4)];',
            ],
            // multi line array
            [
                '<?php $x = ["a" =>$a,
                    "b"=> "b",
                    3 => $this->foo(),
                    "d" => 30];',
            ],
            // multi line array
            [
                '<?php $a = [
                            "foo" ,
                            "bar",
                        ];',
            ],
            // nested multiline
            [
                '<?php $a = array(array(
                                    array(T_OPEN_TAG),
                                    array(T_VARIABLE, "$x"),
                        ), 1, );',
                '<?php $a = array(array(
                                    array(T_OPEN_TAG),
                                    array(T_VARIABLE,"$x"),
                        ),1,);',
            ],
            [
                '<?php $a = array( // comment
                    123,
                );',
            ],
        ];
    }
}
