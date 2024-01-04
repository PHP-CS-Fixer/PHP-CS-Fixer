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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer
 */
final class PhpdocNoEmptyReturnFixerTest extends AbstractFixerTestCase
{
    public function testFixVoid(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @return void
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixNull(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @return null
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixNullWithEndOnSameLine(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @return null */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixNullWithEndOnSameLineNoSpace(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @return null*/

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixVoidCaseInsensitive(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @return vOId
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixNullCaseInsensitive(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @return nULl
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixFull(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello!
                 *
                 * @param string $foo
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello!
                 *
                 * @param string $foo
                 * @return void
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testDoNothing(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @var null
                 */

            EOD;

        $this->doTest($expected);
    }

    public function testDoNothingAgain(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @return null|int
                 */

            EOD;

        $this->doTest($expected);
    }

    public function testOtherDoNothing(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @return int|null
                 */

            EOD;

        $this->doTest($expected);
    }

    public function testYetAnotherDoNothing(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @return null[]|string[]
                 */

            EOD;

        $this->doTest($expected);
    }

    public function testHandleSingleLinePhpdoc(): void
    {
        $expected = <<<'EOD'
            <?php



            EOD;

        $input = <<<'EOD'
            <?php

            /** @return null */

            EOD;

        $this->doTest($expected, $input);
    }
}
