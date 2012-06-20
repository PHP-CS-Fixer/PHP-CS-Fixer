<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\Psr0Fixer;

class Psr0FixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFixCase()
    {
        $fixer = new Psr0Fixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
namespace Symfony\CS\Tests\Fixer;
class Psr0FixerTest {}
EOF;
        $input = <<<'EOF'
namespace Symfony\cs\Tests\Fixer;
class psr0FixerteST {}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));

        $expected = <<<'EOF'
class Symfony_CS_Tests_Fixer_Psr0FixerTest {}
EOF;
        $input = <<<'EOF'
class symfony_cs_tests_FiXER_Psr0FIXerTest {}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testFixClassName()
    {
        $fixer = new Psr0Fixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
namespace Symfony\CS\Tests\Fixer;
class Psr0FixerTest {}
/* class foo */
EOF;
        $input = <<<'EOF'
namespace Symfony\CS\Tests\Fixer;
class blah {}
/* class foo */
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFixNamespaceThrows()
    {
        $fixer = new Psr0Fixer();
        $file = new \SplFileInfo(__FILE__);

        $input = <<<'EOF'
namespace lala;
class Psr0FixerTest {}
EOF;

        $fixer->fix($file, $input);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFixOldClassnameThrows()
    {
        $fixer = new Psr0Fixer();
        $file = new \SplFileInfo(__FILE__);

        $input = <<<'EOF'
class blah {}
EOF;

        $fixer->fix($file, $input);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testMissingVendorThrows()
    {
        $fixer = new Psr0Fixer();
        $file = new \SplFileInfo(__FILE__);

        $input = <<<'EOF'
class Psr0FixerTest {}
EOF;

        $fixer->fix($file, $input);
    }
}
