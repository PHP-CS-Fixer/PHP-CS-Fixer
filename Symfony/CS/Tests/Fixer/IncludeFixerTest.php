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

use Symfony\CS\Fixer\IncludeFixer as Fixer;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class IncludeFixerTest extends \PHPUnit_Framework_TestCase
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
            array("include   'foo.php'", "include 'foo.php'"),
            array("include('foo.php')", "include 'foo.php'"),
            array("include_once( 'foo1.php' )", "include_once 'foo1.php'"),
            array('require("foo.php")', 'require "foo.php"'),
            array("require_once  __DIR__ . 'sasa.php'", "require_once __DIR__ . 'sasa.php'"),
            array('$includeVar', '$includeVar'),
        );
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
