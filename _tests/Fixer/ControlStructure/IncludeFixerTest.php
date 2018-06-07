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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\IncludeFixer
 */
final class IncludeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $template = '<?php %s';
        $tests = [
            [
                '<?php include # A
# B
# C
"a"# D
# E
# F
;# G
# H',
                '<?php include# A
(# B
# C
"a"# D
# E
)# F
;# G
# H',
            ],
            [
                '<?php include $a;',
                '<?php include  (  $a  )  ;',
            ],
            [
                '<?php
require_once "test1.php";
include_once "test2.php";
require "test3.php";
include "test4.php";',
                '<?php
require_once("test1.php");
include_once("test2.php");
require("test3.php");
include("test4.php");',
            ],
            [
                '<?php
require_once #1
#2
#3
"test1.php"#4
#5
#6
;',
                '<?php
require_once #1
(#2
#3
"test1.php"#4
)#5
#6
;',
            ],
        ];

        foreach (['require', 'require_once', 'include', 'include_once'] as $statement) {
            $tests[] = [
                sprintf($template.' "foo.php"?>', $statement),
                sprintf($template.' ("foo.php") ?>', $statement),
            ];

            $tests[] = [
                sprintf($template.' /**/"foo.php"// test
                    ?>', $statement),
                sprintf($template.'/**/ ("foo.php") // test
                    ?>', $statement),
            ];

            $tests[] = [
                sprintf($template.' $a;', $statement),
                sprintf($template.'$a;', $statement),
            ];

            $tests[] = [
                sprintf($template.' $a;', $statement),
                sprintf($template.'            $a;', $statement),
            ];

            $tests[] = [
                sprintf($template.' $a; ', $statement),
                sprintf($template.'            $a   ; ', $statement),
            ];

            $tests[] = [
                sprintf($template." /**/'foo.php';", $statement),
                sprintf($template."/**/'foo.php';", $statement),
            ];

            $tests[] = [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."'foo.php';", $statement),
            ];

            $tests[] = [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."           'foo.php';", $statement),
            ];

            $tests[] = [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."('foo.php');", $statement),
            ];

            $tests[] = [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."(           'foo.php');", $statement),
            ];

            $tests[] = [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."          ( 'foo.php' );", $statement),
            ];

            $tests[] = [
                sprintf($template." '\".__DIR__.\"/../bootstrap.php';", $statement),
            ];

            $tests[] = [
                sprintf('<?php // %s foo', $statement),
            ];

            $tests[] = [
                sprintf('<?php /* %s foo */', $statement),
            ];

            $tests[] = [
                sprintf('<?php /** %s foo */', $statement),
            ];

            $tests[] = [
                sprintf($template.'($a ? $b : $c) . $d;', $statement),
            ];

            $tests[] = [
                sprintf($template.' ($a ? $b : $c) . $d;', $statement),
            ];

            $tests[] = [
                sprintf('<?php exit("POST must %s \"file\"");', $statement),
            ];

            $tests[] = [
                sprintf('<?php ClassCollectionLoader::load(%s($this->getCacheDir().\'classes.map\'), $this->getCacheDir(), $name, $this->debug, false, $extension);', $statement),
            ];

            $tests[] = [
                sprintf('<?php $foo = (false === %s($zfLibraryPath."/Zend/Loader/StandardAutoloader.php"));', $statement),
            ];

            $tests[] = [
                sprintf($template.' "Buzz/foo-Bar.php";', $statement),
                sprintf($template.' (  "Buzz/foo-Bar.php" );', $statement),
            ];

            $tests[] = [
                sprintf($template.' "$buzz/foo-Bar.php";', $statement),
                sprintf($template.' (  "$buzz/foo-Bar.php" );', $statement),
            ];

            $tests[] = [
                sprintf($template.' "{$buzz}/foo-Bar.php";', $statement),
                sprintf($template.' (  "{$buzz}/foo-Bar.php" );', $statement),
            ];

            $tests[] = [
                sprintf($template.' $foo ? "foo.php" : "bar.php";', $statement),
                sprintf($template.'($foo ? "foo.php" : "bar.php");', $statement),
            ];

            $tests[] = [
                sprintf($template.' $foo  ?  "foo.php"  :  "bar.php";', $statement),
                sprintf($template.'($foo  ?  "foo.php"  :  "bar.php");', $statement),
            ];

            $tests[] = [
                sprintf("<?php return %s __DIR__.'foo.php';", $statement),
                sprintf("<?php return %s  __DIR__.'foo.php';", $statement),
            ];

            $tests[] = [
                sprintf("<?php \$foo = %s __DIR__.('foo.php');", $statement),
                sprintf("<?php \$foo = %s  __DIR__.('foo.php');", $statement),
            ];

            $tests[] = [
                sprintf("<?php     %s __DIR__.('foo.php');", $statement),
                sprintf("<?php     %s  (__DIR__.('foo.php'));", $statement),
            ];

            $tests[] = [
                sprintf("<?php     %s __DIR__ . ('foo.php');", $statement),
                sprintf("<?php     %s  (__DIR__ . ('foo.php'));", $statement),
            ];

            $tests[] = [
                sprintf("<?php %s dirname(__FILE__).'foo.php';", $statement),
                sprintf("<?php %s (dirname(__FILE__).'foo.php');", $statement),
            ];

            $tests[] = [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php";', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php");', $statement),
            ];

            $tests[] = [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; %s "foo/".CONSTANT."/bar.php";', $statement, $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); %s("foo/".CONSTANT."/bar.php");', $statement, $statement),
            ];

            $tests[] = [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; $foo = "bar";', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); $foo = "bar";', $statement),
            ];

            $tests[] = [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; foo();', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); foo();', $statement),
            ];

            $tests[] = [
                sprintf('<?php %s "foo/" . CONSTANT . "/bar.php";', $statement),
                sprintf('<?php %s("foo/" . CONSTANT . "/bar.php");', $statement),
            ];

            $tests[] = [
                sprintf('<?php %s SOME_CONST . "file.php"; %s Foo::Bar($baz);', $statement, $statement),
                sprintf('<?php %s( SOME_CONST . "file.php" ); %s Foo::Bar($baz);', $statement, $statement),
            ];
            $tests[] = [
                sprintf('<?php %s SOME_CONST . "file1.php"; %s Foo::Bar($baz);', $statement, $statement),
                sprintf('<?php %s          SOME_CONST . "file1.php"; %s Foo::Bar($baz);', $statement, $statement),
            ];
        }

        return $tests;
    }
}
