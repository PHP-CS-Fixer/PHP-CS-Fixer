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

namespace PhpCsFixer\Tests\Fixer\ExceptionHandling;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ExceptionHandling\NoUselessTryCatchFinallyFixer
 */
final class NoUselessTryCatchFinallyFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield 'simple - catch & throw' => [
            '<?php
                     '.'
                        foo();
                        '.'
                         '.'
                    '.'
                ',
            '<?php
                    try {
                        foo();
                    } catch (\Exception $e) {
                        throw $e;
                    }
                ',
        ];

        yield 'catch multiple types' => [
            '<?php
                 '.'
                    throw new MyException();
                      '.'
                     '.'
                '.'
            ',
            '<?php
                try {
                    throw new MyException();
                } catch (MyException | MyOtherException $e) {
                    throw $e;
                }
            ',
        ];

        yield 'simple empty finally - with catch' => [
            '<?php
                 '.'
                    foo();
                   '.'
                     '.'
                  '.'
                    // some comment
                '.'
            ',
            '<?php
                try {
                    foo();
                } catch(\Exception $e) {
                    throw $e;
                } finally {
                    // some comment
                }
            ',
        ];

        yield 'simple empty finally - no catch' => [
            '<?php
                 '.'
                    foo();
                  '.'
                '.'
            ',
            '<?php
                try {
                    foo();
                } finally {
                }
            ',
        ];

        yield 'multiple catch + finally' => [
            '<?php
             '.'
                foo();
                 // 1
                 '.'
                 // 2
                 '.'
                 // 3
                 '.'
                   // 4
                 '.'
                 // 5
                 '.'
                 // 6
                 '.'
               // 7
            ',
            '<?php
            try {
                foo();
            } catch (E $e) { // 1
                throw $e;
            } catch (F $e) { // 2
                throw $e;
            } catch (G $g) { // 3
                throw $g;
            } catch (E1 | T $e1) { // 4
                throw $e1;
            } catch (E2|R|F $e) { // 5
                throw $e;
            } catch (E3 $e) { // 6
                throw $e;
            } finally {} // 7
            ',
        ];

        yield 'multiple catch, but not all + finally' => [
            '<?php
            try {
                foo();
            } catch (E $e) { // 11
                throw $e;
            } catch (F $e) { // 21
                echo 1;
            } catch (G $g) { // 31
                throw $g;
            } catch (E1 $e1) { // 41
                throw $e1;
            } catch (E2 $e) { // 51
                echo 3;
            } catch (E3 $e) { // 61
                throw $e;
            } /*A*/  // 71
            ',
            '<?php
            try {
                foo();
            } catch (E $e) { // 11
                throw $e;
            } catch (F $e) { // 21
                echo 1;
            } catch (G $g) { // 31
                throw $g;
            } catch (E1 $e1) { // 41
                throw $e1;
            } catch (E2 $e) { // 51
                echo 3;
            } catch (E3 $e) { // 61
                throw $e;
            } /*A*/finally {} // 71
            ',
        ];

        yield 'nested' => [
            '<?php
            try {
                foo99();
            } catch (E $e) {
                try {
                    f();
                } catch (Z|A|B $f) {
                    try {
                        $z = b();
                        ++$z;
                    } finally {
                         '.'
                            $a."aa"(99);
                            '.'
                             '.'
                        '.'
                    }

                     '.'
                        $tt->a(1);
                           '.'
                }
            }  '.'
            '.'
            ',
            '<?php
            try {
                foo99();
            } catch (E $e) {
                try {
                    f();
                } catch (Z|A|B $f) {
                    try {
                        $z = b();
                        ++$z;
                    } finally {
                        try {
                            $a."aa"(99);
                        } catch (Z $d) {
                            throw $d;
                        }
                    }

                    try {
                        $tt->a(1);
                    } catch (T $y) { throw $y; }
                }
            } finally {
            }
            ',
        ];

        yield 'not empty finally' => ['<?php try { foo(); } catch (\Exception $e) { throw $e; } finally { echo 1; }'];

        yield 'op before throw' => ['<?php try { foo(); } catch (\Exception $e) { z(); throw $e; }'];

        yield 'op after throw' => ['<?php try { foo(); } catch (\Exception $e) { throw $e(); }'];

        yield 'throw something else I' => ['<?php try { foo(); } catch (\Exception $e) { throw bar($e); }'];

        yield 'throw something else II' => ['<?php $z = f(); try { foo(); } catch (\Exception $e) { throw $z; }'];

        yield 'nested, no candidates' => [
            '<?php
try {
    return 1;
} catch (RunException $exception) {
    try {
        return 2;
    } catch (RunException $exception) {
        return 3;
    }
}
',
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider providePhp80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function providePhp80Cases(): iterable
    {
        yield 'no caught var with op' => [
            '<?php
            try {
                test();
            } catch (SpecificException) {
                echo 123;
            }
            ',
        ];

        yield 'no caught var, no op' => [
            '<?php
            try {
                test();
            } catch (SpecificException) {

            }
            ',
        ];
    }
}
