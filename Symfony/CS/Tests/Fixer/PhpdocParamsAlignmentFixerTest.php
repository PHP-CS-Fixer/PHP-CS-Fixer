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
        $file = new \SplFileInfo(__FILE__);

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

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testFixWithReturnAndThrows()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = new \SplFileInfo(__FILE__);

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

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    /**
     * References the issue #55 on github issue
     * https://github.com/fabpot/PHP-CS-Fixer/issues/55
     */
    public function testFixThreeParamsWithReturn()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = new \SplFileInfo(__FILE__);

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

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testFixOnlyReturn()
    {
        $fixer = new PhpdocParamsAlignmentFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'

     * @return Foo description foo

EOF;

        $input = <<<'EOF'

     * @return   Foo             description foo

EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }
}
