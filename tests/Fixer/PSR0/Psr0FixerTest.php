<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR0;

use Symfony\CS\Config\Config;
use Symfony\CS\Fixer\PSR0\Psr0Fixer;

class Psr0FixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFixCase()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer\PSR0;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
namespace Symfony\cs\Fixer\PSR0;
class psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));

        $expected = <<<'EOF'
class Symfony_CS_Fixer_PSR0_Psr0Fixer {}
EOF;
        $input = <<<'EOF'
class symfony_cs_FiXER_PSR0_Psr0FIXer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixClassName()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer\PSR0;
class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
namespace Symfony\CS\Fixer\PSR0;
class blah {}
/* class foo */
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixAbstractClassName()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer\PSR0;
abstract class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
namespace Symfony\CS\Fixer\PSR0;
abstract class blah {}
/* class foo */
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixFinalClassName()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer\PSR0;
final class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
namespace Symfony\CS\Fixer\PSR0;
final class blah {}
/* class foo */
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testHandlePartialNamespaces()
    {
        $fixer = new Psr0Fixer();
        $config = new Config();
        $config->setDir(__DIR__.'/../../../');
        $fixer->setConfig($config);

        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Foo\Bar\Baz\Fixer\PSR0;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
namespace Foo\Bar\Baz\FIXER\PSR0;
class Psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));

        $config->setDir(__DIR__.'/../../../Fixer/PSR0');
        $expected = <<<'EOF'
namespace Foo\Bar\Baz;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
namespace Foo\Bar\Baz;
class Psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixLeadingSpaceNamespace()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace LeadingSpace;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
 namespace LeadingSpace;
class Psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
