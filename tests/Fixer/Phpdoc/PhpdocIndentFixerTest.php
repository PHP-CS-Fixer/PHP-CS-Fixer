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
     * @dataProvider provideFixIndentCases
     */
    public function testFixIndent(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixIndentCases(): iterable
    {
        yield ['<?php /** @var Foo $foo */ ?>'];

        yield ['<?php /** foo */'];

        yield [
            '<?php
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
}',
            '<?php
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
}',
        ];

        yield [
            '<?php
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
}',
            '<?php
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
}',
        ];

        yield [
            '<?php
/**
 * Final class should also not be indented
 */
final class DocBlocks
{
    /**
     * Test with var keyword
     */
    var $oldStyle = false;
}',
            '<?php
/**
 * Final class should also not be indented
 */
final class DocBlocks
{
/**
 * Test with var keyword
 */
    var $oldStyle = false;
}',
        ];

        yield [
            '<?php
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
    }',
            '<?php
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
    }',
        ];

        yield [
            '<?php
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
    }',
            '<?php
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
    }',
        ];

        yield [
            '<?php
    $user = $event->getForm()->getData();  /** @var User $user */
    echo "Success";',
        ];

        yield [
            '<?php
    $user = $event->getForm()->getData();/** @var User $user */
    echo "Success";',
        ];

        yield [
            "<?php
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
}",
            "<?php
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
}",
        ];

        yield [
            '<?php
/**
 * Used to write a value to a session key.
 *
 * ...
 */
function write($name) {}
',
            "<?php
\t/**
 * Used to write a value to a session key.
 *
 * ...
 */
function write(\$name) {}
",
        ];

        yield [
            '<?php
    class Foo
    {
        public function bar()
        {
            /**
             * baz
             */
        }
    }',
        ];

        yield [
            '<?php
/**
 * docs
 */

// comment
$foo = $bar;
',
        ];

        yield [
            '<?php
function foo()
{
    $foo->bar(/** oops */$baz);
    $foo->bar($a,/** oops */$baz);
}',
        ];

        yield [
            '<?php

/**
 * Foo
       Bar
 */
class Foo
{
}',
        ];

        yield [
            '<?php
class Application
{
}/**
 */
class Dispatcher
{
}
',
        ];
    }
}
