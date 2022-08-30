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
     * @dataProvider provideFixCases
     *
     * @param null|array<string, bool> $configuration
     */
    public function testFix(string $expected, ?string $input = null, ?array $configuration = null): void
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            // old style array
            [
                '<?php $x = array( 1 , "2", 3);',
                '<?php $x = array( 1 ,"2",3);',
            ],
            // old style array with comments
            [
                '<?php $x = array /* comment */ ( 1 ,  "2", 3);',
                '<?php $x = array /* comment */ ( 1 ,  "2",3);',
            ],

            // short array
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
            // don't change anonymous class implements list but change array inside
            [
                '<?php $x = [1,  "2", "c" => new class implements Foo ,Bar { const FOO = ["x", "y"]; }, $y ];',
                '<?php $x = [1,  "2","c" => new class implements Foo ,Bar { const FOO = ["x","y"]; },$y ];',
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
            [
                '<?php $x = array(...$foo, ...$bar);',
                '<?php $x = array(...$foo,...$bar);',
            ],
            [
                '<?php $x = [...$foo, ...$bar];',
                '<?php $x = [...$foo,...$bar];',
            ],
            [
                '<?php [0, 1, 2, 3, 4, 5, 6];',
                '<?php [0,1, 2,  3,   4,    5,     6];',
                ['ensure_single_space' => true],
            ],
            [
                '<?php [0, 1, 2, 3, 4, 5];',
                "<?php [0,\t1,\t\t\t2,\t 3, \t4,    \t    5];",
                ['ensure_single_space' => true],
            ],
            [
                '<?php [
                    0,                    # less than one
                    1,                    // one
                    42,                   /* more than one */
                    1000500100900,        /** much more than one */
                ];',
                null,
                ['ensure_single_space' => true],
            ],
            [
                '<?php [0, /* comment */ 1, /** PHPDoc */ 2];',
                '<?php [0,    /* comment */ 1,    /** PHPDoc */ 2];',
                ['ensure_single_space' => true],
            ],
        ];
    }
}
