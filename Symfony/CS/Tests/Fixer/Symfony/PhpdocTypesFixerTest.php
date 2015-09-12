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
 */
final class PhpdocTypesFixerTest extends AbstractFixerTestBase
{
    public function testConvesion()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param boolean|array|Foo $bar
     *
     * @return int|float
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Boolean|Array|Foo $bar
     *
     * @return inT|Float
     */

EOF;
        $this->makeTest($expected, $input);
    }

    public function testArrayStuff()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param string|string[] $bar
     *
     * @return int[]
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param STRING|String[] $bar
     *
     * @return inT[]
     */

EOF;
        $this->makeTest($expected, $input);
    }

    public function testMixedAndVoid()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param mixed $foo
     *
     * @return void
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Mixed $foo
     *
     * @return Void
     */

EOF;
        $this->makeTest($expected, $input);
    }
}
