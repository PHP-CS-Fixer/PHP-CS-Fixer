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

use Symfony\CS\Fixer\MethodArgumentsFixer as Fixer;

/**
 * @author Ricard Clau <ricard.clau@gmail.com>
 */
class MethodArgumentsFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFixProvider
     */
    public function testFix($include, $includeFixed)
    {
        $fixer = new Fixer();

        $this->assertEquals($includeFixed, $fixer->fix($this->getFileMock(), $include));
        $this->assertEquals($includeFixed, $fixer->fix($this->getFileMock(), $includeFixed));
    }

    public function testFixProvider()
    {
        return array(
            array('public function foo($a,$b,$c)', 'public function foo($a, $b, $c)'),
            array('public function foo    ($a,$b,$c)', 'public function foo($a, $b, $c)'),
            array('function foo($a  ,$b , $c)', 'function foo($a, $b, $c)'),
            array('function foo(  $a  ,  $b , $c )', 'function foo($a, $b, $c)'),
            array("function foo(\t\$a,\t\$b,\$c)", 'function foo($a, $b, $c)'),
            array('function foo( Typehint $a,Namespaced\Typehint $b,array $c,\Full\Namespaced\Typehint $d)', 'function foo(Typehint $a, Namespaced\Typehint $b, array $c, \Full\Namespaced\Typehint $d)'),
            array('function foo($a,$b=12,$c="aaa",$d=array(1,2,3))', 'function foo($a, $b = 12, $c = "aaa", $d = array(1, 2, 3))'),
            array('function foo($a=array(),$b=array(1=>"a",2=>array(2,3),3),$c=array("a"=>"b"))', 'function foo($a = array(), $b = array(1 => "a", 2 => array(2, 3), 3), $c = array("a" => "b"))'),
        );
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
