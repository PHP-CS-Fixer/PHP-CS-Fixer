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

namespace Symfony\CS\Tests\Fixer\PSR0;

use Symfony\CS\Config\Config;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class Psr0FixerTest extends AbstractFixerTestBase
{
    public function testFixCase()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR0;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\cs\Fixer\PSR0;
class psr0Fixer {}
EOF;

        $this->makeTest($expected, $input, $file);

        $expected = <<<'EOF'
<?php
class Symfony_CS_Fixer_PSR0_Psr0Fixer {}
EOF;
        $input = <<<'EOF'
<?php
class symfony_cs_FiXER_PSR0_Psr0FIXer {}
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testFixClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR0;
class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR0;
class blah {}
/* class foo */
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testFixAbstractClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR0;
abstract class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR0;
abstract class blah {}
/* class foo */
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testFixFinalClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR0;
final class Psr0Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR0;
final class blah {}
/* class foo */
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testFixClassNameWithComment()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
<?php
namespace /* namespace here */ Symfony\CS\Fixer\PSR0;
class /* hi there */ Psr0Fixer /* why hello */ {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace /* namespace here */ Symfony\CS\Fixer\PSR0;
class /* hi there */ blah /* why hello */ {}
/* class foo */
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testHandlePartialNamespaces()
    {
        $fixer = $this->getFixer();
        $config = new Config();
        $config->setDir(__DIR__.'/../../../');
        $fixer->setConfig($config);

        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR0/Psr0Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz\Fixer\PSR0;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace Foo\Bar\Baz\FIXER\PSR0;
class Psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));

        $expected = <<<'EOF'
<?php
namespace /* hi there */ Foo\Bar\Baz\Fixer\PSR0;
class /* hi there */ Psr0Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace /* hi there */ Foo\Bar\Baz\FIXER\PSR0;
class /* hi there */ Psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));

        $config->setDir(__DIR__.'/../../../Fixer/PSR0');
        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace Foo\Bar\Baz;
class Psr0Fixer {}
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    /**
     * @dataProvider provideIgnoredCases
     */
    public function testIgnoreWrongNames($filename)
    {
        $file = $this->getTestFile($filename);

        $expected = <<<'EOF'
<?php
namespace Aaa;
class Bar {}
EOF;

        $this->makeTest($expected, null, $file);
    }

    public function provideIgnoredCases()
    {
        $ignoreCases = array(
            array('.php'),
            array('Foo.class.php'),
            array('4Foo.php'),
            array('$#.php'),
        );

        foreach (array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'try', 'unset', 'use', 'var', 'while', 'xor') as $keyword) {
            $ignoreCases[] = array($keyword.'.php');
        }

        foreach (array('__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__') as $magicConstant) {
            $ignoreCases[] = array($magicConstant.'.php');
            $ignoreCases[] = array(strtolower($magicConstant).'.php');
        }

        foreach (array(
            'T_CALLABLE' => 'callable',
            'T_FINALLY' => 'finally',
            'T_INSTEADOF' => 'insteadof',
            'T_TRAIT' => 'trait',
            'T_TRAIT_C' => '__TRAIT__',
        ) as $tokenType => $tokenValue) {
            if (defined($tokenType)) {
                $ignoreCases[] = array($tokenValue.'.php');
                $ignoreCases[] = array(strtolower($tokenValue).'.php');
            }
        }

        return $ignoreCases;
    }
}
