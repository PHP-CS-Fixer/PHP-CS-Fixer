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

        $this->assertEquals($includeFixed, $fixer->fix($this->getTestFile(), $include));
        $this->assertEquals($includeFixed, $fixer->fix($this->getTestFile(), $includeFixed));
    }

    public function testFixProvider()
    {
        return array(
            array("include   'foo.php'", "include 'foo.php'"),
            array('include   "foo.php"', "include 'foo.php'"),
            array('include (  "Buzz/foo-Bar.php" )', "include 'Buzz/foo-Bar.php'"),
            array("include('foo.php')", "include 'foo.php'"),
            array("include_once( 'foo.php' )", "include_once 'foo.php'"),
            array('require("foo.php")', "require 'foo.php'"),
            array("return require_once  __DIR__.'foo.php'", "return require_once __DIR__.'foo.php'"),
            array("\$foo = require_once  __DIR__.('foo.php')", "\$foo = require_once __DIR__.('foo.php')"),
            array("    require_once  (__DIR__.('foo.php'))", "    require_once (__DIR__.('foo.php'))"),
            array("require_once (dirname(__FILE__).'foo.php')", "require_once (dirname(__FILE__).'foo.php')"),
            array('$includeVar', '$includeVar'),
            array("ClassCollectionLoader::load(include(\$this->getCacheDir().'classes.map'), \$this->getCacheDir(), \$name, \$this->debug, false, \$extension)", "ClassCollectionLoader::load(include(\$this->getCacheDir().'classes.map'), \$this->getCacheDir(), \$name, \$this->debug, false, \$extension)"),
            array("require_once '\".__DIR__.\"/../bootstrap.php'", "require_once '\".__DIR__.\"/../bootstrap.php'"),
            array("//  require   foo", "//  require   foo"),
            array("* require   foo", "* require   foo"),
            array('exit(\'POST must include "file"\');', 'exit(\'POST must include "file"\');'),
            array('include_once("foo/".CONSTANT."/bar.php")', 'include_once "foo/".CONSTANT."/bar.php"'),
            array('include_once("foo/".CONSTANT."/bar.php"); include_once("foo/".CONSTANT."/bar.php");', 'include_once "foo/".CONSTANT."/bar.php"; include_once "foo/".CONSTANT."/bar.php";'),
            array('include_once("foo/".CONSTANT."/bar.php"); $foo = "bar";', 'include_once "foo/".CONSTANT."/bar.php"; $foo = "bar";'),
            array('include_once("foo/".CONSTANT."/bar.php"); foo();', 'include_once "foo/".CONSTANT."/bar.php"; foo();'),
            array('include_once("foo/" . CONSTANT . "/bar.php")', 'include_once "foo/" . CONSTANT . "/bar.php"'),
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
