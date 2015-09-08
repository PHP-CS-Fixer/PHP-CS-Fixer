<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BracesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixControlContinuationBracesCases
     */
    public function testFixControlContinuationBraces($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixControlContinuationBracesCases()
    {
        return array(
            array(
                '<?php
    if (true) {
        $a = 1;
    } else {
        $b = 2;
    }',
                '<?php
    if (true) {
        $a = 1;
    }
    else {
        $b = 2;
    }',
            ),
            array(
                '<?php
    try {
        throw new \Exception();
    } catch (\LogicException $e) {
        // do nothing
    } catch (\Exception $e) {
        // do nothing
    }',
                '<?php
    try {
        throw new \Exception();
    }catch (\LogicException $e) {
        // do nothing
    }
    catch (\Exception $e) {
        // do nothing
    }',
            ),
            array(
                '<?php
    if (true) {
        echo 1;
    } elseif (true) {
        echo 2;
    }',
                '<?php
    if (true) {
        echo 1;
    } elseif (true)
    {
        echo 2;
    }',
            ),
            array(
                '<?php
    try {
        echo 1;
    } catch (Exception $2) {
        echo 2;
    }',
                '<?php
    try
    {
        echo 1;
    }
    catch (Exception $2)
    {
        echo 2;
    }',
            ),
            array(
                '<?php
    class Foo
    {
        public function bar(
            FooInterface $foo,
            BarInterface $bar,
            array $data = []
        ) {
        }
    }',
                '<?php
    class Foo
    {
        public function bar(
            FooInterface $foo,
            BarInterface $bar,
            array $data = []
        ){
        }
    }',
            ),
            array(
                '<?php
    if (1) {
        self::${$key} = $val;
        self::${$type}[$rule] = $pattern;
        self::${$type}[$rule] = array_merge($pattern, self::${$type}[$rule]);
        self::${$type}[$rule] = $pattern + self::${$type}["rules"];
    }
                ',
            ),
            array(
            '<?php
    if (1) {
        do {
            $a = 1;
        } while (true);
    }
    ',
            ),
        );
    }

    /**
     * @dataProvider provideFixMissingBracesAndIndentCases
     */
    public function testFixMissingBracesAndIndent($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixMissingBracesAndIndentCases()
    {
        return array(
            array(
                '<?php
if (true):
    $foo = 0;
endif;',
            ),
            array(
                '<?php
if (true)  :
    $foo = 0;
endif;',
            ),
            array(
                '<?php
    if (true) : $foo = 1; endif;',
            ),
            array(
                '<?php
if (true) {
    $foo = 1;
}',
                '<?php
if (true)$foo = 1;',
            ),
            array(
                '<?php
if (true) {
    $foo = 2;
}',
                '<?php
if (true)    $foo = 2;',
            ),
            array(
                '<?php
if (true) {
    $foo = 3;
}',
                '<?php
if (true){$foo = 3;}',
            ),
            array(
                '<?php
if (true) {
    echo 1;
} else {
    echo 2;
}',
                '<?php
if(true) { echo 1; } else echo 2;',
            ),
            array(
                '<?php
if (true) {
    echo 3;
} else {
    echo 4;
}',
                '<?php
if(true) echo 3; else { echo 4; }',
            ),
            array(
                '<?php
if (true) {
    echo 5;
} else {
    echo 6;
}',
                '<?php
if (true) echo 5; else echo 6;',
            ),
            array(
                '<?php
if (true) {
    while (true) {
        $foo = 1;
        $bar = 2;
    }
}',
                '<?php
if (true) while (true) { $foo = 1; $bar = 2;}',
            ),
            array(
                '<?php
if (true) {
    if (true) {
        echo 1;
    } else {
        echo 2;
    }
} else {
    echo 3;
}',
                '<?php
if (true) if (true) echo 1; else echo 2; else echo 3;',
            ),
            array(
                '<?php
if (true) {
    // sth here...

    if ($a && ($b || $c)) {
        $d = 1;
    }
}',
                '<?php
if (true) {
    // sth here...

    if ($a && ($b || $c)) $d = 1;
}',
            ),
            array(
                '<?php
for ($i = 1; $i < 10; ++$) {
    echo $i;
}
for ($i = 1; $i < 10; ++$) {
    echo $i;
}',
                '<?php
for ($i = 1; $i < 10; ++$) echo $i;
for ($i = 1; $i < 10; ++$) { echo $i; }',
            ),
            array(
                '<?php
for ($i = 1; $i < 5; ++$i) {
    for ($i = 1; $i < 10; ++$i) {
        echo $i;
    }
}',
                '<?php
for ($i = 1; $i < 5; ++$i) for ($i = 1; $i < 10; ++$i) { echo $i; }',
            ),
            array(
                '<?php
do {
    echo 1;
} while (false);',
                '<?php
do { echo 1; } while (false);',
            ),
            array(
                '<?php
while ($foo->next());',
            ),
            array(
                '<?php
foreach ($foo as $bar) {
    echo $bar;
}',
                '<?php
foreach ($foo as $bar) echo $bar;',
            ),
            array(
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {$a = 1;}',
            ),
            array(
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {
 $a = 1;
}',
            ),
            array(
                '<?php
if (true) {
    $a = 1;
    $b = 2;
    while (true) {
        $c = 3;
    }
    $d = 4;
}',
                '<?php
if (true) {
 $a = 1;
        $b = 2;
  while (true) {
            $c = 3;
                        }
        $d = 4;
}',
            ),
            array(
                '<?php
if (true) {
    $a = 1;


    $b = 2;
}',
            ),
            array(
                '<?php
if (1) {
    $a = 1;

    // comment at end
}',
            ),
            array(
                '<?php
if (1) {
    if (2) {
        $a = "a";
    } elseif (3) {
        $b = "b";
        // comment
    } else {
        $c = "c";
    }
    $d = "d";
}',
            ),
            array(
                '<?php
foreach ($numbers as $num) {
    for ($i = 0; $i < $num; ++$i) {
        $a = "a";
    }
    $b = "b";
}',
            ),
            array(
                '<?php
if (1) {
    if (2) {
        $foo = 2;

        if (3) {
            $foo = 3;
        }
    }
}',
            ),
            array(
                '<?php
    declare (ticks=1) {
        $ticks = 1;
    }',
                '<?php
    declare (ticks=1) {
  $ticks = 1;
    }',
            ),
            array(
                '<?php
    if (true) {
        foo();
    } elseif (true) {
        bar();
    }',
                '<?php
    if (true)
    {
        foo();
    } elseif (true)
    {
        bar();
    }',
            ),
            array(
                '<?php
    while (true) {
        foo();
    }',
                '<?php
    while (true)
    {
        foo();
    }',
            ),
            array(
                '<?php
    do {
        echo $test;
    } while ($test = $this->getTest());',
                '<?php
    do
    {
        echo $test;
    }
    while ($test = $this->getTest());',
            ),
            array(
                '<?php
    do {
        echo $test;
    } while ($test = $this->getTest());',
                '<?php
    do
    {
        echo $test;
    }while ($test = $this->getTest());',
            ),
            array(
                '<?php
    class ClassName
    {
        /**
         * comment
         */
        public $foo = null;
    }',
                '<?php
    class ClassName
    {




        /**
         * comment
         */
        public $foo = null;


    }',
            ),
            array(
                '<?php
    while ($true) {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            // do nothing
        }
    }',
            ),
            array(
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
            ),
            array(
                '<?php
function bar()
{
    $a = 1; //comment
}',
            ),
            array(
                '<?php

function & lambda()
{
    return function () {};
}',
            ),
            array(
                '<?php
function nested()
{
    $a = "a{$b->c()}d";
}',
            ),
            array(
                '<?php
function foo()
{
    $a = $b->{$c->d}($e);
    $f->{$g} = $h;
    $i->{$j}[$k] = $l;
    $m = $n->{$o};
    $p = array($q->{$r}, $s->{$t});
    $u->{$v}->w = 1;
}',
            ),
            array(
                '<?php
function mixed()
{
    $a = $b->{"a{$c}d"}();
}',
            ),
            array(
                '<?php
function mixedComplex()
{
    $a = $b->{"a{$c->{\'foo-bar\'}()}d"}();
}',
            ),
            array(
                '<?php
function mixedComplex()
{
    $a = ${"b{$foo}"}->{"a{$c->{\'foo-bar\'}()}d"}();
}',
            ),
            array(
                '<?php
    if (true):
        echo 1;
    else:
        echo 2;
    endif;
',
            ),
            array(
                '<?php
    if ($test) { //foo
        echo 1;
    }',
            ),
            array(
                '<?php
    if (true) {
        // foo
        // bar

        if (true) {
            print("foo");
            print("bar");
        }
    }',
                '<?php
    if (true)
        // foo
        // bar
            {
        if (true)
        {
            print("foo");
            print("bar");
        }
    }',
            ),
            array(
                '<?php
    if (true) {
        // foo
        /* bar */
        if (true) {
            print("foo");
            print("bar");
        }
    }',
                '<?php
    if (true)
        // foo
        /* bar */{
        if (true)
        {
            print("foo");
            print("bar");
        }
    }',
            ),
            array(
                '<?php if (true) {
    echo "s";
} ?>x',
                '<?php if (true) echo "s" ?>x',
            ),
            array(
                '<?php
    class Foo
    {
        public function getFaxNumbers()
        {
            if (1) {
                return $this->phoneNumbers->filter(function ($phone) {
                    $a = 1;
                    $b = 1;
                    $c = 1;
                    return ($phone->getType() === 1) ? true : false;
                });
            }
        }
    }',
                '<?php
    class Foo
    {
        public function getFaxNumbers()
        {
            if (1)
                return $this->phoneNumbers->filter(function ($phone) {
                    $a = 1;
                    $b = 1;
                    $c = 1;
                    return ($phone->getType() === 1) ? true : false;
                });
        }
    }',
            ),
            array(
                '<?php
if (true) {
    if (true) {
        echo 1;
    } elseif (true) {
        echo 2;
    } else {
        echo 3;
    }
}
',
                '<?php
if(true)
    if(true)
        echo 1;
    elseif(true)
        echo 2;
    else
        echo 3;
',
            ),
            array(
                '<?php
if (true) {
    if (true) {
        echo 1;
    } elseif (true) {
        echo 2;
    } else {
        echo 3;
    }
}
echo 4;
',
                '<?php
if(true)
    if(true)
        echo 1;
    elseif(true)
        echo 2;
    else
        echo 3;
echo 4;
',
            ),
            array(
                '<?php
if (true) {
    if (true) {
        echo 1;
    } elseif (true) {
        echo 2;
    } else {
        echo 3;
    }
}',
                '<?php
if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;',
            ),
            array(
                '<?php
if (true) {
    if (true) {
        echo 1;
    } else {
        echo 2;
    }
} else {
    echo 3;
}',
                '<?php
if(true) if(true) echo 1; else echo 2; else echo 3;',
            ),
            array(
                '<?php
if (true) {
    try {
        echo 1;
    } catch (Exception $e) {
        echo 2;
    } catch (Exception $e) {
        echo 3;
    }
} else {
    echo 4;
}',
                '<?php
if (true)
    try
        echo 1;
    catch(Exception $e)
        echo 2;
    catch(Exception $e)
        echo 3;
else
    echo 4;',
            ),
            array(
                '<?php
foreach ($data as $val) {
    // test val
    if ($val === "errors") {
        echo "!";
    }
}',
                '<?php
foreach ($data as $val)
    // test val
    if ($val === "errors") {
        echo "!";
    }',
            ),
            array(
                '<?php
if (1) {
    foreach ($data as $val) {
        // test val
        if ($val === "errors") {
            echo "!";
        }
    }
}',
                '<?php
if (1)
    foreach ($data as $val)
        // test val
        if ($val === "errors") {
            echo "!";
        }',
            ),
            array(
                '<?php
class Foo
{
    public function main()
    {
        echo "Hello";
    }
}',
                '<?php
class Foo
{
  public function main()
  {
    echo "Hello";
  }
}',
            ),
            array(
                '<?php
    class Foo
    {
        public $bar;
        public $baz;
    }',
                '<?php
    class Foo
    {
                public $bar;
                public $baz;
    }',
            ),
        );
    }

    /**
     * @dataProvider provideFixClassyBracesCases
     */
    public function testFixClassyBraces($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixClassyBracesCases()
    {
        return array(
            array(
                '<?php
                    class FooA
                    {
                    }',
                '<?php
                    class FooA {}',
            ),
            array(
                '<?php
                    class FooB
                    {
                    }',
                '<?php
                    class FooB{}',
            ),
            array(
                '<?php
                    class FooC
                    {
                    }',
                '<?php
                    class FooC
{}',
            ),
            array(
                '<?php
                    interface FooD
                    {
                    }',
                '<?php
                    interface FooD {}',
            ),
            array(
                '<?php
                class TestClass extends BaseTestClass implements TestInterface
                {
                    private $foo;
                }',
                '<?php
                class TestClass extends BaseTestClass implements TestInterface { private $foo;}',
            ),
            array(
                '<?php
<?php

abstract class Foo
{
    public function getProcess($foo)
    {
        return true;
    }
}',
            ),
            array('<?php
function foo()
{
    return "$c ($d)";
}',
            ),
        );
    }

    /**
     * @dataProvider provideFixClassyBraces54Cases
     * @requires PHP 5.4
     */
    public function testFixClassyBraces54($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixClassyBraces54Cases()
    {
        return array(
            array(
                '<?php
    trait TFoo
    {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
            ),
        );
    }

    /**
     * @dataProvider provideFixAnonFunctionInShortArraySyntax54Cases
     * @requires PHP 5.4
     */
    public function testFixAnonFunctionInShortArraySyntax54($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixAnonFunctionInShortArraySyntax54Cases()
    {
        return array(
            array(
                '<?php
    function myFunction()
    {
        return [
            [
                "callback" => function ($data) {
                    return true;
                }
            ],
            [
                "callback" => function ($data) {
                    return true;
                },
            ],
        ];
    }',
            ),
        );
    }

    /**
     * @dataProvider provideFixCommentBeforeBraceCases
     */
    public function testFixCommentBeforeBrace($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCommentBeforeBraceCases()
    {
        return array(
            array(
                '<?php
    if ($test) {
        // foo

        echo 1;
    }',
                '<?php
    if ($test) // foo
    {
        echo 1;
    }',
            ),
        );
    }

    /**
     * @dataProvider provideFixWhitespaceBeforeBraceCases
     */
    public function testFixWhitespaceBeforeBrace($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixWhitespaceBeforeBraceCases()
    {
        return array(
            array(
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true)
    {
        echo 1;
    }',
            ),
            array(
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true){
        echo 1;
    }',
            ),
            array(
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true)           {
        echo 1;
    }',
            ),
            array(
                '<?php
    while ($file = $this->getFile()) {
    }',
                '<?php
    while ($file = $this->getFile())
    {
    }',
            ),
            array(
                '<?php
    switch (n) {
        case label1:
            echo 1;
            echo 2;
            break;
        default:
            echo 3;
            echo 4;
    }',
                '<?php
    switch (n)
    {
        case label1:
            echo 1;
            echo 2;
            break;
        default:
            echo 3;
            echo 4;
    }',
            ),
        );
    }

    /**
     * @dataProvider provideFixFunctionsCases
     */
    public function testFixFunctions($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixFunctionsCases()
    {
        return array(
            array(
                '<?php
    function download()
    {
    }',
                '<?php
    function download() {
    }',
            ),
            array(
                '<?php
class Foo
{
    public function AAAA()
    {
    }

    public function BBBB()
    {
    }

    public function CCCC()
    {
    }
}',
                '<?php
class Foo
{
    public function AAAA(){
    }

    public function BBBB()   {
    }

    public function CCCC()
    {
    }
}',
            ),
            array(
                '<?php
    filter(function () {
        return true;
    })
',
            ),
            array(
                '<?php
    filter(function   ($a) {});',
                '<?php
    filter(function   ($a)
    {});',
            ),
            array(
                '<?php
    filter(function   ($b) {});',
                '<?php
    filter(function   ($b){});',
            ),
            array(
                '<?php
class Foo
{
    public static function bar()
    {
        return 1;
    }
}',
            ),
            array(
                '<?php
    usort($this->fixers, function &($a, $b) use ($selfName) {
        return 1;
    });',
            ),
            array(
                '<?php
    usort(
        $this->fixers,
        function &($a, $b) use ($selfName) {
            return 1;
        }
    );',
            ),
            array(
                '<?php
    use function Foo\bar;
    if (true) {
    }',
            ),
        );
    }

    /**
     * @dataProvider provideFixSpaceAroundTokenCases
     */
    public function testFixSpaceAroundToken($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixSpaceAroundTokenCases()
    {
        return array(
            array(
                '<?php
    try {
        throw new Exception();
    } catch (Exception $e) {
        log($e);
    }',
                '<?php
    try{
        throw new Exception();
    }catch (Exception $e){
        log($e);
    }',
            ),
            array(
                '<?php
    do {
        echo 1;
    } while ($test);',
                '<?php
    do{
        echo 1;
    }while($test);',
            ),
            array(
                '<?php
    if (true === true
        && true === true
    ) {
    }',
                '<?php
    if(true === true
        && true === true
    )     {
    }',
            ),
            array(
                '<?php
    if (1) {
    }
    if ($this->tesT ($test)) {
    }',
                '<?php
    if(1){
    }
    if ($this->tesT ($test)) {
    }',
            ),
            array(
                '<?php
    if (true) {
    } elseif (false) {
    } else {
    }',
                '<?php
    if(true){
    }elseif(false){
    }else{
    }',
            ),
            array(
                '<?php
    $foo = function& () use ($bar) {}',
                '<?php
    $foo = function& ()use($bar){}',
            ),
            array(
                '<?php

// comment
declare (ticks = 1);

// comment
while (true) {
}',
            ),
        );
    }

    /**
     * @dataProvider provide55Cases
     * @requires PHP 5.5
     */
    public function test55($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provide55Cases()
    {
        return array(
            array(
                '<?php
    try {
        throw new \Exception();
    } catch (\LogicException $e) {
        // do nothing
    } catch (\Exception $e) {
        // do nothing
    } finally {
        echo "finish!";
    }',
                '<?php
    try {
        throw new \Exception();
    }catch (\LogicException $e) {
        // do nothing
    }
    catch (\Exception $e) {
        // do nothing
    }
    finally     {
        echo "finish!";
    }',
            ),
            array(
                '<?php
try {
    try {
        echo 1;
    } catch (Exception $e) {
        echo 2;
    } catch (Exception $e) {
        echo 3;
    } finally {
        echo 4;
    }
} catch (Exception $e) {
    echo 5;
}',
                '<?php
try
    try
        echo 1;
    catch(Exception $e)
        echo 2;
    catch(Exception $e)
        echo 3;
    finally
        echo 4;
catch(Exception $e)
    echo 5;',
            ),
        );
    }

    /**
     * @dataProvider providePreserveLineAfterControlBrace
     */
    public function testPreserveLineAfterControlBrace($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function providePreserveLineAfterControlBrace()
    {
        return array(
            array(
                '<?php
if (true) {

    //  The blank line helps with legibility in nested control structures
    if (true) {
        // if body
    }

    // if body
}',
            ),
            array(
                "<?php if (true) {\r\n\r\n// CRLF newline\n}",
            ),
        );
    }
}
