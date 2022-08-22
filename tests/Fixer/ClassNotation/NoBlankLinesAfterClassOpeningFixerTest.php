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
 * @author Ceeram <ceeram@cakephp.org>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer
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
     * @dataProvider provideTraitsCases
     */
    public function testFixTraits(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        $cases = [];

        $cases[] = [
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

        $cases[] = [
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

        $cases[] = [
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
        $cases[] = [
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
        $cases[] = [
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
        $cases[] = [
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

        return $cases;
    }

    public function provideTraitsCases(): array
    {
        $cases = [];

        $cases[] = [
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

        return $cases;
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): array
    {
        return [
            [
                "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
                "<?php\nclass Foo\n{\n\n    public function bar() {}\n}",
            ],
            [
                "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
                "<?php\nclass Foo\n{\r\n\r\n    public function bar() {}\n}",
            ],
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

    public function provideFix81Cases(): iterable
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
