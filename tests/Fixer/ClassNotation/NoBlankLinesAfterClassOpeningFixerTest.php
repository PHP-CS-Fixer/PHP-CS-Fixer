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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer>
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer
 *
 * @author Ceeram <ceeram@cakephp.org>
 */
final class NoBlankLinesAfterClassOpeningFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
class Good
{
    public function firstMethod()
    {
        //code here
    }
}',
            '<?php
class Good
{

    public function firstMethod()
    {
        //code here
    }
}',
        ];

        yield [
            '<?php
class Good
{
    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod()
    {
        //code here
    }
}',
            '<?php
class Good
{

    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod()
    {
        //code here
    }
}',
        ];

        yield [
            '<?php
interface Good
{
    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod();
}',
            '<?php
interface Good
{

    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod();
}',
        ];

        // check if some fancy whitespaces aren't modified
        yield [
            '<?php
class Good
{public



    function firstMethod()
    {
        //code here
    }
}',
        ];

        // check if line with spaces is removed when next token is indented
        yield [
            '<?php
class Foo
{
    function bar() {}
}
',
            '<?php
class Foo
{
    '.'
    function bar() {}
}
',
        ];

        // check if line with spaces is removed when next token is not indented
        yield [
            '<?php
class Foo
{
function bar() {}
}
',
            '<?php
class Foo
{
    '.'
function bar() {}
}
',
        ];

        yield [
            '<?php
trait Good
{
    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod() {}
}',
            '<?php
trait Good
{

    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod() {}
}',
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
            "<?php\nclass Foo\n{\n\n    public function bar() {}\n}",
        ];

        yield [
            "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
            "<?php\nclass Foo\n{\r\n\r\n    public function bar() {}\n}",
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
enum Good
{
    public function firstMethod()
    {}
}',
            '<?php
enum Good
{

    public function firstMethod()
    {}
}',
        ];
    }
}
