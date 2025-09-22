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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer>
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer
 *
 * @author Jared Henderson <jared@netrivet.com>
 */
final class TrimArraySpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $foo = array("foo");',
            '<?php $foo = array( "foo" );',
        ];

        yield [
            '<?php $foo = ["foo"];',
            '<?php $foo = [ "foo" ];',
        ];

        yield [
            '<?php $foo = array();',
            '<?php $foo = array( );',
        ];

        yield [
            '<?php $foo = [];',
            '<?php $foo = [ ];',
        ];

        yield [
            '<?php $foo = array("foo", "bar");',
            '<?php $foo = array( "foo", "bar" );',
        ];

        yield [
            '<?php $foo = array("foo", "bar", );',
            '<?php $foo = array( "foo", "bar", );',
        ];

        yield [
            '<?php $foo = ["foo", "bar", ];',
            '<?php $foo = [ "foo", "bar", ];',
        ];

        yield [
            "<?php \$foo = array('foo', 'bar');",
            "<?php \$foo = array(\t'foo', 'bar'\t);",
        ];

        yield [
            "<?php \$foo = array('foo', 'bar');",
            "<?php \$foo = array(  \t 'foo', 'bar'\t   );",
        ];

        yield [
            '<?php $foo = array("foo", "bar");',
            '<?php $foo = array(    "foo", "bar"   );',
        ];

        yield [
            '<?php $foo = ["foo", "bar"];',
            '<?php $foo = [ "foo", "bar" ];',
        ];

        yield [
            '<?php $foo = ["foo", "bar"];',
            '<?php $foo = [     "foo", "bar"   ];',
        ];

        yield [
            "<?php \$foo = ['foo', 'bar'];",
            "<?php \$foo = [\t'foo', 'bar'\t];",
        ];

        yield [
            "<?php \$foo = ['foo', 'bar'];",
            "<?php \$foo = [ \t \t 'foo', 'bar'\t \t ];",
        ];

        yield [
            '<?php $foo = array("foo", "bar"); $bar = array("foo", "bar");',
            '<?php $foo = array( "foo", "bar" ); $bar = array( "foo", "bar" );',
        ];

        yield [
            '<?php $foo = ["foo", "bar"]; $bar = ["foo", "bar"];',
            '<?php $foo = [ "foo", "bar" ]; $bar = [ "foo", "bar" ];',
        ];

        yield [
            '<?php $foo = array("foo" => "bar");',
            '<?php $foo = array( "foo" => "bar" );',
        ];

        yield [
            '<?php $foo = ["foo" => "bar"];',
            '<?php $foo = [ "foo" => "bar" ];',
        ];

        yield [
            '<?php $foo = array($y ? true : false);',
            '<?php $foo = array( $y ? true : false );',
        ];

        yield [
            '<?php $foo = [$y ? true : false];',
            '<?php $foo = [ $y ? true : false ];',
        ];

        yield [
            '<?php $foo = array(array("foo"), array("bar"));',
            '<?php $foo = array( array( "foo" ), array( "bar" ) );',
        ];

        yield [
            '<?php $foo = [["foo"], ["bar"]];',
            '<?php $foo = [ [ "foo" ], [ "bar" ] ];',
        ];

        yield [
            '<?php function(array $foo = array("bar")) {};',
            '<?php function(array $foo = array( "bar" )) {};',
        ];

        yield [
            '<?php function(array $foo = ["bar"]) {};',
            '<?php function(array $foo = [ "bar" ]) {};',
        ];

        yield [
            '<?php $foo = array(function() {return "foo";});',
            '<?php $foo = array( function() {return "foo";} );',
        ];

        yield [
            '<?php $foo = [function() {return "foo";}];',
            '<?php $foo = [ function() {return "foo";} ];',
        ];

        yield [
            "<?php \$foo = [function( \$a =    \tarray('foo')  )  { return       'foo'   ;}];",
            "<?php \$foo = [ function( \$a =    \tarray( 'foo' )  )  { return       'foo'   ;} ];",
        ];

        yield [
            "<?php \$foo = array(function(  )  {\treturn     'foo'    \t;\t});",
            "<?php \$foo = array( function(  )  {\treturn     'foo'    \t;\t} );",
        ];

        yield [
            "<?php \$foo = [function()\t{\t  \treturn 'foo';\t}];",
            "<?php \$foo = [ function()\t{\t  \treturn 'foo';\t} ];",
        ];

        yield [
            "<?php \$foo \t   = array(function(\$a,\$b,\$c=array(3, 4))\t{\t  \treturn 'foo';\t});",
            "<?php \$foo \t   = array( function(\$a,\$b,\$c=array( 3, 4 ))\t{\t  \treturn 'foo';\t} );",
        ];

        yield [
            '<?php $foo = array($bar->method(), Foo::doSomething());',
            '<?php $foo = array( $bar->method(), Foo::doSomething() );',
        ];

        yield [
            '<?php $foo = [$bar->method(), Foo::doSomething()];',
            '<?php $foo = [ $bar->method(), Foo::doSomething() ];',
        ];

        yield [
            "<?php \$foo = [\$bar->method( \$a,\$b,    \$c,\t\t \$d  ), Foo::doSomething()];",
            "<?php \$foo = [ \$bar->method( \$a,\$b,    \$c,\t\t \$d  ), Foo::doSomething() ];",
        ];

        yield [
            "<?php \$foo   =\t array(\$bar->method( \$a,\$b,    \$c,\t\t \$d  ), \$bar -> doSomething(  ['baz']));",
            "<?php \$foo   =\t array( \$bar->method( \$a,\$b,    \$c,\t\t \$d  ), \$bar -> doSomething(  [ 'baz']) );",
        ];

        yield [
            '<?php $foo = array(array("foo"), array("bar"));',
            '<?php $foo = array( array("foo"), array("bar") );',
        ];

        yield [
            '<?php $foo = [["foo"], ["bar"]];',
            '<?php $foo = [ ["foo"], ["bar"] ];',
        ];

        yield [
            '<?php $foo = array(array("foo"), array("bar"));',
            '<?php $foo = array(array( "foo" ), array( "bar" ));',
        ];

        yield [
            '<?php $foo = [["foo"], ["bar"]];',
            '<?php $foo = [[ "foo" ], [ "bar" ]];',
        ];

        yield [
            '<?php $foo = array(/* empty array */);',
            '<?php $foo = array( /* empty array */ );',
        ];

        yield [
            '<?php $foo = [/* empty array */];',
            '<?php $foo = [ /* empty array */ ];',
        ];

        yield [
            '<?php someFunc(array(/* empty array */));',
            '<?php someFunc(array( /* empty array */ ));',
        ];

        yield [
            '<?php someFunc([/* empty array */]);',
            '<?php someFunc([ /* empty array */ ]);',
        ];

        yield [
            '<?php
    someFunc(array(
        /* empty array */
    ));',
        ];

        yield [
            '<?php
    someFunc([
        /* empty array */
    ]);',
        ];

        yield [
            '<?php
    someFunc(array(
        /* empty
        array */));',
            '<?php
    someFunc(array(
        /* empty
        array */ ));',
        ];

        yield [
            '<?php
    someFunc([
        /* empty
        array */]);',
            '<?php
    someFunc([
        /* empty
        array */ ]);',
        ];

        yield [
            '<?php
    $a = array( // My array of:
        1,      // - first item
        2,      // - second item
    );',
        ];

        yield [
            '<?php
    $a = [  // My array of:
        1,  // - first item
        2,  // - second item
    ];',
        ];

        yield [
            '<?php
    $a = array(
            // My array of:
        1,  // - first item
        2,  // - second item
    );',
        ];

        yield [
            '<?php
    $a = [
            // My array of:
        1,  // - first item
        2,  // - second item
    ];',
        ];

        yield [
            '<?php
    $foo = array(/* comment */
        1
    );',
            '<?php
    $foo = array( /* comment */
        1
    );',
        ];

        yield [
            '<?php
    $foo = [/* comment */
        1
    ];',
            '<?php
    $foo = [ /* comment */
        1
    ];',
        ];

        // don't fix array syntax within comments
        yield [
            '<?php someFunc([/* array( "foo", "bar", [ "foo" ] ) */]);',
            '<?php someFunc([ /* array( "foo", "bar", [ "foo" ] ) */ ]);',
        ];

        yield [
            '<?php $foo = array($bar[  4 ]);',
            '<?php $foo = array( $bar[  4 ] );',
        ];

        yield [
            '<?php $foo = [$bar[  4 ]];',
            '<?php $foo = [ $bar[  4 ] ];',
        ];

        yield [
            '<?php // array( "foo", "bar" );',
        ];

        // multiple single line nested arrays on one line
        yield [
            '<?php $foo = array("foo", "bar", [1, 2, array(3)]); $baz = ["hash", 1, array("test")];',
            '<?php $foo = array( "foo", "bar", [ 1, 2, array( 3 )] ); $baz = [ "hash", 1, array( "test") ];',
        ];

        yield [
            "<?php \$foo = array( \n'bar'\n );",
        ];

        yield [
            "<?php \$foo = [ \n'bar'\n ];",
        ];

        yield [
            "<?php \$foo = array( \n'a', 'b',\n'c');",
            "<?php \$foo = array( \n'a', 'b',\n'c' );",
        ];

        yield [
            "<?php \$foo = [ \n'a', 'b',\n'c'];",
            "<?php \$foo = [ \n'a', 'b',\n'c' ];",
        ];

        yield [
            "<?php \$foo = array('a', 'b',\n'c'\n);",
            "<?php \$foo = array( 'a', 'b',\n'c'\n);",
        ];

        yield [
            "<?php \$foo = ['a', 'b',\n'c'\n];",
            "<?php \$foo = [ 'a', 'b',\n'c'\n];",
        ];

        // don't fix array syntax within string
        yield [
            '<?php $foo = [\'$bar = array( "foo" );\', array(1, 5)];',
            '<?php $foo = [ \'$bar = array( "foo" );\', array(1, 5 ) ];',
        ];

        // crazy nested garbage pile #1
        yield [
            "<?php \$foo = array(/* comment \$bar = array([ ], array( 'foo' ) ), */ function(\$a = array('foo'), \$b = [/* comment [] */]) {}, array('foo' => 'bar', 'baz' => \$x[  4], 'hash' => array(1,2,3)));",
            "<?php \$foo = array( /* comment \$bar = array([ ], array( 'foo' ) ), */ function(\$a = array( 'foo' ), \$b = [ /* comment [] */ ]) {}, array( 'foo' => 'bar', 'baz' => \$x[  4], 'hash' => array(1,2,3 )) );",
        ];

        // crazy nested garbage pile #2
        yield [
            '<?php $a = [array("foo", "bar ", [1, 4, function($x = ["foobar", 2]) {}, [/* array( 1) */]]), array("foo", [$y[ 3]()], \'bar\')];',
            '<?php $a = [ array("foo", "bar ", [ 1, 4, function($x = [ "foobar", 2 ]) {}, [/* array( 1) */] ] ), array("foo", [ $y[ 3]() ], \'bar\') ];',
        ];

        yield [
            '<?php
    $foo = array(
        1 => 2, // comment
    );
',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield 'array destructuring' => [
            "<?php ['url' => \$url] = \$data;",
            "<?php [                  'url' => \$url                         ] = \$data;",
        ];

        yield 'array destructuring with comments' => [
            "<?php [/* foo */ 'url' => \$url /* bar */] = \$data;",
            "<?php [     /* foo */ 'url' => \$url /* bar */     ] = \$data;",
        ];

        yield 'multiline array destructuring' => [
            '<?php
    [
        \'url\' => $url,
        \'token\' => $token,
    ] = $data;
',
        ];
    }
}
