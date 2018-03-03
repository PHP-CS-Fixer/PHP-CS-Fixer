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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer
 */
final class PhpdocTrimFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
<<<'EOF'
                <?php
    /**
     * @param EngineInterface $templating
     *
     * @return void
     */

EOF
            ),
            array(
                '<?php

/**
 * @return int количество деактивированных
 */
function deactivateCompleted()
{
    return 0;
}',
            ),
            array(
                mb_convert_encoding('
<?php
/**
 * Test à
 */
function foo(){}
', 'Windows-1252', 'UTF-8'),
            ),
        );
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

    public function testWithLinesWithoutAsterisk()
    {
        $expected = <<<'EOF'
<?php

/**
 * Foo
      Baz
 */
class Foo
{
}

EOF;

        $this->doTest($expected);
    }
}
