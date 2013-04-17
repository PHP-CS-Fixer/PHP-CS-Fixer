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

    /**
     * @dataProvider testFixControlsWithParenthesesAndSuffixBraceProvider
     */
    public function testFixControlsWithParenthesesAndSuffixBrace($if, $ifFixed)
    {
        $fixer = new Fixer();

        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $if));
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $ifFixed));
    }

    public function testFixControlClosingParenthesesKeepsIndentation()
    {
        $fixer = new Fixer();

        $if = 'if(true === true
            && true === true
        )    {';

        $ifFixed = 'if (true === true
            && true === true
        ) {';

        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $if));
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $ifFixed));
    }

    public function testFixControlsWithParenthesesAndSuffixBraceProvider()
    {
        return array(
            array('if($test){', 'if ($test) {'),
            array('if( $test ){', 'if ($test) {'),
            array('if  (   $test ){', 'if ($test) {'),
            array('if  (($test1 || $test2) && $test3){', 'if (($test1 || $test2) && $test3) {'),
            array('if(($test1 || $test2) && $test3){', 'if (($test1 || $test2) && $test3) {'),
            array('if ($this->tesT ($test)) {', 'if ($this->tesT ($test)) {'),
            array('if ($this->testtesT ($test)) {', 'if ($this->testtesT ($test)) {'),
        );
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

    public function testFixControlsWithPrefixBraceAndParenthesesAndSuffixBraceInLambdas()
    {
        $fixer = new Fixer();

        $use = ')use($test){';
        $useFixed = ') use ($test) {';
        $this->assertEquals($useFixed, $fixer->fix($this->getFileMock(), $use));
        $this->assertEquals($useFixed, $fixer->fix($this->getFileMock(), $useFixed));
    }

    /**
     * @dataProvider testFixCastsProvider
     */
    public function testFixCasts($cast, $castFixed)
    {
        $fixer = new Fixer();

        $this->assertEquals($castFixed, $fixer->fix($this->getFileMock(), $cast));
        $this->assertEquals($castFixed, $fixer->fix($this->getFileMock(), $castFixed));
    }

    public function testFixCastsProvider()
    {
        return array(
            array('( int)$foo', '(int) $foo'),
            array('( string )( int )$foo', '(string) (int) $foo'),
            array('(string)(int)$foo', '(string) (int) $foo'),
            array('( string   )    (   int )$foo', '(string) (int) $foo'),
            array('( string )   $foo', '(string) $foo'),
            array('(float )Foo::bar()', '(float) Foo::bar()'),
            array('Foo::baz((float )Foo::bar())', 'Foo::baz((float) Foo::bar())'),
            array('$query["params"] = (array)$query["params"]', '$query["params"] = (array) $query["params"]'),
            array("(int)\n    *", "(int)\n    *"),
        );
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
