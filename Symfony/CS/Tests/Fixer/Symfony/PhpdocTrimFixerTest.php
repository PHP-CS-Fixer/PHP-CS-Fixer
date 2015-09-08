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
final class PhpdocTrimFixerTest extends AbstractFixerTestBase
{
    public function testFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     *
     * @return void
     */

EOF;

        $this->makeTest($expected);
    }

    public function testFixMore()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there!
     * @internal
     *@param string $foo
     *@throws Exception
     *
    *
     *
     *  @return bool
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     *
  *
     * Hello there!
     * @internal
     *@param string $foo
     *@throws Exception
     *
    *
     *
     *  @return bool
     *
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testClassDocBlock()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

  /**
 * This is a class that does classy things.
 *
 * @internal
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
   */
class Bar {}

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

  /**
   *
 *
 * This is a class that does classy things.
 *
 * @internal
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 *
    *
  *
   */
class Bar {}

EOF;

        $this->makeTest($expected, $input);
    }

    public function testEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->makeTest($expected);
    }

    public function testEmptyLargerEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     *
     *
     *
     *
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testSuperSimpleDocBlockStart()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Test.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     *
     * Test.
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testSuperSimpleDocBlockEnd()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Test.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Test.
     *
     */

EOF;

        $this->makeTest($expected, $input);
    }
}
