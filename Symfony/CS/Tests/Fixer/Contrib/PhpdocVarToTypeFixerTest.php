<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class PhpdocVarToTypeFixerTest extends AbstractFixerTestBase
{
    public function testBasicFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @type string Hello!
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var string Hello!
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testNoChanges()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @type string Hello!
     */

EOF;

        $this->makeTest($expected);
    }

    public function testSingleLine()
    {
        $this->makeTest('<?php /** @type string Hello! */', '<?php /** @var string Hello! */');
    }

    public function testEmpty()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->makeTest($expected);
    }
}
