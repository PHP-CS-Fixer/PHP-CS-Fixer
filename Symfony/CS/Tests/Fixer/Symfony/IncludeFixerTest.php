<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class IncludeFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider testFixProvider
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function testFixProvider()
    {
        return array(
            array(
                "<?php include 'foo.php';",
                "<?php include   'foo.php';",
            ),
            array(
                "<?php include 'foo.php';",
                "<?php include   'foo.php'  ;",
            ),
            array(
                "<?php include 'foo.php';",
                "<?php include   ('foo.php')  ;",
            ),
            array(
                '<?php include "Buzz/foo-Bar.php";',
                '<?php include (  "Buzz/foo-Bar.php" );',
            ),
            array(
                '<?php include "$buzz/foo-Bar.php";',
                '<?php include (  "$buzz/foo-Bar.php" );',
            ),
            array(
                '<?php include "{$buzz}/foo-Bar.php";',
                '<?php include (  "{$buzz}/foo-Bar.php" );',
            ),
            array(
                "<?php include 'foo.php';",
                "<?php include('foo.php');",
            ),
            array(
                "<?php include_once 'foo.php';",
                "<?php include_once( 'foo.php' );",
            ),
            array(
                '<?php require $foo ? "foo.php" : "bar.php";',
                '<?php require($foo ? "foo.php" : "bar.php");',
            ),
            array(
                '<?php require $foo  ?  "foo.php"  :  "bar.php";',
                '<?php require($foo  ?  "foo.php"  :  "bar.php");',
            ),
            array(
                "<?php return require_once __DIR__.'foo.php';",
                "<?php return require_once  __DIR__.'foo.php';",
            ),
            array(
                "<?php \$foo = require_once __DIR__.('foo.php');",
                "<?php \$foo = require_once  __DIR__.('foo.php');",
            ),
            array(
                "<?php     require_once __DIR__.('foo.php');",
                "<?php     require_once  (__DIR__.('foo.php'));",
            ),
            array(
                "<?php     require_once __DIR__ . ('foo.php');",
                "<?php     require_once  (__DIR__ . ('foo.php'));",
            ),
            array(
                "<?php require_once dirname(__FILE__).'foo.php';",
                "<?php require_once (dirname(__FILE__).'foo.php');",
            ),
            array(
                '<?php ClassCollectionLoader::load(include($this->getCacheDir().\'classes.map\'), $this->getCacheDir(), $name, $this->debug, false, $extension);',
            ),
            array(
                "<?php require_once '\".__DIR__.\"/../bootstrap.php';",
            ),
            array(
                "// require foo",
            ),
            array(
                "<?php // require foo",
            ),
            array(
                "* require foo",
            ),
            array(
                "<?php /* require foo */",
            ),
            array(
                'exit(\'POST must include "file"\');',
            ),
            array(
                '<?php include_once "foo/".CONSTANT."/bar.php";',
                '<?php include_once("foo/".CONSTANT."/bar.php");',
            ),
            array(
                '<?php include_once "foo/".CONSTANT."/bar.php"; include_once "foo/".CONSTANT."/bar.php";',
                '<?php include_once("foo/".CONSTANT."/bar.php"); include_once("foo/".CONSTANT."/bar.php");',
            ),
            array(
                '<?php include_once "foo/".CONSTANT."/bar.php"; $foo = "bar";',
                '<?php include_once("foo/".CONSTANT."/bar.php"); $foo = "bar";',
            ),
            array(
                '<?php include_once "foo/".CONSTANT."/bar.php"; foo();',
                '<?php include_once("foo/".CONSTANT."/bar.php"); foo();',
            ),
            array(
                '<?php include_once "foo/" . CONSTANT . "/bar.php";',
                '<?php include_once("foo/" . CONSTANT . "/bar.php");',
            ),
            array(
                '<?php require ($a ? $b : $c) . $d;',
                '<?php require($a ? $b : $c) . $d;',
            ),
        );
    }
}
