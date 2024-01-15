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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Ceeram <ceeram@cakephp.org>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer
 */
final class PhpdocIndentFixerTest extends AbstractFixerTestCase
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
        yield ['<?php /** @var Foo $foo */ ?>'];

        yield ['<?php /** foo */'];

        yield [
            <<<'EOD'
                <?php
                /**
                 * Do not indent
                 */

                /**
                 * Do not indent this
                 */
                class DocBlocks
                {
                    /**
                     *Test that attribute docblocks are indented
                     */
                    protected $indent = false;

                    /**
                     * Test that method docblocks are indented.
                     */
                    public function test() {}
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * Do not indent
                 */

                /**
                 * Do not indent this
                 */
                class DocBlocks
                {
                /**
                 *Test that attribute docblocks are indented
                 */
                    protected $indent = false;

                /**
                 * Test that method docblocks are indented.
                 */
                    public function test() {}
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class DocBlocks
                {
                    /**
                     * Test constants
                     */
                    const INDENT = 1;

                    /**
                     * Test with var keyword
                     */
                    var $oldStyle = false;

                    /**
                     * Test static
                     */
                    public static function test1() {}

                    /**
                     * Test static first.
                     */
                    static public function test2() {}

                    /**
                     * Test final first.
                     */
                    final public function test3() {}

                    /**
                     * Test no keywords
                     */
                    function test4() {}
                }
                EOD,
            <<<'EOD'
                <?php
                class DocBlocks
                {
                /**
                 * Test constants
                 */
                    const INDENT = 1;

                /**
                 * Test with var keyword
                 */
                    var $oldStyle = false;

                /**
                 * Test static
                 */
                    public static function test1() {}

                /**
                 * Test static first.
                 */
                    static public function test2() {}

                /**
                 * Test final first.
                 */
                    final public function test3() {}

                /**
                 * Test no keywords
                 */
                    function test4() {}
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * Final class should also not be indented
                 */
                final class DocBlocks
                {
                    /**
                     * Test with var keyword
                     */
                    var $oldStyle = false;
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * Final class should also not be indented
                 */
                final class DocBlocks
                {
                /**
                 * Test with var keyword
                 */
                    var $oldStyle = false;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (1) {
                        class Foo {
                            /**
                             * Foo
                             */
                            function foo() {}

                            /**
                             * Bar
                             */
                            function bar() {}
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (1) {
                        class Foo {
                /**
                 * Foo
                 */
                            function foo() {}

                                        /**
                                         * Bar
                                         */
                            function bar() {}
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * Variable
                 */
                $variable = true;

                /**
                 * Partial docblock fix
                 */
                $partialFix = true;

                    /**
                     * Other partial docblock fix
                     */
                    $otherPartial = true;

                    /** Single line */
                    $single = true;

                    /**
                     * Function
                     */
                    function something()
                    {
                        /**
                         * Inside functions
                         */
                        return;
                    }

                    /**
                     * function call
                     */
                    something();

                    /**
                     * Control structure
                     * @var \Sqlite3 $sqlite
                     */
                    foreach($connections as $sqlite) {
                        $sqlite->open();
                    }
                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Variable
                     */
                $variable = true;

                /**
                 * Partial docblock fix
                 */
                $partialFix = true;

                    /**
                    * Other partial docblock fix
                    */
                    $otherPartial = true;

                /** Single line */
                    $single = true;

                /**
                 * Function
                 */
                    function something()
                    {
                /**
                 * Inside functions
                 */
                        return;
                    }

                /**
                 * function call
                 */
                    something();

                /**
                 * Control structure
                 * @var \Sqlite3 $sqlite
                 */
                    foreach($connections as $sqlite) {
                        $sqlite->open();
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $user = $event->getForm()->getData();  /** @var User $user */
                    echo "Success";
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $user = $event->getForm()->getData();/** @var User $user */
                    echo "Success";
                EOD,
        ];

        yield [
            <<<EOD
                <?php
                class DocBlocks
                {
                \t/**
                \t *Test that attribute docblocks are indented
                \t */
                \tprotected \$indent = false;

                \t/**
                \t * Test that method docblocks are indented.
                \t */
                \tpublic function test() {}
                }
                EOD,
            <<<EOD
                <?php
                class DocBlocks
                {
                /**
                 *Test that attribute docblocks are indented
                 */
                \tprotected \$indent = false;

                /**
                 * Test that method docblocks are indented.
                 */
                \tpublic function test() {}
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * Used to write a value to a session key.
                 *
                 * ...
                 */
                function write($name) {}

                EOD,
            <<<EOD
                <?php
                \t/**
                 * Used to write a value to a session key.
                 *
                 * ...
                 */
                function write(\$name) {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function bar()
                        {
                            /**
                             * baz
                             */
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * docs
                 */

                // comment
                $foo = $bar;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $foo->bar(/** oops */$baz);
                    $foo->bar($a,/** oops */$baz);
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                /**
                 * Foo
                       Bar
                 */
                class Foo
                {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Application
                {
                }/**
                 */
                class Dispatcher
                {
                }

                EOD,
        ];
    }
}
