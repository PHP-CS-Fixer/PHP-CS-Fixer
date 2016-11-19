<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class IncludeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider testFixProvider
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testFixProvider()
    {
        $template = '<?php %s';
        $tests = array();
        foreach (array('require', 'require_once', 'include', 'include_once') as $statement) {
            $tests[] = array(
                sprintf($template.' "foo.php"?>', $statement),
                sprintf($template.' ("foo.php") ?>', $statement),
            );

            $tests[] = array(
                sprintf($template.' /**/ "foo.php"// test
                    ?>', $statement),
                sprintf($template.' /**/ ("foo.php") // test
                    ?>', $statement),
            );

            $tests[] = array(
                sprintf($template.' $a;', $statement),
                sprintf($template.'$a;', $statement),
            );

            $tests[] = array(
                sprintf($template.' $a;', $statement),
                sprintf($template.'            $a;', $statement),
            );

            $tests[] = array(
                sprintf($template.' $a; ', $statement),
                sprintf($template.'            $a   ; ', $statement),
            );

            $tests[] = array(
                sprintf($template." /**/'foo.php';", $statement),
                sprintf($template."/**/'foo.php';", $statement),
            );

            $tests[] = array(
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."'foo.php';", $statement),
            );

            $tests[] = array(
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."           'foo.php';", $statement),
            );

            $tests[] = array(
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."('foo.php');", $statement),
            );

            $tests[] = array(
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."(           'foo.php');", $statement),
            );

            $tests[] = array(
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."          ( 'foo.php' );", $statement),
            );

            $tests[] = array(
                sprintf($template." '\".__DIR__.\"/../bootstrap.php';", $statement),
            );

            $tests[] = array(
                sprintf('<?php // %s foo', $statement),
            );

            $tests[] = array(
                sprintf('<?php /* %s foo */', $statement),
            );

            $tests[] = array(
                sprintf('<?php /** %s foo */', $statement),
            );

            $tests[] = array(
                sprintf($template.'($a ? $b : $c) . $d;', $statement),
            );

            $tests[] = array(
                sprintf($template.' ($a ? $b : $c) . $d;', $statement),
            );

            $tests[] = array(
                sprintf('<?php exit("POST must %s \"file\"");', $statement),
            );

            $tests[] = array(
                sprintf('<?php ClassCollectionLoader::load(%s($this->getCacheDir().\'classes.map\'), $this->getCacheDir(), $name, $this->debug, false, $extension);', $statement),
            );

            $tests[] = array(
                sprintf('<?php $foo = (false === %s($zfLibraryPath."/Zend/Loader/StandardAutoloader.php"));', $statement),
            );

            $tests[] = array(
                sprintf($template.' "Buzz/foo-Bar.php";', $statement),
                sprintf($template.' (  "Buzz/foo-Bar.php" );', $statement),
            );

            $tests[] = array(
                sprintf($template.' "$buzz/foo-Bar.php";', $statement),
                sprintf($template.' (  "$buzz/foo-Bar.php" );', $statement),
            );

            $tests[] = array(
                sprintf($template.' "{$buzz}/foo-Bar.php";', $statement),
                sprintf($template.' (  "{$buzz}/foo-Bar.php" );', $statement),
            );

            $tests[] = array(
                sprintf($template.' $foo ? "foo.php" : "bar.php";', $statement),
                sprintf($template.'($foo ? "foo.php" : "bar.php");', $statement),
            );

            $tests[] = array(
                sprintf($template.' $foo  ?  "foo.php"  :  "bar.php";', $statement),
                sprintf($template.'($foo  ?  "foo.php"  :  "bar.php");', $statement),
            );

            $tests[] = array(
                sprintf("<?php return %s __DIR__.'foo.php';", $statement),
                sprintf("<?php return %s  __DIR__.'foo.php';", $statement),
            );

            $tests[] = array(
                sprintf("<?php \$foo = %s __DIR__.('foo.php');", $statement),
                sprintf("<?php \$foo = %s  __DIR__.('foo.php');", $statement),
            );

            $tests[] = array(
                sprintf("<?php     %s __DIR__.('foo.php');", $statement),
                sprintf("<?php     %s  (__DIR__.('foo.php'));", $statement),
            );

            $tests[] = array(
                sprintf("<?php     %s __DIR__ . ('foo.php');", $statement),
                sprintf("<?php     %s  (__DIR__ . ('foo.php'));", $statement),
            );

            $tests[] = array(
                sprintf("<?php %s dirname(__FILE__).'foo.php';", $statement),
                sprintf("<?php %s (dirname(__FILE__).'foo.php');", $statement),
            );

            $tests[] = array(
                sprintf('<?php %s "foo/".CONSTANT."/bar.php";', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php");', $statement),
            );

            $tests[] = array(
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; %s "foo/".CONSTANT."/bar.php";', $statement, $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); %s("foo/".CONSTANT."/bar.php");', $statement, $statement),
            );

            $tests[] = array(
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; $foo = "bar";', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); $foo = "bar";', $statement),
            );

            $tests[] = array(
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; foo();', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); foo();', $statement),
            );

            $tests[] = array(
                sprintf('<?php %s "foo/" . CONSTANT . "/bar.php";', $statement),
                sprintf('<?php %s("foo/" . CONSTANT . "/bar.php");', $statement),
            );

            $tests[] = array(
                sprintf('<?php %s SOME_CONST . "file.php"; %s Foo::Bar($baz);', $statement, $statement),
                sprintf('<?php %s( SOME_CONST . "file.php" ); %s Foo::Bar($baz);', $statement, $statement),
            );
            $tests[] = array(
                sprintf('<?php %s SOME_CONST . "file1.php"; %s Foo::Bar($baz);', $statement, $statement),
                sprintf('<?php %s          SOME_CONST . "file1.php"; %s Foo::Bar($baz);', $statement, $statement),
            );
        }

        return $tests;
    }
}
