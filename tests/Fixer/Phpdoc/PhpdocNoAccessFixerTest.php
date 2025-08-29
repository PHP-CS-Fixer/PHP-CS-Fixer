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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer>
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpdocNoAccessFixerTest extends AbstractFixerTestCase
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
        yield 'access' => [
            <<<'PHP'
                <?php
                    /**
                     */

                PHP,
            <<<'PHP'
                <?php
                    /**
                     * @access public
                     */

                PHP,
        ];

        yield 'many' => [
            <<<'PHP'
                <?php
                /**
                 * Hello!
                 * @notaccess bar
                 */

                PHP,
            <<<'PHP'
                <?php
                /**
                 * Hello!
                 * @access private
                 * @notaccess bar
                 * @access foo
                 */

                PHP,
        ];

        yield 'do nothing' => [
            <<<'PHP'
                <?php
                    /**
                     * @var access
                     */

                PHP,
        ];
    }
}
