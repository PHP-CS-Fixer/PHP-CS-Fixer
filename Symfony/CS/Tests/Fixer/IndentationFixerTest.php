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

use Symfony\CS\Fixer\IndentationFixer;

class IndentationFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new IndentationFixer();
        $file = $this->getTestFile();

        // Indentation only
        $this->assertEquals('        ALPHA', $fixer->fix($file, "\t\tALPHA"));
        $this->assertEquals('        BRAVO', $fixer->fix($file, "\t\tBRAVO"));
        $this->assertEquals('        CHARLIE', $fixer->fix($file, " \t\tCHARLIE"));
        $this->assertEquals('        DELTA', $fixer->fix($file, "  \t\tDELTA"));
        $this->assertEquals('        ECHO', $fixer->fix($file, "   \t\tECHO"));
        $this->assertEquals('        FOXTROT', $fixer->fix($file, "\t \tFOXTROT"));
        $this->assertEquals('        GOLF', $fixer->fix($file, "\t  \tGOLF"));
        $this->assertEquals('        HOTEL', $fixer->fix($file, "\t   \tHOTEL"));
        $this->assertEquals('        INDIA', $fixer->fix($file, "\t    INDIA"));
        $this->assertEquals('        JULIET', $fixer->fix($file, " \t   \tJULIET"));
        $this->assertEquals('        KILO', $fixer->fix($file, "  \t  \tKILO"));
        $this->assertEquals('        MIKE', $fixer->fix($file, "   \t \tMIKE"));
        $this->assertEquals('        NOVEMBER', $fixer->fix($file, "    \tNOVEMBER"));
        // Indentation and alignment
        $this->assertEquals('         OSCAR', $fixer->fix($file, "\t \t OSCAR"));
        $this->assertEquals('          PAPA', $fixer->fix($file, "\t \t  PAPA"));
        $this->assertEquals('           QUEBEC', $fixer->fix($file, "\t \t   QUEBEC"));
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
