<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class PhpdocNoEmptyReturnFixerTest extends AbstractFixerTestBase
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
    }

    public function testDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var null
     */

EOF;

        $this->makeTest($expected);
    }

    public function testDoNothingAgain()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return null|int
     */

EOF;

        $this->makeTest($expected);
    }

    public function testOtherDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return int|null
     */

EOF;

        $this->makeTest($expected);
    }
}
