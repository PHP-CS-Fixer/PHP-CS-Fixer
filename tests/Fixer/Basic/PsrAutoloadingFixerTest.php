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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\PsrAutoloadingFixer
 */
final class PsrAutoloadingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string            $expected
     * @param null|string       $input
     * @param null|\SplFileInfo $file
     * @param null|string       $dir
     *
     * @dataProvider provideFixCases
     * @dataProvider provideIgnoredCases
     */
    public function testFix($expected, $input = null, $file = null, $dir = null)
    {
        if (null === $file) {
            $file = $this->getTestFile(__FILE__);
        }
        if (null !== $dir) {
            $this->fixer->configure(['dir' => $dir]);
        }

        $this->doTest($expected, $input, $file);
    }

    public function provideFixCases()
    {
        $fileProphecy = $this->prophesize();
        $fileProphecy->willExtend(\SplFileInfo::class);
        $fileProphecy->getBasename('.php')->willReturn('Bar');
        $fileProphecy->getExtension()->willReturn('php');
        $fileProphecy->getRealPath()->willReturn(__DIR__.'/Psr/Foo/Bar.php');
        $file = $fileProphecy->reveal();

        yield [
            '<?php
namespace Psr\foo;
class Bar {}
',
            '<?php
namespace Psr\foo;
class bar {}
',
            $file,
        ];

        yield [
            '<?php
class Psr_Foo_Bar {}
',
            '<?php
class Psr_fOo_bAr {}
',
            $file,
        ];

        yield [
            '<?php
namespace Psr\Foo;
class Bar {}
',
            '<?php
namespace Psr\foo;
class bar {}
',
            $file,
            __DIR__,
        ];

        yield [ // ignore multiple classy in file
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
interface SomeInterfaceToBeUsedInTests {}
class blah {}
/* class foo */',
        ];

        yield [ // ignore multiple namespaces in file
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
interface SomeInterfaceToBeUsedInTests {}
namespace AnotherNamespace;
class blah {}
/* class foo */',
        ];

        yield [ // fix class
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
class PsrAutoloadingFixerTest {}
/* class foo */
',
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
class blah {}
/* class foo */
',
        ];

        yield [ // abstract class
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
abstract class PsrAutoloadingFixerTest {}
/* class foo */
',
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
abstract class blah {}
/* class foo */
',
        ];

        yield [ // final class
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
final class PsrAutoloadingFixerTest {}
/* class foo */
',
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
final class blah {}
/* class foo */
',
        ];

        yield [ // class with comment
            '<?php
namespace /* namespace here */ PhpCsFixer\Fixer\Psr;
class /* hi there */ PsrAutoloadingFixerTest /* why hello */ {}
/* class foo */
',
            '<?php
namespace /* namespace here */ PhpCsFixer\Fixer\Psr;
class /* hi there */ blah /* why hello */ {}
/* class foo */
',
        ];

        yield [ // partial namespace
            '<?php
namespace Foo\Bar\Baz\FIXER\Basic;
class PsrAutoloadingFixer {}
',
            null,
            $this->getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
        ];

        yield [ // partial namespace with comment
            '<?php
namespace /* hi there */ Foo\Bar\Baz\FIXER\Basic;
class /* hi there */ PsrAutoloadingFixer {}
',
            null,
            $this->getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
        ];

        yield [ // partial namespace
            '<?php
namespace Foo\Bar\Baz;
class PsrAutoloadingFixer {}
',
            null,
            $this->getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
        ];

        yield [ // partial namespace with directory
            '<?php
namespace Foo\Bar\Baz\Fixer\Basic;
class PsrAutoloadingFixer {}
',
            '<?php
namespace Foo\Bar\Baz\FIXER\Basic;
class PsrAutoloadingFixer {}
',
            $this->getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
            __DIR__.'/../../../src/',
        ];

        yield [ // partial namespace with comment and directory
            '<?php
namespace /* hi there */ Foo\Bar\Baz\Fixer\Basic;
class /* hi there */ PsrAutoloadingFixer {}
',
            '<?php
namespace /* hi there */ Foo\Bar\Baz\FIXER\Basic;
class /* hi there */ PsrAutoloadingFixer {}
',
            $this->getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
            __DIR__.'/../../../src/',
        ];

        yield [ // partial namespace with directory
            '<?php
namespace Foo\Bar\Baz;
class PsrAutoloadingFixer {}
',
            null,
            $this->getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
            __DIR__.'/../../../src/Fixer/Basic',
        ];
    }

    public function provideIgnoredCases()
    {
        $cases = ['.php', 'Foo.class.php', '4Foo.php', '$#.php'];

        foreach (['__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'try', 'unset', 'use', 'var', 'while', 'xor'] as $keyword) {
            $cases[] = $keyword.'.php';
        }

        foreach (['__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__'] as $magicConstant) {
            $cases[] = $magicConstant.'.php';
            $cases[] = strtolower($magicConstant).'.php';
        }

        foreach ([
            'T_CALLABLE' => 'callable',
            'T_FINALLY' => 'finally',
            'T_INSTEADOF' => 'insteadof',
            'T_TRAIT' => 'trait',
            'T_TRAIT_C' => '__TRAIT__',
        ] as $tokenType => $tokenValue) {
            if (\defined($tokenType)) {
                $cases[] = $tokenValue.'.php';
                $cases[] = strtolower($tokenValue).'.php';
            }
        }

        return array_map(function ($case) {
            return [
                '<?php
namespace Aaa;
class Bar {}',
                null,
                $this->getTestFile($case),
            ];
        }, $cases);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires     PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input, $this->getTestFile(__FILE__));
    }

    public function provideFix70Cases()
    {
        yield [ // class with anonymous class
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
class PsrAutoloadingFixerTest {
    public function foo() {
        return new class() implements FooInterface {};
    }
}
',
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
class stdClass {
    public function foo() {
        return new class() implements FooInterface {};
    }
}
',
        ];

        yield [ // ignore anonymous class
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
new class implements Countable {};
',
        ];

        yield [ // ignore anonymous class
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
new class extends stdClass {};
',
        ];

        yield [ // ignore multiple classy in file with anonymous class between them
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
class ClassOne {};
new class extends stdClass {};
class ClassTwo {};
',
        ];
    }
}
