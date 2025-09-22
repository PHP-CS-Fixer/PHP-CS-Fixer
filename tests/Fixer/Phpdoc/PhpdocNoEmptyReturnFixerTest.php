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
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer>
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class PhpdocNoEmptyReturnFixerTest extends AbstractFixerTestCase
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
        yield 'void' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @return void
                     */

                EOF,
        ];

        yield 'null' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @return null
                     */

                EOF,
        ];

        yield 'null with end on the same line' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @return null */

                EOF,
        ];

        yield 'null with end on the same line no space' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @return null*/

                EOF,
        ];

        yield 'void case insensitive' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @return vOId
                     */

                EOF,
        ];

        yield 'null case insensitive' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @return nULl
                     */

                EOF,
        ];

        yield 'full' => [
            <<<'EOF'
                <?php
                    /**
                     * Hello!
                     *
                     * @param string $foo
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * Hello!
                     *
                     * @param string $foo
                     * @return void
                     */

                EOF,
        ];

        yield 'do nothing' => [<<<'EOF'
            <?php
                /**
                 * @var null
                 */

            EOF];

        yield 'do nothing again' => [<<<'EOF'
            <?php
                /**
                 * @return null|int
                 */

            EOF];

        yield 'other do nothing' => [<<<'EOF'
            <?php
                /**
                 * @return int|null
                 */

            EOF];

        yield 'yet another do nothing' => [<<<'EOF'
            <?php
                /**
                 * @return null[]|string[]
                 */

            EOF];

        yield 'handle single line phpdoc' => [
            <<<'EOF'
                <?php



                EOF,
            <<<'EOF'
                <?php

                /** @return null */

                EOF,
        ];
    }
}
