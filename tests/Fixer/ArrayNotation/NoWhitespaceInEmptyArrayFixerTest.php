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
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceInEmptyArrayFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceInEmptyArrayFixer>
 *
 * @author Jeremiasz Major <jrh.mjr@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoWhitespaceInEmptyArrayFixerTest extends AbstractFixerTestCase
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
            "<?php\n\n\$foo = [];\n",
            "<?php\n\n\$foo = [\n];\n",
        ];

        yield [
            "<?php\n\n\$foo = [];\n",
            "<?php\n\n\$foo = [      ];\n",
        ];

        yield [
            "<?php\n\n\$foo = [];\n",
            "<?php\n\n\$foo = [\n\n];\n",
        ];

        yield [
            "<?php\n\n\$foo = [\n    // foo\n];\n",
        ];

        yield [
            "<?php\n\n\$foo = [];\n",
            "<?php\n\n\$foo = [\n    \n];\n",
        ];

        yield [
            "<?php\n\n\$foo = [ /* test */ ];\n",
        ];

        yield [
            <<<'PHP'
                <?php class Foo {
                    private const Foo = [];
                }
                PHP,
            <<<'PHP'
                <?php class Foo {
                    private const Foo = [
                    ];
                }
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php class Foo {
                    public function bar(): void {
                        $foo = [];
                    }
                }
                PHP,
            <<<'PHP'
                <?php class Foo {
                    public function bar(): void {
                        $foo = [   ];
                    }
                }
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php class Foo {
                    public array $ignore = [];
                }
                PHP,
            <<<'PHP'
                <?php class Foo {
                    public array $ignore = [
                    ];
                }
                PHP,
        ];
    }
}
