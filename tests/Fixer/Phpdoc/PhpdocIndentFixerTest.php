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

    public static function provideFixIndentCases(): array
    {
        $cases = [];

        $cases[] = ['<?php /** @var Foo $foo */ ?>'];

        $cases[] = ['<?php /** foo */'];

        $cases[] = [
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

        $cases[] = [
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

        $cases[] = [
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

        $cases[] = [
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

        $cases[] = [
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

        $cases[] = [
            '<?php
    $user = $event->getForm()->getData();  /** @var User $user */
    echo "Success";',
        ];

        $cases[] = [
            '<?php
    $user = $event->getForm()->getData();/** @var User $user */
    echo "Success";',
        ];

        $cases[] = [
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

        $cases[] = [
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

        $cases[] = [
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

        $cases[] = [
            '<?php
/**
 * docs
 */

// comment
$foo = $bar;
',
        ];

        $cases[] = [
            '<?php
function foo()
{
    $foo->bar(/** oops */$baz);
    $foo->bar($a,/** oops */$baz);
}',
        ];

        $cases[] = [
            '<?php

/**
 * Foo
       Bar
 */
class Foo
{
}',
        ];

        $cases[] = [
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

        return $cases;
    }
}
