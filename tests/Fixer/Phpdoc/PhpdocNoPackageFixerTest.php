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
        $expected = <<<'EOF'
            <?php
                /**
                 */

            EOF;

        $input = <<<'EOF'
            <?php
                /**
                 * @package Foo\Bar
                 */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixSubpackage(): void
    {
        $expected = <<<'EOF'
            <?php
                /**
                 */

            EOF;

        $input = <<<'EOF'
            <?php
                /**
                 * @subpackage Foo\Bar\Baz
                 */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMany(): void
    {
        $expected = <<<'EOF'
            <?php
            /**
             * Hello!
             */

            EOF;

        $input = <<<'EOF'
            <?php
            /**
             * Hello!
             * @package
             * @subpackage
             */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNothing(): void
    {
        $expected = <<<'EOF'
            <?php
                /**
                 * @var package
                 */

            EOF;

        $this->doTest($expected);
    }
}
