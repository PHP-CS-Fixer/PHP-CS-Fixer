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
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return void
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixNull(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return null
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixNullWithEndOnSameLine(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return null */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixNullWithEndOnSameLineNoSpace(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return null*/

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixVoidCaseInsensitive(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return vOId
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixNullCaseInsensitive(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return nULl
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixFull(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello!
     *
     * @param string $foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello!
     *
     * @param string $foo
     * @return void
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNothing(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var null
     */

EOF;

        $this->doTest($expected);
    }

    public function testDoNothingAgain(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return null|int
     */

EOF;

        $this->doTest($expected);
    }

    public function testOtherDoNothing(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return int|null
     */

EOF;

        $this->doTest($expected);
    }

    public function testYetAnotherDoNothing(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return null[]|string[]
     */

EOF;

        $this->doTest($expected);
    }

    public function testHandleSingleLinePhpdoc(): void
    {
        $expected = <<<'EOF'
<?php



EOF;

        $input = <<<'EOF'
<?php

/** @return null */

EOF;

        $this->doTest($expected, $input);
    }
}
