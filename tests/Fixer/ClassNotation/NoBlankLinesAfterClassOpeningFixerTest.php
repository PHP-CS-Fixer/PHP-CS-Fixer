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

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                class Good
                {
                    public function firstMethod()
                    {
                        //code here
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Good
                {

                    public function firstMethod()
                    {
                        //code here
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Good
                {
                    /**
                     * Also no blank line before DocBlock
                     */
                    public function firstMethod()
                    {
                        //code here
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Good
                {

                    /**
                     * Also no blank line before DocBlock
                     */
                    public function firstMethod()
                    {
                        //code here
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                interface Good
                {
                    /**
                     * Also no blank line before DocBlock
                     */
                    public function firstMethod();
                }
                EOD,
            <<<'EOD'
                <?php
                interface Good
                {

                    /**
                     * Also no blank line before DocBlock
                     */
                    public function firstMethod();
                }
                EOD,
        ];

        // check if some fancy whitespaces aren't modified
        yield [
            <<<'EOD'
                <?php
                class Good
                {public



                    function firstMethod()
                    {
                        //code here
                    }
                }
                EOD,
        ];

        // check if line with spaces is removed when next token is indented
        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    function bar() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                EOD."\n    ".<<<'EOD'

                    function bar() {}
                }

                EOD,
        ];

        // check if line with spaces is removed when next token is not indented
        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                function bar() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                EOD."\n    ".<<<'EOD'

                function bar() {}
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                trait Good
                {
                    /**
                     * Also no blank line before DocBlock
                     */
                    public function firstMethod() {}
                }
                EOD,
            <<<'EOD'
                <?php
                trait Good
                {

                    /**
                     * Also no blank line before DocBlock
                     */
                    public function firstMethod() {}
                }
                EOD,
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

    public static function provideFix81Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                enum Good
                {
                    public function firstMethod()
                    {}
                }
                EOD,
            <<<'EOD'
                <?php
                enum Good
                {

                    public function firstMethod()
                    {}
                }
                EOD,
        ];
    }
}
