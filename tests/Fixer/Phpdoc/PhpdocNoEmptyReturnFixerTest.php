<?php

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
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer
 */
final class PhpdocNoEmptyReturnFixerTest extends AbstractFixerTestCase
{
    public function testFixVoid()
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

    public function testFixNull()
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

    public function testFixFull()
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

    public function testDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var null
     */

EOF;

        $this->doTest($expected);
    }

    public function testDoNothingAgain()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return null|int
     */

EOF;

        $this->doTest($expected);
    }

    public function testOtherDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return int|null
     */

EOF;

        $this->doTest($expected);
    }
}
