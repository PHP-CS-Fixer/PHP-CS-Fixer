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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer
 */
final class PhpdocNoPackageFixerTest extends AbstractFixerTestCase
{
    public function testFixPackage(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @package Foo\Bar
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixSubpackage(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @subpackage Foo\Bar\Baz
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixMany(): void
    {
        $expected = <<<'EOD'
            <?php
            /**
             * Hello!
             */

            EOD;

        $input = <<<'EOD'
            <?php
            /**
             * Hello!
             * @package
             * @subpackage
             */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testDoNothing(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @var package
                 */

            EOD;

        $this->doTest($expected);
    }
}
