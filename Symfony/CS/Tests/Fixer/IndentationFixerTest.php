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

use Symfony\CS\Fixer\IndentationFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class IndentationFixerTest extends \PHPUnit_Framework_TestCase
{
    private function makeTest($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    /**
     * @dataProvider provideIndentationOnly
     */
    public function testIndentationOnly($expected, $input)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideIndentationAndAlignment
     */
    public function testIndentationAndAlignment($expected, $input)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideTabInString
     */
    public function testTabInString($expected, $input)
    {
        $this->makeTest($expected, $input);
    }

    public function provideIndentationOnly()
    {
        $cases = array();

        $cases[] = array('
<?php
        echo ALPHA;', '
<?php
		echo ALPHA;');

        $cases[] = array('
<?php
        echo BRAVO;', '
<?php
		echo BRAVO;');

        $cases[] = array('
<?php
        echo CHARLIE;', '
<?php
 		echo CHARLIE;');

        $cases[] = array('
<?php
        echo DELTA;', '
<?php
  		echo DELTA;');

        $cases[] = array('
<?php
        echo ECHO;', '
<?php
   		echo ECHO;');

        $cases[] = array('
<?php
        echo FOXTROT;', '
<?php
	 	echo FOXTROT;');

        $cases[] = array('
<?php
        echo GOLF;', '
<?php
	  	echo GOLF;');

        $cases[] = array('
<?php
        echo HOTEL;', '
<?php
	   	echo HOTEL;');

        $cases[] = array('
<?php
        echo INDIA;', '
<?php
	    echo INDIA;');

        $cases[] = array('
<?php
        echo JULIET;', '
<?php
 	   	echo JULIET;');

        $cases[] = array('
<?php
        echo KILO;', '
<?php
  	  	echo KILO;');

        $cases[] = array('
<?php
        echo MIKE;', '
<?php
   	 	echo MIKE;');

        $cases[] = array('
<?php
        echo NOVEMBER;', '
<?php
    	echo NOVEMBER;');

        return $cases;
    }

    public function provideIndentationAndAlignment()
    {
        $cases = array();

        $cases[] = array('
<?php
         echo OSCAR;', '
<?php
	 	 echo OSCAR;');

        $cases[] = array('
<?php
          echo PAPA;', '
<?php
	 	  echo PAPA;');

        $cases[] = array('
<?php
           echo QUEBEC;', '
<?php
	 	   echo QUEBEC;');

        return $cases;
    }

    public function provideTabInString()
    {
        $cases = array();

        $cases[] = array('
<?php
$x = "a: 	";', '
<?php
$x = "a: 	";');

$cases[] = array('
<?php
$x = "
	Like
	a
	dog";', '
<?php
$x = "
	Like
	a
	dog";');

        return $cases;
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
