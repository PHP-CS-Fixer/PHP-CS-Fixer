<?php

declare(strict_types=1);

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
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\IncludeFixer
 */
final class IncludeFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield [
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
        ];

        yield [
            '<?php include $a;',
            '<?php include  (  $a  )  ;',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php foo(require "foo.php");',
            '<?php foo(require("foo.php"));',
        ];

        yield [
            '<?php $foo[include "foo.php"];',
            '<?php $foo[include("foo.php")];',
        ];

        $template = '<?php %s';

        foreach (['require', 'require_once', 'include', 'include_once'] as $statement) {
            yield [
                sprintf($template.' "foo.php"?>', $statement),
                sprintf($template.' ("foo.php") ?>', $statement),
            ];

            yield [
                sprintf($template.' /**/"foo.php"// test
                    ?>', $statement),
                sprintf($template.'/**/ ("foo.php") // test
                    ?>', $statement),
            ];

            yield [
                sprintf($template.' $a;', $statement),
                sprintf($template.'$a;', $statement),
            ];

            yield [
                sprintf($template.' $a;', $statement),
                sprintf($template.'            $a;', $statement),
            ];

            yield [
                sprintf($template.' $a; ', $statement),
                sprintf($template.'            $a   ; ', $statement),
            ];

            yield [
                sprintf($template." /**/'foo.php';", $statement),
                sprintf($template."/**/'foo.php';", $statement),
            ];

            yield [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."'foo.php';", $statement),
            ];

            yield [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."           'foo.php';", $statement),
            ];

            yield [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."('foo.php');", $statement),
            ];

            yield [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."(           'foo.php');", $statement),
            ];

            yield [
                sprintf($template." 'foo.php';", $statement),
                sprintf($template."          ( 'foo.php' );", $statement),
            ];

            yield [
                sprintf($template." '\".__DIR__.\"/../bootstrap.php';", $statement),
            ];

            yield [
                sprintf('<?php // %s foo', $statement),
            ];

            yield [
                sprintf('<?php /* %s foo */', $statement),
            ];

            yield [
                sprintf('<?php /** %s foo */', $statement),
            ];

            yield [
                sprintf($template.'($a ? $b : $c) . $d;', $statement),
            ];

            yield [
                sprintf($template.' ($a ? $b : $c) . $d;', $statement),
            ];

            yield [
                sprintf('<?php exit("POST must %s \"file\"");', $statement),
            ];

            yield [
                sprintf('<?php ClassCollectionLoader::load(%s($this->getCacheDir().\'classes.map\'), $this->getCacheDir(), $name, $this->debug, false, $extension);', $statement),
            ];

            yield [
                sprintf('<?php $foo = (false === %s($zfLibraryPath."/Zend/Loader/StandardAutoloader.php"));', $statement),
            ];

            yield [
                sprintf($template.' "Buzz/foo-Bar.php";', $statement),
                sprintf($template.' (  "Buzz/foo-Bar.php" );', $statement),
            ];

            yield [
                sprintf($template.' "$buzz/foo-Bar.php";', $statement),
                sprintf($template.' (  "$buzz/foo-Bar.php" );', $statement),
            ];

            yield [
                sprintf($template.' "{$buzz}/foo-Bar.php";', $statement),
                sprintf($template.' (  "{$buzz}/foo-Bar.php" );', $statement),
            ];

            yield [
                sprintf($template.' $foo ? "foo.php" : "bar.php";', $statement),
                sprintf($template.'($foo ? "foo.php" : "bar.php");', $statement),
            ];

            yield [
                sprintf($template.' $foo  ?  "foo.php"  :  "bar.php";', $statement),
                sprintf($template.'($foo  ?  "foo.php"  :  "bar.php");', $statement),
            ];

            yield [
                sprintf("<?php return %s __DIR__.'foo.php';", $statement),
                sprintf("<?php return %s  __DIR__.'foo.php';", $statement),
            ];

            yield [
                sprintf("<?php \$foo = %s __DIR__.('foo.php');", $statement),
                sprintf("<?php \$foo = %s  __DIR__.('foo.php');", $statement),
            ];

            yield [
                sprintf("<?php     %s __DIR__.('foo.php');", $statement),
                sprintf("<?php     %s  (__DIR__.('foo.php'));", $statement),
            ];

            yield [
                sprintf("<?php     %s __DIR__ . ('foo.php');", $statement),
                sprintf("<?php     %s  (__DIR__ . ('foo.php'));", $statement),
            ];

            yield [
                sprintf("<?php %s dirname(__FILE__).'foo.php';", $statement),
                sprintf("<?php %s (dirname(__FILE__).'foo.php');", $statement),
            ];

            yield [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php";', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php");', $statement),
            ];

            yield [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; %s "foo/".CONSTANT."/bar.php";', $statement, $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); %s("foo/".CONSTANT."/bar.php");', $statement, $statement),
            ];

            yield [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; $foo = "bar";', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); $foo = "bar";', $statement),
            ];

            yield [
                sprintf('<?php %s "foo/".CONSTANT."/bar.php"; foo();', $statement),
                sprintf('<?php %s("foo/".CONSTANT."/bar.php"); foo();', $statement),
            ];

            yield [
                sprintf('<?php %s "foo/" . CONSTANT . "/bar.php";', $statement),
                sprintf('<?php %s("foo/" . CONSTANT . "/bar.php");', $statement),
            ];

            yield [
                sprintf('<?php %s SOME_CONST . "file.php"; %s Foo::Bar($baz);', $statement, $statement),
                sprintf('<?php %s( SOME_CONST . "file.php" ); %s Foo::Bar($baz);', $statement, $statement),
            ];

            yield [
                sprintf('<?php %s SOME_CONST . "file1.php"; %s Foo::Bar($baz);', $statement, $statement),
                sprintf('<?php %s          SOME_CONST . "file1.php"; %s Foo::Bar($baz);', $statement, $statement),
            ];

            yield $statement.': binary string lower case' => [
                sprintf($template." b'foo.php';", $statement),
                sprintf($template."(b'foo.php');", $statement),
            ];

            yield $statement.': binary string upper case' => [
                sprintf($template." B'foo.php';", $statement),
                sprintf($template."(B'foo.php');", $statement),
            ];
        }
    }
}
