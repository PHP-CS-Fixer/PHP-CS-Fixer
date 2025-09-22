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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer>
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer
 */
final class NoEmptyPhpdocFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'multiple PHPdocs' => [
            '<?php
                    /** a */

                    '.'

                    '.'

                    '.'

                    '.'
                    /**
                     * test
                     */

                     /** *test* */
                ',
            '<?php
                    /**  *//** a *//**  */

                    /**
                    */

                    /**
                     *
                     */

                    /** ***
                     *
                     ******/

                    /**
**/
                    /**
                     * test
                     */

                     /** *test* */
                ',
        ];

        yield 'PHPDoc on its own line' => [
            <<<'PHP'
                <?php
                echo $a;
                echo $b;
                PHP,
            <<<'PHP'
                <?php
                echo $a;
                /** */
                echo $b;
                PHP,
        ];

        yield 'PHPDoc on its own line with empty line before' => [
            <<<'PHP'
                <?php
                function f() {
                    echo $a;

                    echo $b;
                }
                PHP,
            <<<'PHP'
                <?php
                function f() {
                    echo $a;

                    /** */
                    echo $b;
                }
                PHP,
        ];

        yield 'PHPDoc on its own line with empty line after' => [
            <<<'PHP'
                <?php
                echo $a;

                echo $b;
                PHP,
            <<<'PHP'
                <?php
                echo $a;
                /** */

                echo $b;
                PHP,
        ];

        yield 'PHPDoc on its own line with empty line before and after' => [
            <<<'PHP'
                <?php
                echo $a;


                echo $b;
                PHP,
            <<<'PHP'
                <?php
                echo $a;

                /** */

                echo $b;
                PHP,
        ];

        yield 'PHPDoc with empty line before and content after' => [
            <<<'PHP'
                <?php
                function f() {
                    echo $a;
                    echo $b;
                }
                PHP,
            <<<'PHP'
                <?php
                function f() {
                    echo $a;
                    /** */echo $b;
                }
                PHP,
        ];

        yield 'PHPDoc with content before and empty line after' => [
            <<<'PHP'
                <?php
                function f() {
                    echo $a;
                    echo $b;
                }
                PHP,
            <<<'PHP'
                <?php
                function f() {
                    echo $a;/** */
                    echo $b;
                }
                PHP,
        ];

        yield 'PHPDoc after open tag - the same line' => [
            '<?php '.'
                foo();
                ',
            '<?php /** */
                foo();
                ',
        ];

        yield 'PHPDoc after open tag - next line' => [
            <<<'PHP'
                <?php
                foo();
                PHP,
            <<<'PHP'
                <?php
                /** */
                foo();
                PHP,
        ];

        yield 'PHPDoc after open tag - next next next line' => [
            <<<'PHP'
                <?php


                foo();
                PHP,
            <<<'PHP'
                <?php


                /** */
                foo();
                PHP,
        ];
    }
}
