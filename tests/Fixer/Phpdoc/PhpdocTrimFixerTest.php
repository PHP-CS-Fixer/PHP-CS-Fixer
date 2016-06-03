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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 */
final class PhpdocTrimFixerTest extends AbstractFixerTestCase
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

        $this->doTest($expected);
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

        $this->doTest($expected, $input);
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
 * @author Graham Campbell <graham@alt-three.com>
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
 * @author Graham Campbell <graham@alt-three.com>
 *
    *
  *
   */
class Bar {}

EOF;

        $this->doTest($expected, $input);
    }

    public function testEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->doTest($expected);
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

        $this->doTest($expected, $input);
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

        $this->doTest($expected, $input);
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

        $this->doTest($expected, $input);
    }
}
