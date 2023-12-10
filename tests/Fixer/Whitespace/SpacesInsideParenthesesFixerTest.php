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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Marc Aubé
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\SpacesInsideParenthesesFixer
 */
final class SpacesInsideParenthesesFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigMissingKey(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[spaces_inside_parentheses\] Invalid configuration: The option "a" does not exist\. Defined options are: "space"\.$#');

        $this->fixer->configure(['a' => 1]);
    }

    public function testInvalidConfigValue(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[spaces_inside_parentheses\] Invalid configuration: The option "space" with value "double" is invalid\. Accepted values are: "none", "single"\.$#');

        $this->fixer->configure(['space' => 'double']);
    }

    /**
     * @dataProvider provideDefaultFixCases
     */
    public function testDefaultFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideSpacesFixCases
     */
    public function testSpacesFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['space' => 'single']);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideDefaultFixCases(): iterable
    {
        // default leaves new lines alone
        yield [
            "<?php

class Foo
{
    private function bar()
    {
        if (foo(
            'foo' ,
            'bar'    ,
            [1, 2, 3],
            'baz' // a comment just to mix things up
        )) {
            return 1;
        };
    }
}
",
        ];

        yield [
            '<?php foo();',
            '<?php foo( );',
        ];

        yield [
            '<?php
if (true) {
    // if body
}',
            '<?php
if ( true ) {
    // if body
}',
        ];

        yield [
            '<?php
if (true) {
    // if body
}',
            '<?php
if (     true   ) {
    // if body
}',
        ];

        yield [
            '<?php
function foo($bar, $baz)
{
    // function body
}',
            '<?php
function foo( $bar, $baz )
{
    // function body
}',
        ];

        yield [
            '<?php
$foo->bar($arg1, $arg2);',
            '<?php
$foo->bar(  $arg1, $arg2   );',
        ];

        yield [
            '<?php
$var = array( 1, 2, 3 );
',
        ];

        yield [
            '<?php
$var = [ 1, 2, 3 ];
',
        ];

        // list call with trailing comma - need to leave alone
        yield [
            '<?php list($path, $mode, ) = foo();',
        ];

        yield [
            '<?php list($path, $mode,) = foo();',
        ];

        yield [
            '<?php
$a = $b->test(  // do not remove space
    $e          // between `(` and `)`
                // and this comment
);',
        ];

        yield [
            '<?php
function foo($bar, $baz)
{
    // function body
}',
            '<?php
function foo( $bar, $baz )
{
    // function body
}',
        ];

        yield [
            '<?php
function hello($value) {
    // code...
}',
            '<?php
function hello( $value ) {
    // code...
}',
        ];

        yield [
            '<?php
$code = function ($hello, $there) use ($ami, $tumi) {
    // code
};
',
            '<?php
$code = function ( $hello, $there   ) use ( $ami, $tumi ) {
    // code
};
',
        ];

        yield [
            '<?php
for ($i = 0; $i < 42; $i++) {
    // code...
}
',
            '<?php
for (   $i = 0; $i < 42; $i++ ) {
    // code...
}
',
        ];

        yield [
            '<?php
explode($a, $b);
',
            '<?php
explode( $a, $b );
',
        ];

        yield [
            '<?php
if ($something) {
    // code
}
',
            '<?php
if (  $something      ) {
    // code
}
',
        ];

        yield [
            '<?php
multiply((2 + 3) * 4);
',
            '<?php
multiply( (    2 + 3  ) * 4    );
',
        ];

        yield [
            '<?php $x = (new Foo())->bar();',
            '<?php $x = ( new Foo() )->bar();',
        ];

        yield [
            '<?php $x = (new Foo)->bar;',
            '<?php $x = ( new Foo )->bar;',
        ];
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideSpacesFixCases(): iterable
    {
        // Leaves new lines alone
        yield [
            "<?php

class Foo
{
    private function bar()
    {
        if ( foo(
            'foo' ,
            'bar'    ,
            [1, 2, 3],
            'baz' // a comment just to mix things up
        ) ) {
            return 1;
        };
    }
}",
        ];

        yield [
            '<?php foo();',
            '<?php foo( );',
        ];

        yield [
            '<?php
if ( true ) {
    // if body
}',
            '<?php
if (true) {
    // if body
}',
        ];

        yield [
            '<?php
if ( true ) {
    // if body
}',
            '<?php
if (     true   ) {
    // if body
}',
        ];

        yield [
            '<?php
function foo( $bar, $baz )
{
    // function body
}',
            '<?php
function foo($bar, $baz)
{
    // function body
}',
        ];

        yield [
            '<?php
$foo->bar( $arg1, $arg2 );',
            '<?php
$foo->bar(  $arg1, $arg2   );',
        ];

        yield [
            '<?php
$var = array( 1, 2, 3 );
',
            '<?php
$var = array(1, 2, 3);
',
        ];

        yield [
            '<?php
$var = [ 1, 2, 3 ];
',
        ];

        yield [
            '<?php list( $path, $mode, ) = foo();',
            '<?php list($path, $mode,) = foo();',
        ];

        yield [
            '<?php
$a = $b->test(  // do not remove space
    $e          // between `(` and `)`
                // and this comment
 );',
        ];

        yield [
            '<?php
function foo( $bar, $baz )
{
    // function body
}',
            '<?php
function foo($bar, $baz)
{
    // function body
}',
        ];

        yield [
            '<?php
function hello( $value ) {
    // code...
}',
            '<?php
function hello($value) {
    // code...
}',
        ];

        yield [
            '<?php
$code = function ( $hello, $there ) use ( $ami, $tumi ) {
    // code
};
',
            '<?php
$code = function ($hello, $there) use ($ami, $tumi) {
    // code
};
',
        ];

        yield [
            '<?php
for ( $i = 0; $i < 42; $i++ ) {
    // code...
}
',
            '<?php
for ($i = 0; $i < 42; $i++) {
    // code...
}
',
        ];

        yield [
            '<?php
explode( $a, $b );
',
            '<?php
explode($a, $b);
',
        ];

        yield [
            '<?php
if ( $something ) {
    // code
}
',
            '<?php
if (    $something    ) {
    // code
}
',
        ];

        yield [
            '<?php
multiply( ( 2 + 3 ) * 4 );
',
            '<?php
multiply((2 + 3) * 4);
',
        ];

        yield [
            '<?php $x = ( new Foo() )->bar();',
            '<?php $x = (new Foo())->bar();',
        ];
    }

    /**
     * @dataProvider provideDefaultFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testDefaultFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideDefaultFix80Cases(): iterable
    {
        yield 'mixed argument' => [
            '<?php function foo(mixed $a){}',
            '<?php function foo( mixed $a ){}',
        ];
    }

    /**
     * @dataProvider provideSpacesFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testSpacesFix80(string $expected, string $input): void
    {
        $this->fixer->configure(['space' => 'single']);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideSpacesFix80Cases(): iterable
    {
        yield 'mixed argument' => [
            '<?php function foo( mixed $a ){}',
            '<?php function foo(mixed $a){}',
        ];
    }
}
