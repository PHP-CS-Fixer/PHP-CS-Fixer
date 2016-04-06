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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 * @author Adam Marczuk <adam@marczuk.info>
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 *
 * @internal
 */
final class WhiteSpaceAroundCommaFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
                    list($a, , $c, $d,) = foo();
                ',
            ),
            array(
                '<?php
                    list($a, , $c, $d,) = foo();
                ',
                '<?php
                    list($a   , ,$c,$d  ,  ) = foo();
                ',
            ),
            array(
                '<?php
                    list($a,) = foo();
                    list($b,) = foo();
                    list($c,) = foo();
                    unset($a, $b, $c);
                ',
                '<?php
                    list($a , ) = foo();
                    list($b ,) = foo();
                    list($c, ) = foo();
                    unset($a,   $b  ,$c);
                ',
            ),
            array(
                '<?php
                    for($a = 1, $b =2; $c < time(); ++$c) {
                    }
                ',
                '<?php
                    for($a = 1     ,$b =2; $c < time(); ++$c) {
                    }
                ',
            ),
            array(
                '<?php
                    $a = array(17, 12, /**/13, /**/14, /**/
                           //
                          //
                        5,	'.'
                        6,
 7,             /**/
     8,);
                $b = [1,
                ];
                $c = [1, //
                ];
                ',
                '<?php
                    $a = array(17    ,12/**/,13 /**/,14/**/
                           //
                        ,  //
                        5	,
                        6
 ,7             /**/
     ,8,);
                $b = [1,
                ];
                $c = [1//
                ,];
                ',
            ),
            array(
                '<?php
                    array(
                        $a,
                        $b, // test
                        $c, /* abc */
                        $d,
                        $e, $f,
                        $d,
                        $z,
                        $y,     '.'
                    );
                ',
                '<?php
                    array(
                        $a
                        ,$b // test
                        ,$c /* abc */
                        ,$d
                        ,$e   ,$f,
                        $d,
                        $z,
                        $y     ,
                    );
                ',
            ),
            array(
                '<?php
                    interface Foo extends D,
                    E
                    {
                        public function diff($old, $new);
                    }

                    class A implements B, C,
                           D,
                       E
                    {

                    }

                    class D extends D
                    {
                        public function __construct($a = 1,
                                 $b = 2)
                        {
                        }
                    }
                ',
                '<?php
                    interface Foo extends D,
                    E
                    {
                        public function diff($old   ,         $new);
                    }

                    class A implements B,         C
                       ,    D
                       ,E
                    {

                    }

                    class D extends D
                    {
                        public function __construct($a = 1
                              ,   $b = 2)
                        {
                        }
                    }
                ',
            ),
            array(
                '<?php
                    global $a, $b;
                    static $a, $b;
                    sprintf($a, $j, $b);
                    unset($a, $b);
                ',
                '<?php
                    global $a      ,$b;
                    static $a      ,$b;
                    sprintf($a     ,$j,    $b);
                    unset($a       ,$b);
                ',
            ),
            array(
                '<?php
                    $a = array(1, /**/ 2, /**/  3, /**/  4, /**/);
                ',
                '<?php
                    $a = array(1/**/, 2/**/ , 3 /**/ , 4 /**/,);
                ',
            ),
        );
    }

    /**
     * @dataProvider testNoWhitespaceBeforeCommaInArrayProvider
     */
    public function testNoWhitespaceBeforeCommaInArrayFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testNoWhitespaceBeforeCommaInArrayProvider()
    {
        return array(
            //old style array
            array(
                '<?php $x = array(1, "2", 3);',
                '<?php $x = array(1 , "2",3);',
            ),
            //old style array with comments
            array(
                '<?php $x = array /* comment */ (1, "2", 3);',
                '<?php $x = array /* comment */ (1  ,  "2", 3);',
            ),

            //short array
            array(
                '<?php $x = [1, "2", 3, $y];',
                '<?php $x = [1 ,  "2", 3 ,$y];',
            ),
            // don't change function calls
            array(
                '<?php $x = [ 1, "2", getValue(1, 2, 3), $y];',
                '<?php $x = [ 1 , "2",getValue(1,2  ,3)   ,$y];',
            ),
            // don't change function declarations
            array(
                '<?php $x = [1, "2", function( $x, $y) { return $x + $y; }, $y];',
                '<?php $x = [1 , "2", function( $x ,$y) { return $x + $y; }, $y];',
            ),
            // don't change function declarations but change array inside
            array(
                '<?php $x = [ 1, "2", "c" => function( $x, $y) { return [$x, $y]; }, $y];',
                '<?php $x = [ 1 ,  "2","c" => function( $x ,$y) { return [$x , $y]; }, $y];',
            ),
            // associative array (old)
            array(
                '<?php $x = array( "a" => $a, "b" =>  "b", 3=>$this->foo(), "d" => 30);',
                '<?php $x = array( "a" => $a , "b" =>  "b",3=>$this->foo()  , "d" => 30);',
            ),
            // associative array (short)
            array(
                '<?php $x = [  "a" => $a, "b"=>"b", 3 => $this->foo(), "d" =>30  ];',
                '<?php $x = [  "a" => $a , "b"=>"b",3 => $this->foo()    , "d" =>30  ];',
            ),
            // nested arrays
            array(
                '<?php $x = ["a" => $a, "b" => "b", 3=> [5, 6, 7], "d" => array(1, 2, 3, 4)];',
                '<?php $x = ["a" => $a , "b" => "b", 3=> [5 ,6, 7]  , "d" => array(1, 2,3 ,4)];',
            ),
            // multi line array
            array(
                '<?php $x = [  "a" =>$a,
                    "b"=>
                "b",
                    3 => $this->foo(),   '.'
                    "d" => 30  ];',
                '<?php $x = [  "a" =>$a,
                    "b"=>
                "b",
                    3 => $this->foo()   ,
                    "d" => 30  ];',
            ),
            // multi line array
            array(
                '<?php $a = [
                            "foo",  '.'
                            "bar",
                            '.'
                        ];',
                '<?php $a = [
                            "foo"  ,
                            "bar"
                            ,
                        ];',
            ),
            // nested multiline
            array(
                '<?php $a = array(array(
                                    array(T_OPEN_TAG),
                                    array(T_VARIABLE, "$x"),
                        ), 1);',
            ),
            array(
                '<?php $a = array( // comment
                    123,
                );',
            ),
            array(
                "<?php \$x = array(<<<'EOF'
<?php \$a = '\\foo\\bar\\\\';
EOF
                , <<<'EOF'
<?php \$a = \"\\foo\\bar\\\\\";
EOF
                    );",
            ),
        );
    }

    /**
     * @dataProvider testFixMethodArgumentSpace
     */
    public function testMethodArgumentSpaceFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testFixMethodArgumentSpace()
    {
        return array(
            array(
                '<?php xyz("", "", "", "");',
                '<?php xyz("","","","");',
            ),
            // test method arguments
            array(
                '<?php function xyz1($a=10, $b=20, $c=30) {}',
                '<?php function xyz1($a=10,$b=20,$c=30) {}',
            ),
            // test method arguments with multiple spaces
            array(
                '<?php function xyz2($a=10, $b=20, $c=30) {}',
                '<?php function xyz2($a=10,         $b=20 , $c=30) {}',
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
                '<?php list($a, $b, , ,$c) = foo();',
            ),
            // test receiving data in list context with omitted values and multiple spaces
            array(
                '<?php list($a, $b, , , $c) = foo();',
                '<?php list($a, $b,,    ,$c) = foo();',
            ),
            //inline comments with spaces
            array(
                '<?php xyz($a=10, /*comment1*/ $b=6000, /*comment2*/ $c=30);',
                '<?php xyz($a=10,    /*comment1*/ $b=6000,/*comment2*/ $c=30);',

            ),
            // must keep align comments
            array(
                '<?php function xyz3(
                    $a=10,      //comment1
                    $b=20,      //comment2
                    $c=30) {
                }',
            ),
            array(
                '<?php function xyz4(
                    $a=10,  //comment1
                    $b=2000, //comment2
                    $c=30) {
                }',
                '<?php function xyz4(
                    $a=10,  //comment1
                    $b=2000,//comment2
                    $c=30) {
                }',
            ),
            //multiline comments also must be ignored
            array(
                '<?php function xyz5(
                    $a=10,  /* comment1a
                               comment1b
                            */
                    $b=2000, /* comment2a
                        comment 2b
                        comment 2c */
                    $c=30) {
                }',
                '<?php function xyz5(
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
                    function xyz5(
                        $a=10, /* multiline comment
                                 not at the end of line
                                */ $b=2000,
                        $a22=10, /* multiline comment
                                 not at the end of line
                                */ $b2=2000,
                        $c=30) {
                    }',
                '<?php
                    function xyz5(
                        $a=10, /* multiline comment
                                 not at the end of line
                                */ $b=2000,
                        $a22=10 /* multiline comment
                                 not at the end of line
                                */ ,$b2=2000,
                        $c=30) {
                    }',
            ),
            // multi line testing method arguments
            array(
                '<?php function xyz6(
                    $a=12, '.'
                    $b=22,
                    $c=32) {
                }',
                '<?php function xyz6(
                    $a=12 ,
                    $b=22,
                    $c=32) {
                }',
            ),
            // multi line testing method call
            array(
                '<?php xyz7(
                    $a=11, '.'
                    $b=21,
                    $c=31
                    );',
                '<?php xyz7(
                    $a=11 ,
                    $b=21,
                    $c=31
                    );',
            ),
            // skip arrays but replace arg methods
            array(
                '<?php fnc1(1, array(2, func2(6, 7), 4), 5);',
                '<?php fnc1(1,array(2, func2(6,    7) ,4),    5);',
            ),
            // ignore commas inside call argument
            array(
                '<?php fnc2(1, array(2, 3, 4), 5);',
                '<?php fnc2(1, array(2, 3 ,4), 5);',
            ),
            // skip short array
            array(
                '<?php
    $foo = ["a"=>"apple", "b"=>"bed", "c"=>"car"];
    $bar = ["a", "b", "c"];
    ',
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
