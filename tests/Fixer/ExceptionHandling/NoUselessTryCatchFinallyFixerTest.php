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

        yield 'not empty finally' => ['<?php try { foo(); } catch (\Exception $e) { throw $e; } finally { echo 1; }'];

        yield 'op before throw' => ['<?php try { foo(); } catch (\Exception $e) { z(); throw $e; }'];

        yield 'op after throw' => ['<?php try { foo(); } catch (\Exception $e) { throw $e(); }'];

        yield 'throw something else I' => ['<?php try { foo(); } catch (\Exception $e) { throw bar($e); }'];

        yield 'throw something else II' => ['<?php $z = f(); try { foo(); } catch (\Exception $e) { throw $z; }'];
    }

    /**
     * @requires PHP 8.0
     * @dataProvider providePhp80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function providePhp80Cases(): \Generator
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
