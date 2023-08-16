<?php

declare(strict_types=1);

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
use Prophecy\Prophet;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\PsrAutoloadingFixer
 */
final class PsrAutoloadingFixerTest extends AbstractFixerTestCase
{
    /**
     * This is new test method, to replace old one some day.
     *
     * @dataProvider provideFixNewCases
     */
    public function testFixNew(string $expected, ?string $input = null, ?string $dir = null): void
    {
        if (null !== $dir) {
            $this->fixer->configure(['dir' => $dir]);
        }

        $this->doTest($expected, $input, self::getTestFile(__FILE__));
    }

    public static function provideFixNewCases(): iterable
    {
        foreach (['class', 'interface', 'trait'] as $element) {
            yield sprintf('%s with originally short name', $element) => [
                sprintf('<?php %s PsrAutoloadingFixerTest {}', $element),
                sprintf('<?php %s Foo {}', $element),
            ];
        }

        yield 'abstract class' => [
            '<?php abstract class PsrAutoloadingFixerTest {}',
            '<?php abstract class WrongName {}',
        ];

        yield 'final class' => [
            '<?php final class PsrAutoloadingFixerTest {}',
            '<?php final class WrongName {}',
        ];

        yield 'class with originally long name' => [
            '<?php class PsrAutoloadingFixerTest {}',
            '<?php class FooFooFooFooFooFooFooFooFooFooFooFooFoo {}',
        ];

        yield 'class with wrong casing' => [
            '<?php class PsrAutoloadingFixerTest {}',
            '<?php class psrautoloadingfixertest {}',
        ];

        yield 'namespaced class with wrong casing' => [
            '<?php namespace Foo; class PsrAutoloadingFixerTest {}',
            '<?php namespace Foo; class psrautoloadingfixertest {}',
        ];

        yield 'class with wrong casing (1 level namespace)' => [
            '<?php class Basic_PsrAutoloadingFixerTest {}',
            '<?php class BASIC_PSRAUTOLOADINGFIXERTEST {}',
        ];

        yield 'class with wrong casing (2 levels namespace)' => [
            '<?php class Fixer_Basic_PsrAutoloadingFixerTest {}',
            '<?php class FIXER_BASIC_PSRAUTOLOADINGFIXERTEST {}',
        ];

        yield 'class with name not matching directory structure' => [
            '<?php class PsrAutoloadingFixerTest {}',
            '<?php class Aaaaa_Bbbbb_PsrAutoloadingFixerTest {}',
        ];

        yield 'configured directory (1 subdirectory)' => [
            '<?php class Basic_PsrAutoloadingFixerTest {}',
            '<?php class PsrAutoloadingFixerTest {}',
            __DIR__.'/..',
        ];

        yield 'configured directory (2 subdirectories)' => [
            '<?php class Fixer_Basic_PsrAutoloadingFixerTest {}',
            '<?php class PsrAutoloadingFixerTest {}',
            __DIR__.'/../..',
        ];

        yield 'configured directory (other directory)' => [
            '<?php namespace Basic; class Foobar {}',
            null,
            __DIR__.'/../../Test',
        ];

        yield 'multiple classy elements in file' => [
            '<?php interface Foo {} class Bar {}',
        ];

        yield 'namespace with wrong casing' => [
            '<?php namespace Fixer\\Basic; class PsrAutoloadingFixerTest {}',
            '<?php namespace Fixer\\BASIC; class PsrAutoloadingFixerTest {}',
            __DIR__.'/../..',
        ];

        yield 'multiple namespaces in file' => [
            '<?php namespace Foo\\Helpers; function helper() {}; namespace Foo\\Domain; class Feature {}',
        ];

        yield 'namespace and class with comments' => [
            '<?php namespace /* namespace here */ PhpCsFixer\\Tests\\Fixer\\Basic; class /* hi there */ PsrAutoloadingFixerTest /* hello */ {} /* class end */',
            '<?php namespace /* namespace here */ PhpCsFixer\\Tests\\Fixer\\Basic; class /* hi there */ Foo /* hello */ {} /* class end */',
        ];

        yield 'namespace partially matching directory structure' => [
            '<?php namespace Foo\\Bar\\Baz\\FIXER\\Basic; class PsrAutoloadingFixerTest {}',
        ];

        yield 'namespace partially matching directory structure with comment' => [
            '<?php namespace /* hi there */ Foo\\Bar\\Baz\\FIXER\\Basic; class /* hi there */ PsrAutoloadingFixerTest {}',
        ];

        yield 'namespace partially matching directory structure with configured directory' => [
            '<?php namespace Foo\\Bar\\Baz\\Fixer\\Basic; class PsrAutoloadingFixerTest {}',
            '<?php namespace Foo\\Bar\\Baz\\FIXER\\Basic; class PsrAutoloadingFixerTest {}',
            __DIR__.'/../..',
        ];

        yield 'namespace partially matching directory structure with comment and configured directory' => [
            '<?php namespace /* hi there */ Foo\\Bar\\Baz\\Fixer\\Basic; class /* hi there */ PsrAutoloadingFixerTest {}',
            '<?php namespace /* hi there */ Foo\\Bar\\Baz\\FIXER\\Basic; class /* hi there */ PsrAutoloadingFixerTest {}',
            __DIR__.'/../..',
        ];

        yield 'namespace not matching directory structure' => [
            '<?php namespace Foo\\Bar\\Baz; class PsrAutoloadingFixerTest {}',
        ];

        yield 'namespace not matching directory structure with configured directory' => [
            '<?php namespace Foo\\Bar\\Baz; class PsrAutoloadingFixerTest {}',
            null,
            __DIR__,
        ];
    }

    /**
     * @dataProvider provideFixCases
     * @dataProvider provideIgnoredCases
     * @dataProvider provideAnonymousClassCases
     */
    public function testFix(string $expected, ?string $input = null, ?\SplFileInfo $file = null, ?string $dir = null): void
    {
        if (null === $file) {
            $file = self::getTestFile(__FILE__);
        }
        if (null !== $dir) {
            $this->fixer->configure(['dir' => $dir]);
        }

        $this->doTest($expected, $input, $file);
    }

    public static function provideFixCases(): iterable
    {
        $prophet = new Prophet();
        $fileProphecy = $prophet->prophesize(\SplFileInfo::class);
        $fileProphecy->willBeConstructedWith(['']);
        $fileProphecy->getBasename('.php')->willReturn('Bar');
        $fileProphecy->getExtension()->willReturn('php');
        $fileProphecy->getRealPath()->willReturn(__DIR__.\DIRECTORY_SEPARATOR.'Psr'.\DIRECTORY_SEPARATOR.'Foo'.\DIRECTORY_SEPARATOR.'Bar.php');
        $file = $fileProphecy->reveal();

        yield [ // namespace with wrong casing
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

        yield [ // class with wrong casing (2 levels namespace)
            '<?php
class Psr_Foo_Bar {}
',
            '<?php
class Psr_fOo_bAr {}
',
            $file,
            __DIR__,
        ];

        yield [ // namespaced class with wrong casing
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

        yield [ // multiple classy elements in file
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
interface SomeInterfaceToBeUsedInTests {}
class blah {}
/* class foo */',
        ];

        yield [ // multiple namespaces in file
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

        yield [ // namespace and class with comments
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

        yield [ // namespace partially matching directory structure
            '<?php
namespace Foo\Bar\Baz\FIXER\Basic;
class PsrAutoloadingFixer {}
',
            null,
            self::getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
        ];

        yield [ // namespace partially matching directory structure with comment
            '<?php
namespace /* hi there */ Foo\Bar\Baz\FIXER\Basic;
class /* hi there */ PsrAutoloadingFixer {}
',
            null,
            self::getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
        ];

        yield [ // namespace not matching directory structure
            '<?php
namespace Foo\Bar\Baz;
class PsrAutoloadingFixer {}
',
            null,
            self::getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
        ];

        yield [ // namespace partially matching directory structure with configured directory
            '<?php
namespace Foo\Bar\Baz\Fixer\Basic;
class PsrAutoloadingFixer {}
',
            '<?php
namespace Foo\Bar\Baz\FIXER\Basic;
class PsrAutoloadingFixer {}
',
            self::getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
            __DIR__.'/../../../src/',
        ];

        yield [ // namespace partially matching directory structure with comment and configured directory
            '<?php
namespace /* hi there */ Foo\Bar\Baz\Fixer\Basic;
class /* hi there */ PsrAutoloadingFixer {}
',
            '<?php
namespace /* hi there */ Foo\Bar\Baz\FIXER\Basic;
class /* hi there */ PsrAutoloadingFixer {}
',
            self::getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
            __DIR__.'/../../../src/',
        ];

        yield [ // namespace not matching directory structure with configured directory
            '<?php
namespace Foo\Bar\Baz;
class PsrAutoloadingFixer {}
',
            null,
            self::getTestFile(__DIR__.'/../../../src/Fixer/Basic/PsrAutoloadingFixer.php'),
            __DIR__.'/../../../src/Fixer/Basic',
        ];

        yield [ // class with originally short name
            '<?php class PsrAutoloadingFixerTest {}',
            '<?php class Foo {}',
        ];

        yield [ // class with originally long name
            '<?php class PsrAutoloadingFixerTest {}',
            '<?php class PsrAutoloadingFixerTestFoo {}',
        ];
    }

    public static function provideIgnoredCases(): iterable
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

        return array_map(static fn ($case): array => [
            '<?php
namespace Aaa;
class Bar {}',
            null,
            self::getTestFile($case),
        ], $cases);
    }

    public static function provideAnonymousClassCases(): iterable
    {
        yield 'class with anonymous class' => [
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

        yield 'ignore anonymous class implementing interface' => [
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
new class implements Countable {};
',
        ];

        yield 'ignore anonymous class extending other class' => [
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
new class extends stdClass {};
',
        ];

        yield 'ignore multiple classy in file with anonymous class between them' => [
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
class ClassOne {};
new class extends stdClass {};
class ClassTwo {};
',
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'anonymous + annotation' => [
            '<?php
namespace PhpCsFixer\Tests\Fixer\Basic;
new
#[Foo]
class extends stdClass {};
',
        ];
    }

    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input, self::getTestFile(__FILE__));
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'enum with wrong casing' => [
            '<?php enum PsrAutoloadingFixerTest {}',
            '<?php enum psrautoloadingfixertest {}',
        ];
    }
}
