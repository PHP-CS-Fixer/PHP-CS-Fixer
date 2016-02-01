<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Contrib;

use PhpCsFixer\Config;
use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class Psr0FixerTest extends AbstractFixerTestCase
{
    public function testFixCase()
    {
        $fixer = $this->getFixer();
        $config = new Config();
        $config->setDir(__DIR__.'/../../Fixtures/FixerTest/');
        $fixer->setConfig($config);

        $file = $this->getMock('SplFileInfo', array('getRealPath'), array(__DIR__.'/Psr0/Foo/Bar.php'));
        $file->expects($this->any())->method('getRealPath')->willReturn(__DIR__.'/Psr0/Foo/Bar.php');

        $expected = <<<'EOF'
<?php
namespace Psr0\Foo;
class Bar {}
EOF;
        $input = <<<'EOF'
<?php
namespace Psr0\foo;
class bar {}
EOF;

        $this->doTest($expected, $input, $file, $fixer);

        $expected = <<<'EOF'
<?php
class Psr0_Foo_Bar {}
EOF;
        $input = <<<'EOF'
<?php
class Psr0_fOo_bAr {}
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixClassName()
    {
        $file = $this->getTestFile(__FILE__);

        $expected = <<<'EOF'
<?php
namespace PhpCsFixer\Tests\Fixer\Contrib;
class Psr0FixerTest {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace PhpCsFixer\Tests\Fixer\Contrib;
class blah {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixAbstractClassName()
    {
        $file = $this->getTestFile(__FILE__);

        $expected = <<<'EOF'
<?php
namespace PhpCsFixer\Tests\Fixer\Contrib;
abstract class Psr0FixerTest {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace PhpCsFixer\Tests\Fixer\Contrib;
abstract class blah {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixFinalClassName()
    {
        $file = $this->getTestFile(__FILE__);

        $expected = <<<'EOF'
<?php
namespace PhpCsFixer\Tests\Fixer\Contrib;
final class Psr0FixerTest {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace PhpCsFixer\Tests\Fixer\Contrib;
final class blah {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixClassNameWithComment()
    {
        $file = $this->getTestFile(__FILE__);

        $expected = <<<'EOF'
<?php
namespace /* namespace here */ PhpCsFixer\Fixer\PSR0;
class /* hi there */ Psr0FixerTest /* why hello */ {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace /* namespace here */ PhpCsFixer\Fixer\PSR0;
class /* hi there */ blah /* why hello */ {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testHandlePartialNamespaces()
    {
        $fixer = $this->getFixer();
        $config = new Config();
        $config->setDir(__DIR__.'/../../../src/');
        $fixer->setConfig($config);

        $file = $this->getTestFile(__DIR__.'/../../../src/Fixer/Contrib/Psr0Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz\Fixer\Contrib;
class Psr0Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace Foo\Bar\Baz\FIXER\Contrib;
class Psr0Fixer {}
EOF;
        $this->doTest($expected, $input, $file, $fixer);

        $expected = <<<'EOF'
<?php
namespace /* hi there */ Foo\Bar\Baz\Fixer\Contrib;
class /* hi there */ Psr0Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace /* hi there */ Foo\Bar\Baz\FIXER\Contrib;
class /* hi there */ Psr0Fixer {}
EOF;
        $this->doTest($expected, $input, $file, $fixer);

        $config->setDir(__DIR__.'/../../../src/Fixer/Contrib');
        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz;
class Psr0Fixer {}
EOF;
        $this->doTest($expected, null, $file, $fixer);
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

        $this->doTest($expected, null, $file);
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
