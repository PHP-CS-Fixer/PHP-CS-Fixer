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

namespace PhpCsFixer\Tests\Fixer\Internal;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Internal\PhpUnitRequiresWithCaretFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Internal\PhpUnitRequiresWithCaretFixer>
 */
final class PhpUnitRequiresWithCaretFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            <<<'PHP'
                <?php
                /**
                 * @requires PHP ^8.1
                 */
                final class FooTest extends TestCase {
                    public function testSomething1() {}
                    /**
                     * @requires PHP ^8.2
                     */
                    public function testSomething2() {}
                    /**
                     * @requires PHP ^8.0
                     */
                    public function testSomething3() {}
                    /**
                     * @requires PHP ^8.3
                     */
                    public function testSomething4() {}
                }

                PHP,
            <<<'PHP'
                <?php
                /**
                 * @requires PHP 8.1
                 */
                final class FooTest extends TestCase {
                    public function testSomething1() {}
                    /**
                     * @requires PHP 8.2
                     */
                    public function testSomething2() {}
                    /**
                     * @requires PHP 8
                     */
                    public function testSomething3() {}
                    /**
                     * @requires    PHP    8.3
                     */
                    public function testSomething4() {}
                }

                PHP,
        ];
    }
}
