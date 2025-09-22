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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\NoUselessPrintfFixer>
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoUselessPrintfFixer
 *
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 */
final class NoUselessPrintfFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'single-quoted' => [
            '<?php print \'bar\';',
            '<?php printf(\'bar\');',
        ];

        yield 'double-quoted' => [
            '<?php print "bar\n";',
            '<?php printf("bar\n");',
        ];

        yield 'case insensitive' => [
            '<?php print "bar";',
            '<?php PrInTf("bar");',
        ];

        yield 'trailing comma' => [
            '<?php print "bar";',
            '<?php printf("bar",);',
        ];

        yield 'leading NS separator' => [
            '<?php print "bar";',
            '<?php \printf("bar");',
        ];

        yield 'skip not global function' => [
            <<<'PHP'
                <?php
                namespace Foo {
                    function printf($arg) {}
                }

                namespace Bar {
                    use function Foo\printf;
                    printf('bar');
                }
                PHP,
        ];

        yield 'skip function cases' => [
            <<<'PHP'
                <?php
                printf();
                printf('%s%s', 'foo', 'bar');
                \Foo\printf('bar');
                printf(...$bar);
                PHP,
        ];

        yield 'skip class cases' => [
            <<<'PHP'
                <?php
                class Foo {
                    public function printf($arg) {}
                }

                class Bar {
                    public static function printf($arg) {}
                }

                (new Foo())->printf('bar');
                Bar::printf('bar');
                PHP,
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'skip first class callable' => [
            '<?php return printf(...);',
        ];
    }
}
