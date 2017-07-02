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
 * @author Jared Henderson <jared@netrivet.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer
 */
final class TrimArraySpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php $foo = array("foo");',
                '<?php $foo = array( "foo" );',
            ],

            [
                '<?php $foo = ["foo"];',
                '<?php $foo = [ "foo" ];',
            ],

            [
                '<?php $foo = array();',
                '<?php $foo = array( );',
            ],

            [
                '<?php $foo = [];',
                '<?php $foo = [ ];',
            ],

            [
                '<?php $foo = array("foo", "bar");',
                '<?php $foo = array( "foo", "bar" );',
            ],

            [
                '<?php $foo = array("foo", "bar", );',
                '<?php $foo = array( "foo", "bar", );',
            ],

            [
                '<?php $foo = ["foo", "bar", ];',
                '<?php $foo = [ "foo", "bar", ];',
            ],

            [
                "<?php \$foo = array('foo', 'bar');",
                "<?php \$foo = array(\t'foo', 'bar'\t);",
            ],

            [
                "<?php \$foo = array('foo', 'bar');",
                "<?php \$foo = array(  \t 'foo', 'bar'\t   );",
            ],

            [
                '<?php $foo = array("foo", "bar");',
                '<?php $foo = array(    "foo", "bar"   );',
            ],

            [
                '<?php $foo = ["foo", "bar"];',
                '<?php $foo = [ "foo", "bar" ];',
            ],

            [
                '<?php $foo = ["foo", "bar"];',
                '<?php $foo = [     "foo", "bar"   ];',
            ],

            [
                "<?php \$foo = ['foo', 'bar'];",
                "<?php \$foo = [\t'foo', 'bar'\t];",
            ],

            [
                "<?php \$foo = ['foo', 'bar'];",
                "<?php \$foo = [ \t \t 'foo', 'bar'\t \t ];",
            ],

            [
                '<?php $foo = array("foo", "bar"); $bar = array("foo", "bar");',
                '<?php $foo = array( "foo", "bar" ); $bar = array( "foo", "bar" );',
            ],

            [
                '<?php $foo = ["foo", "bar"]; $bar = ["foo", "bar"];',
                '<?php $foo = [ "foo", "bar" ]; $bar = [ "foo", "bar" ];',
            ],

            [
                '<?php $foo = array("foo" => "bar");',
                '<?php $foo = array( "foo" => "bar" );',
            ],

            [
                '<?php $foo = ["foo" => "bar"];',
                '<?php $foo = [ "foo" => "bar" ];',
            ],

            [
                '<?php $foo = array("foo");',
                '<?php $foo = array( "foo" );',
            ],

            [
                '<?php $foo = ["foo"];',
                '<?php $foo = [ "foo" ];',
            ],

            [
                '<?php $foo = array($y ? true : false);',
                '<?php $foo = array( $y ? true : false );',
            ],

            [
                '<?php $foo = [$y ? true : false];',
                '<?php $foo = [ $y ? true : false ];',
            ],

            [
                '<?php $foo = array(array("foo"), array("bar"));',
                '<?php $foo = array( array( "foo" ), array( "bar" ) );',
            ],

            [
                '<?php $foo = [["foo"], ["bar"]];',
                '<?php $foo = [ [ "foo" ], [ "bar" ] ];',
            ],

            [
                '<?php function(array $foo = array("bar")) {};',
                '<?php function(array $foo = array( "bar" )) {};',
            ],

            [
                '<?php function(array $foo = ["bar"]) {};',
                '<?php function(array $foo = [ "bar" ]) {};',
            ],

            [
                '<?php $foo = array(function() {return "foo";});',
                '<?php $foo = array( function() {return "foo";} );',
            ],

            [
                '<?php $foo = [function() {return "foo";}];',
                '<?php $foo = [ function() {return "foo";} ];',
            ],

            [
                "<?php \$foo = [function( \$a =    \tarray('foo')  )  { return       'foo'   ;}];",
                "<?php \$foo = [ function( \$a =    \tarray( 'foo' )  )  { return       'foo'   ;} ];",
            ],

            [
                "<?php \$foo = array(function(  )  {\treturn     'foo'    \t;\t});",
                "<?php \$foo = array( function(  )  {\treturn     'foo'    \t;\t} );",
            ],

            [
                "<?php \$foo = [function()\t{\t  \treturn 'foo';\t}];",
                "<?php \$foo = [ function()\t{\t  \treturn 'foo';\t} ];",
            ],

            [
                "<?php \$foo \t   = array(function(\$a,\$b,\$c=array(3, 4))\t{\t  \treturn 'foo';\t});",
                "<?php \$foo \t   = array( function(\$a,\$b,\$c=array( 3, 4 ))\t{\t  \treturn 'foo';\t} );",
            ],

            [
                '<?php $foo = array($bar->method(), Foo::doSomething());',
                '<?php $foo = array( $bar->method(), Foo::doSomething() );',
            ],

            [
                '<?php $foo = [$bar->method(), Foo::doSomething()];',
                '<?php $foo = [ $bar->method(), Foo::doSomething() ];',
            ],

            [
                "<?php \$foo = [\$bar->method( \$a,\$b,    \$c,\t\t \$d  ), Foo::doSomething()];",
                "<?php \$foo = [ \$bar->method( \$a,\$b,    \$c,\t\t \$d  ), Foo::doSomething() ];",
            ],

            [
                "<?php \$foo   =\t array(\$bar->method( \$a,\$b,    \$c,\t\t \$d  ), \$bar -> doSomething(  ['baz']));",
                "<?php \$foo   =\t array( \$bar->method( \$a,\$b,    \$c,\t\t \$d  ), \$bar -> doSomething(  [ 'baz']) );",
            ],

            [
                '<?php $foo = array(array("foo"), array("bar"));',
                '<?php $foo = array( array("foo"), array("bar") );',
            ],

            [
                '<?php $foo = [["foo"], ["bar"]];',
                '<?php $foo = [ ["foo"], ["bar"] ];',
            ],

            [
                '<?php $foo = array(array("foo"), array("bar"));',
                '<?php $foo = array(array( "foo" ), array( "bar" ));',
            ],

            [
                '<?php $foo = [["foo"], ["bar"]];',
                '<?php $foo = [[ "foo" ], [ "bar" ]];',
            ],

            [
                '<?php $foo = array(array("foo"), array("bar"));',
                '<?php $foo = array( array( "foo" ), array( "bar" ) );',
            ],

            [
                '<?php $foo = [["foo"], ["bar"]];',
                '<?php $foo = [ [ "foo" ], [ "bar" ] ];',
            ],

            [
                '<?php $foo = array(/* empty array */);',
                '<?php $foo = array( /* empty array */ );',
            ],

            [
                '<?php $foo = [/* empty array */];',
                '<?php $foo = [ /* empty array */ ];',
            ],

            [
                '<?php someFunc(array(/* empty array */));',
                '<?php someFunc(array( /* empty array */ ));',
            ],

            [
                '<?php someFunc([/* empty array */]);',
                '<?php someFunc([ /* empty array */ ]);',
            ],

            [
                '<?php
    someFunc(array(
        /* empty array */
    ));',
            ],

            [
                '<?php
    someFunc([
        /* empty array */
    ]);',
            ],

            [
                '<?php
    someFunc(array(
        /* empty
        array */));',
                '<?php
    someFunc(array(
        /* empty
        array */ ));',
            ],

            [
                '<?php
    someFunc([
        /* empty
        array */]);',
                '<?php
    someFunc([
        /* empty
        array */ ]);',
            ],

            [
                '<?php
    $a = array( // My array of:
        1,      // - first item
        2,      // - second item
    );',
            ],

            [
                '<?php
    $a = [  // My array of:
        1,  // - first item
        2,  // - second item
    ];',
            ],

            [
                '<?php
    $a = array(
            // My array of:
        1,  // - first item
        2,  // - second item
    );',
            ],

            [
                '<?php
    $a = [
            // My array of:
        1,  // - first item
        2,  // - second item
    ];',
            ],

            [
                '<?php
    $foo = array(/* comment */
        1
    );',
                '<?php
    $foo = array( /* comment */
        1
    );',
            ],

            [
                '<?php
    $foo = [/* comment */
        1
    ];',
                '<?php
    $foo = [ /* comment */
        1
    ];',
            ],

            // don't fix array syntax within comments
            [
                '<?php someFunc([/* array( "foo", "bar", [ "foo" ] ) */]);',
                '<?php someFunc([ /* array( "foo", "bar", [ "foo" ] ) */ ]);',
            ],

            [
                '<?php $foo = array($bar[  4 ]);',
                '<?php $foo = array( $bar[  4 ] );',
            ],

            [
                '<?php $foo = [$bar[  4 ]];',
                '<?php $foo = [ $bar[  4 ] ];',
            ],

            [
                '<?php // array( "foo", "bar" );',
            ],

            // multiple single line nested arrays on one line
            [
                '<?php $foo = array("foo", "bar", [1, 2, array(3)]); $baz = ["hash", 1, array("test")];',
                '<?php $foo = array( "foo", "bar", [ 1, 2, array( 3 )] ); $baz = [ "hash", 1, array( "test") ];',
            ],

            [
                "<?php \$foo = array( \n'bar'\n );",
            ],

            [
                "<?php \$foo = [ \n'bar'\n ];",
            ],

            [
                "<?php \$foo = array( \n'a', 'b',\n'c');",
                "<?php \$foo = array( \n'a', 'b',\n'c' );",
            ],

            [
                "<?php \$foo = [ \n'a', 'b',\n'c'];",
                "<?php \$foo = [ \n'a', 'b',\n'c' ];",
            ],

            [
                "<?php \$foo = array('a', 'b',\n'c'\n);",
                "<?php \$foo = array( 'a', 'b',\n'c'\n);",
            ],

            [
                "<?php \$foo = ['a', 'b',\n'c'\n];",
                "<?php \$foo = [ 'a', 'b',\n'c'\n];",
            ],

            // dont fix array syntax within string
            [
                '<?php $foo = [\'$bar = array( "foo" );\', array(1, 5)];',
                '<?php $foo = [ \'$bar = array( "foo" );\', array(1, 5 ) ];',
            ],

            // crazy nested garbage pile #1
            [
                "<?php \$foo = array(/* comment \$bar = array([ ], array( 'foo' ) ), */ function(\$a = array('foo'), \$b = [/* comment [] */]) {}, array('foo' => 'bar', 'baz' => \$x[  4], 'hash' => array(1,2,3)));",
                "<?php \$foo = array( /* comment \$bar = array([ ], array( 'foo' ) ), */ function(\$a = array( 'foo' ), \$b = [ /* comment [] */ ]) {}, array( 'foo' => 'bar', 'baz' => \$x[  4], 'hash' => array(1,2,3 )) );",
            ],

            // crazy nested garbage pile #2
            [
                '<?php $a = [array("foo", "bar ", [1, 4, function($x = ["foobar", 2]) {}, [/* array( 1) */]]), array("foo", [$y[ 3]()], \'bar\')];',
                '<?php $a = [ array("foo", "bar ", [ 1, 4, function($x = [ "foobar", 2 ]) {}, [/* array( 1) */] ] ), array("foo", [ $y[ 3]() ], \'bar\') ];',
            ],

            [
                '<?php
    $foo = array(
        1 => 2, // comment
    );
',
            ],
            [
                '<?php
function a()
{
    yield array("a" => 1, "b" => 2);
}',
                '<?php
function a()
{
    yield array( "a" => 1, "b" => 2 );
}',
            ],
            [
                '<?php
function a()
{
    yield ["a" => 1, "b" => 2];
}',
                '<?php
function a()
{
    yield [ "a" => 1, "b" => 2 ];
}',
            ],
        ];
    }
}
