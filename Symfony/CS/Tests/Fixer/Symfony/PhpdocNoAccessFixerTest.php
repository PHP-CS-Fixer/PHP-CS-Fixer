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
final class PhpdocNoAccessFixerTest extends AbstractFixerTestBase
{
    public function testFixAccess()
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @access public
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixMany()
    {
        $expected = <<<'EOF'
<?php
/**
 * Hello!
 * @notaccess bar
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * Hello!
 * @access private
 * @notaccess bar
 * @access foo
 */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var access
     */

EOF;

        $this->makeTest($expected);
    }
}
