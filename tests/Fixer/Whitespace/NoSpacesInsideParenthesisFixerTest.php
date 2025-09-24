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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer>
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer
 *
 * @author Marc Aubé
 */
final class NoSpacesInsideParenthesisFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
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
    }

    public function testLeaveNewLinesAlone(): void
    {
        $expected = <<<'EOF'
            <?php

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
            EOF;
        $this->doTest($expected);
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php function foo(mixed $a){}',
            '<?php function foo( mixed $a ){}',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'first callable class' => [
            '<?php $a = strlen(...);',
            '<?php $a = strlen( ... );',
        ];
    }
}
