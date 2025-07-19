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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer>
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class PhpdocNoPackageFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'package' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @package Foo\Bar
                     */

                EOF,
        ];

        yield 'subpackage' => [
            <<<'EOF'
                <?php
                    /**
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * @subpackage Foo\Bar\Baz
                     */

                EOF,
        ];

        yield 'many' => [
            <<<'EOF'
                <?php
                /**
                 * Hello!
                 */

                EOF,
            <<<'EOF'
                <?php
                /**
                 * Hello!
                 * @package
                 * @subpackage
                 */

                EOF,
        ];

        yield 'do nothing' => [
            <<<'EOF'
                <?php
                    /**
                     * @var package
                     */

                EOF,
        ];
    }
}
