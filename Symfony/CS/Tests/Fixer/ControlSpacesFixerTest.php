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

use Symfony\CS\Fixer\ControlSpacesFixer as Fixer;

class ControlSpacesFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFixControlsWithSuffixBrace()
    {
        $fixer = new Fixer();

        $try = 'try{';
        $tryFixed = 'try {';
        $this->assertEquals($tryFixed, $fixer->fix($this->getFileMock(), $try));
        $this->assertEquals($tryFixed, $fixer->fix($this->getFileMock(), $tryFixed));
    }

    public function testFixControlsWithPrefixBraceAndParentheses()
    {
        $fixer = new Fixer();

        $while = 'do { ... }while($test);';
        $whileFixed = 'do { ... } while ($test);';
        $this->assertEquals($whileFixed, $fixer->fix($this->getFileMock(), $while));
        $this->assertEquals($whileFixed, $fixer->fix($this->getFileMock(), $whileFixed));
    }

    public function testFixControlsWithParenthesesAndSuffixBrace()
    {
        $fixer = new Fixer();

        $if = 'if($test){';
        $ifFixed = 'if ($test) {';
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $if));
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $ifFixed));
    }

    public function testFixControlsWithPrefixBraceAndSuffixBrace()
    {
        $fixer = new Fixer();

        $else = '}else{';
        $elseFixed = '} else {';
        $this->assertEquals($elseFixed, $fixer->fix($this->getFileMock(), $else));
        $this->assertEquals($elseFixed, $fixer->fix($this->getFileMock(), $elseFixed));
    }

    public function testFixControlsWithPrefixBraceAndParenthesesAndSuffixBrace()
    {
        $fixer = new Fixer();

        $elseif = '}elseif($test){';
        $elseifFixed = '} elseif ($test) {';
        $this->assertEquals($elseifFixed, $fixer->fix($this->getFileMock(), $elseif));
        $this->assertEquals($elseifFixed, $fixer->fix($this->getFileMock(), $elseifFixed));
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
