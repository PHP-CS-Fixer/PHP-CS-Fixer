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

namespace PhpCsFixer\Tests\Fixer\PSR2;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 *
 * @internal
 */
final class MethodArgumentSpaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider testFixProvider
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testFixProvider()
    {
        return array(
            array(
                '<?php xyz("", "", "", "");',
                '<?php xyz("","","","");',
            ),
            // test method arguments
            array(
                '<?php function xyz($a=10, $b=20, $c=30) {}',
                '<?php function xyz($a=10,$b=20,$c=30) {}',
            ),
            // test method arguments with multiple spaces
            array(
                '<?php function xyz($a=10, $b=20, $c=30) {}',
                '<?php function xyz($a=10,         $b=20 , $c=30) {}',
            ),
            // test method call
            array(
                '<?php xyz($a=10, $b=20, $c=30);',
                '<?php xyz($a=10 ,$b=20,$c=30);',
            ),
            // test method call with multiple spaces
            array(
                '<?php xyz($a=10, $b=20, $c=30);',
                '<?php xyz($a=10 , $b=20 ,          $c=30);',
            ),
            // test method call with tab
            array(
                '<?php xyz($a=10, $b=20, $c=30);',
                "<?php xyz(\$a=10 , \$b=20 ,\t \$c=30);",
            ),
            // test method call with \n not affected
            array(
                "<?php xyz(\$a=10, \$b=20,\n                    \$c=30);",
            ),
            // test method call with \r\n not affected
            array(
                "<?php xyz(\$a=10, \$b=20,\r\n                    \$c=30);",
            ),
            // test method call
            array(
                '<?php xyz($a=10, $b=20, $this->foo(), $c=30);',
                '<?php xyz($a=10,$b=20 ,$this->foo() ,$c=30);',
            ),
            // test method call with multiple spaces
            array(
                '<?php xyz($a=10, $b=20, $this->foo(), $c=30);',
                '<?php xyz($a=10,$b=20 ,         $this->foo() ,$c=30);',
            ),
            // test receiving data in list context with omitted values
            array(
                '<?php list($a, $b, , , $c) = foo();',
                '<?php list($a, $b,, ,$c) = foo();',
            ),
            // test receiving data in list context with omitted values and multiple spaces
            array(
                '<?php list($a, $b, , , $c) = foo();',
                '<?php list($a, $b,,    ,$c) = foo();',
            ),
            // skip array
            array(
                '<?php array(10 , 20 ,30);',
            ),
            // list call with trailing comma
            array(
                '<?php list($path, $mode, ) = foo();',
                '<?php list($path, $mode,) = foo();',
            ),
            //inline comments with spaces
            array(
                '<?php xyz($a=10, /*comment1*/ $b=2000, /*comment2*/ $c=30);',
                '<?php xyz($a=10,    /*comment1*/ $b=2000,/*comment2*/ $c=30);',

            ),
            // must keep align comments
            array(
                '<?php function xyz(
                    $a=10,      //comment1
                    $b=20,      //comment2
                    $c=30) {
                }',
            ),
            array(
                '<?php function xyz(
                    $a=10,  //comment1
                    $b=2000,//comment2
                    $c=30) {
                }',
            ),
            //multiline comments also must be ignored
            array(
                '<?php function xyz(
                    $a=10,  /* comment1a
                               comment1b
                            */
                    $b=2000,/* comment2a
                        comment 2b
                        comment 2c */
                    $c=30) {
                }',
            ),
            // multiline comments also must be ignored
            array(
                '<?php
                    function xyz(
                        $a=10, /* multiline comment
                                 not at the end of line
                                */ $b=2000,
                        $a2=10 /* multiline comment
                                 not at the end of line
                                */, $b2=2000,
                        $c=30) {
                    }',
                '<?php
                    function xyz(
                        $a=10, /* multiline comment
                                 not at the end of line
                                */ $b=2000,
                        $a2=10 /* multiline comment
                                 not at the end of line
                                */ ,$b2=2000,
                        $c=30) {
                    }',
            ),
            // multi line testing method arguments
            array(
                '<?php function xyz(
                    $a=10,
                    $b=20,
                    $c=30) {
                }',
                '<?php function xyz(
                    $a=10 ,
                    $b=20,
                    $c=30) {
                }',
            ),
            // multi line testing method call
            array(
                '<?php xyz(
                    $a=10,
                    $b=20,
                    $c=30
                    );',
                '<?php xyz(
                    $a=10 ,
                    $b=20,
                    $c=30
                    );',
            ),
            // skip arrays but replace arg methods
            array(
                '<?php fnc(1, array(2, func2(6, 7) ,4), 5);',
                '<?php fnc(1,array(2, func2(6,    7) ,4),    5);',
            ),
            // ignore commas inside call argument
            array(
                '<?php fnc(1, array(2, 3 ,4), 5);',
            ),
            // skip multi line array
            array(
                '<?php
                    array(
                        10 ,
                        20,
                        30
                    );',
            ),
            // skip short array
            array(
                '<?php
    $foo = ["a"=>"apple", "b"=>"bed" ,"c"=>"car"];
    $bar = ["a" ,"b" ,"c"];
    ',
            ),
            // don't change HEREDOC and NOWDOC
            array(
                "<?php
    \$this->foo(
        <<<EOTXTa
    heredoc
EOTXTa
        ,
        <<<'EOTXTb'
    nowdoc
EOTXTb
        ,
        'foo'
    );
",
            ),
        );
    }
}
