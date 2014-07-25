<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\PhpdocParamsAlignmentFixer;

class PhpdocParamsAlignmentFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'

     * @param EngineInterface $templating
     * @param string          $format
     * @param integer         $code       An HTTP response status code
     * @param Boolean         $debug
     * @param mixed           &$reference A parameter passed by reference

EOF;

        $input = <<<'EOF'

     * @param  EngineInterface $templating
     * @param string      $format
     * @param  integer  $code       An HTTP response status code
     * @param    Boolean      $debug
     * @param  mixed    &$reference     A parameter passed by reference

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixMultiLineDesc()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'

     * @param EngineInterface $templating
     * @param string          $format
     * @param integer         $code       An HTTP response status code
     *                                    See constants
     * @param Boolean         $debug
     * @param Boolean         $debug      See constants
     *                                    See constants
     * @param mixed           &$reference A parameter passed by reference

EOF;

        $input = <<<'EOF'

     * @param  EngineInterface $templating
     * @param string      $format
     * @param  integer  $code       An HTTP response status code
     *                              See constants
     * @param    Boolean      $debug
     * @param    Boolean      $debug See constants
     * See constants
     * @param  mixed    &$reference     A parameter passed by reference

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixMultiLineDescWithThrows()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'

     * @param EngineInterface $templating
     * @param string          $format
     * @param integer         $code       An HTTP response status code
     *                                    See constants
     * @param Boolean         $debug
     * @param Boolean         $debug      See constants
     *                                    See constants
     * @param mixed           &$reference A parameter passed by reference
     *
     * @return Foo description foo
     *
     * @throws Foo description foo
     *             description foo

EOF;

        $input = <<<'EOF'

     * @param  EngineInterface $templating
     * @param string      $format
     * @param  integer  $code       An HTTP response status code
     *                              See constants
     * @param    Boolean      $debug
     * @param    Boolean      $debug See constants
     * See constants
     * @param  mixed    &$reference     A parameter passed by reference
     *
     * @return Foo description foo
     *
     * @throws Foo             description foo
     * description foo

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithReturnAndThrows()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'

     * @param  EngineInterface $templating
     * @param  mixed           &$reference A parameter passed by reference
     * @throws Bar             description bar
     * @return Foo             description foo

EOF;

        $input = <<<'EOF'

     * @param EngineInterface       $templating
     * @param  mixed    &$reference     A parameter passed by reference
     * @throws   Bar description bar
     * @return  Foo     description foo

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    /**
     * References the issue #55 on github issue
     * https://github.com/fabpot/PHP-CS-Fixer/issues/55
     */
    public function testFixThreeParamsWithReturn()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'

     * @param  string $param1
     * @param  bool   $param2 lorem ipsum
     * @param  string $param3 lorem ipsum
     * @return int    lorem ipsum

EOF;

        $input = <<<'EOF'

     * @param   string $param1
     * @param bool   $param2 lorem ipsum
     * @param    string $param3 lorem ipsum
     * @return int lorem ipsum

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixOnlyReturn()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'

     * @return Foo description foo

EOF;

        $input = <<<'EOF'

     * @return   Foo             description foo

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testReturnWithDollarThis()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
     * @param  Foo   $foo
     * @return $this

EOF;

        $input = <<<'EOF'
     * @param Foo $foo
     * @return $this

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testCustomAnnotationsStayUntouched()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)

EOF;

        $input = <<<'EOF'
     * @return string
     *  @SuppressWarnings(PHPMD.UnusedLocalVariable)

EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
