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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer
 */
final class PhpdocNoAccessFixerTest extends AbstractFixerTestCase
{
    public function testFixAccess(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @access public
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
             * @notaccess bar
             */

            EOD;

        $input = <<<'EOD'
            <?php
            /**
             * Hello!
             * @access private
             * @notaccess bar
             * @access foo
             */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testDoNothing(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @var access
                 */

            EOD;

        $this->doTest($expected);
    }
}
