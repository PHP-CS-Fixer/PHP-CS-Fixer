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

use Symfony\CS\Config\Config;
use Symfony\CS\Fixer\Psr0Fixer;

class Psr0FixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFixCase()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
namespace Symfony\cs\Fixer;
class psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));

        $expected = <<<'EOF'
class Symfony_CS_Fixer_Psr0Fixer {}
EOF;
        $input = <<<'EOF'
class symfony_cs_FiXER_Psr0FIXer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixClassName()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer;
class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
namespace Symfony\CS\Fixer;
class blah {}
/* class foo */
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixAbstractClassName()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer;
abstract class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
namespace Symfony\CS\Fixer;
abstract class blah {}
/* class foo */
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixFinalClassName()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Symfony\CS\Fixer;
final class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
namespace Symfony\CS\Fixer;
final class blah {}
/* class foo */
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixNamespaceThrows()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $input = <<<'EOF'
namespace lala;
class Psr0Fixer {}
EOF;

        $expected = '! The namespace lala in';
        ob_start();
        $fixer->fix($file, $input);
        $this->assertContains($expected, ob_get_clean());
    }

    public function testFixOldClassnameThrows()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $input = <<<'EOF'
class blah_bar {}
EOF;

        $expected = '! The class blah_bar in';
        ob_start();
        $fixer->fix($file, $input);
        $this->assertContains($expected, ob_get_clean());
    }

    public function testMissingVendorThrows()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $input = <<<'EOF'
class Psr0Fixer {}
EOF;

        $expected = '! Class Psr0Fixer in';
        ob_start();
        $fixer->fix($file, $input);
        $this->assertContains($expected, ob_get_clean());
    }

    public function testHandlePartialNamespaces()
    {
        $fixer = new Psr0Fixer();
        $config = new Config();
        $config->setDir(__DIR__.'/../../');
        $fixer->setConfig($config);

        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace Foo\Bar\Baz\Fixer;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
namespace Foo\Bar\Baz\FIXER;
class Psr0Fixer {}
EOF;

        ob_start();
        $this->assertSame($expected, $fixer->fix($file, $input));
        $this->assertSame('', ob_get_clean());

        $config->setDir(__DIR__.'/../../Fixer');
        $expected = <<<'EOF'
namespace Foo\Bar\Baz;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
namespace Foo\Bar\Baz;
class Psr0Fixer {}
EOF;

        ob_start();
        $this->assertSame($expected, $fixer->fix($file, $input));
        $this->assertSame('', ob_get_clean());
    }

    public function testLeadingSpaceNamespaceThrows()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $input = <<<'EOF'
 namespace LeadingSpace;
class Psr0Fixer {}
EOF;

        $expected = '! The namespace LeadingSpace in';
        ob_start();
        $fixer->fix($file, $input);
        $this->assertContains($expected, ob_get_clean());
    }

    public function testFixLeadingSpaceNamespace()
    {
        $fixer = new Psr0Fixer();
        $file = $this->getTestFile(__DIR__.'/../../Fixer/Psr0Fixer.php');

        $expected = <<<'EOF'
namespace LeadingSpace;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
 namespace LeadingSpace;
class Psr0Fixer {}
EOF;
        ob_start();
        $this->assertSame($expected, $fixer->fix($file, $input));
        ob_clean();
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
