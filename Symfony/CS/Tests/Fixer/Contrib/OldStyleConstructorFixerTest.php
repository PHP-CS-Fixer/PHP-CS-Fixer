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

class OldStyleConstructorFixerTest extends AbstractFixerTestBase
{
    public function testFix()
    {
        $expected = <<<'EOF'
<?php

class SomeOldClass
{
    public function __construct($foo)
    {
    }
}
EOF;

        $input = <<<'EOF'
<?php

class SomeOldClass
{
    public function SomeOldClass($foo)
    {
    }
}
EOF;

        $this->makeTest($expected, $input);
    }

    public function testMixedConstructorsFix()
    {
        $input = <<<'EOF'
<?php

class SomeNewClass
{
    public function __construct($foo)
    {
    }

    public function SomeNewClass($foo)
    {
    }
}
EOF;

        $this->makeTest($input, $input);
    }
}
