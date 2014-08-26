<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Fixer\PSR2\TrailingSpacesFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TrailingSpacesFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
$a = 1;',
                '<?php
$a = 1;   ',
            ),
            array(
                '<?php
$a = 1  ;',
                '<?php
$a = 1  ;   ',
            ),
            array(
                '<?php
$b = 1;',
                '<?php
$b = 1;		',
            ),
            array(
                '<?php
$b = 1;',
                '<?php
$b = 1;   	   ',
            ),
            array(
                '<?php
                
	
$b = 1;',
                '<?php
                
	
$b = 1;',
            ),
        );
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
