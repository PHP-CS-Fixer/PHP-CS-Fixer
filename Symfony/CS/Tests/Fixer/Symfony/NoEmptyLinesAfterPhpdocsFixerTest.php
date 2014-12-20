<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class NoEmptyLinesAfterPhpdocsFixerTest extends AbstractFixerTestBase
{
    public function testSimpleExampleIsNotChanged()
    {
        $input = <<<'EOF'
<?php

/**
 * This is the bar class.
 */
class Bar
{
    /**
     * @return void
     */
    public function foo()
    {
        //
    }
}

EOF;

        $this->makeTest($input);
    }

    public function testComplexExampleIsNotChanged()
    {
        $input = <<<'EOF'
<?php
/**
 * This is the hello function.
 * Yeh, this layout should be allowed.
 * We're fixing lines following a docblock.
 */
function hello($foo) {}
/**
 * This is the bar class.
 */
final class Bar
{
    /**
     * @return void
     */
    public static function foo()
    {
        //
    }

    /**
     * @return void
     */
    static private function bar() {}

    /*
     * This should not be moved.
     */
    final protected
    // mixin' it up a bit
    function baz() {
    }


    /*
     * This should not be moved.
     */

    public function cool() {}
}

EOF;

        $this->makeTest($input);
    }

    public function testCommentsAreNotChanged()
    {
        $input = <<<'EOF'
<?php

/*
 * This file is part of xyz.
 *
 * License etc...
 */

namespace Foo\Bar;

EOF;

        $this->makeTest($input);
    }

    public function testFixesSimpleClass()
    {
        $expected = <<<'EOF'
<?php

/**
 * This is the bar class.
 */
class Bar {}

EOF;

        $input = <<<'EOF'
<?php

/**
 * This is the bar class.
 */


class Bar {}

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixesIndentedClass()
    {
        $expected = <<<'EOF'
<?php

    /**
     *
     */
    class Foo {
        return;
    }

EOF;

        $input = <<<'EOF'
<?php

    /**
     *
     */

    class Foo {
        return;
    }

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixesOthers()
    {
        $expected = <<<'EOF'
<?php

    /**
     * Constant!
     */
    const test = 'constant';

    /**
     * Foo!
     */
    $foo = 123;

EOF;

        $input = <<<'EOF'
<?php

    /**
     * Constant!
     */


    const test = 'constant';

    /**
     * Foo!
     */

    $foo = 123;

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixesWindowsStyle()
    {
        $expected = "<?php\r\n    /**     * Constant!     */\n    \$foo = 123;";

        $input = "<?php\r\n    /**     * Constant!     */\r\n\r\n\r\n    \$foo = 123;";

        $this->makeTest($expected, $input);
    }
}
