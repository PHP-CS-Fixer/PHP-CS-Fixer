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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoUselessSprintfFixer
 */
final class NoUselessSprintfFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        yield 'simple' => [
            '<?php echo "bar";',
            '<?php echo sprintf("bar");',
        ];

        yield 'simple II' => [
            "<?php echo 'bar' ?>",
            "<?php echo sprintf('bar') ?>",
        ];

        yield 'simple III' => [
            '<?php echo $bar;',
            '<?php echo sprintf($bar);',
        ];

        yield 'simple IV' => [
            '<?php echo 1;',
            '<?php echo sprintf(1);',
        ];

        yield 'minimal' => [
            '<?php $foo;',
            '<?php sprintf($foo);',
        ];

        yield 'case insensitive' => [
            '<?php echo "bar";',
            '<?php echo SPRINTF("bar");',
        ];

        yield 'nested' => [
            '<?php echo /* test */"bar";',
            '<?php echo sprintf(sprintf(sprintf(/* test */sprintf(sprintf(sprintf("bar"))))));',
        ];

        yield [
            '<?php namespace Foo {
                function sprintf($foo) {
                    echo $foo;
                }
            }',
        ];

        yield [
            '<?php namespace Foo;
                function sprintf($foo) {
                    echo $foo;
                }
            ',
        ];

        yield [
            '<?php
                echo \Foo\sprintf("abc");
                echo $bar->sprintf($foo);
                echo Bar::sprintf($foo);
                echo sprint(...$foo);
                echo sprint("%d", 1);
                echo sprint("%d%d%d", 1, 2, 3);
                echo sprint();
            ',
        ];

        if (\PHP_VERSION_ID >= 70000) {
            yield [
                '<?php echo sprint[2]("foo");
            ',
            ];
        }

        if (\PHP_VERSION_ID > 70300) {
            yield 'trailing comma' => [
                '<?php echo "bar";',
                '<?php echo sprintf("bar",);',
            ];
        }

        if (\PHP_VERSION_ID < 80000) {
            yield [
                '<?php echo  "bar";',
                '<?php echo \ sprintf("bar");',
            ];

            yield [
                '<?php echo A /* 1 */ \ B \ sprintf("bar");',
            ];
        }
    }
}
