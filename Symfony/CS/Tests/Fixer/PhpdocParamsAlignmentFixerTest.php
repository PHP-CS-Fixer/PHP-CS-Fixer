<?php

/*
 * This file is part of the Symfony CS utility.
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

EOF;

        $input = <<<'EOF'

     * @param  EngineInterface $templating
     * @param string      $format
     * @param  integer  $code       An HTTP response status code
     * @param    Boolean      $debug

EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }
}
