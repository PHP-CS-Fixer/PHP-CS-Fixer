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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\BracesFixer
 */
final class BracesFixerTest extends AbstractFixerTestCase
{
    private static $configurationOopPositionSameLine = array('position_after_functions_and_oop_constructs' => 'same');

    public function testInvalidConfigurationClassyConstructs()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[braces\] Invalid configuration: The option "position_after_functions_and_oop_constructs" with value "neither" is invalid\. Accepted values are: "next", "same"\.$#'
        );

        $this->fixer->configure(array('position_after_functions_and_oop_constructs' => 'neither'));
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixControlContinuationBracesCases
     */
    public function testFixControlContinuationBraces($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixControlContinuationBracesCases()
    {
        return array(
            array(
                '<?php
    $a = function() {
        $a = 1;
        while (false);
    };',
            ),
            array(
                '<?php
    $a = function() {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
            ),
            array(
                '<?php
    class Foo
    {
        public function A()
        {
            ?>
            Test<?php echo $foobar; ?>Test
            <?php
            $a = 1;
        }
    }',
            ),
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
    } catch (Exception $e) {
        echo 2;
    }',
                '<?php
    try
    {
        echo 1;
    }
    catch (Exception $e)
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
    }',
            ),
            array(
                '<?php
    if /* 1 */ (2) {
    }',
                '<?php
    if /* 1 */ (2) {}',
            ),
            array(
                '<?php
                    if (1) {
                        echo $items{0}->foo;
                        echo $collection->items{1}->property;
                    }
                ',
            ),
            array(
                '<?php
    $a = function() {
        $a = 1;
        while (false);
    };',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $a = function() {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class Foo {
        public function A() {
            ?>
            Test<?php echo $foobar; ?>Test
            <?php
            $a = 1;
        }
    }',
                '<?php
    class Foo
    {
        public function A()
        {
            ?>
            Test<?php echo $foobar; ?>Test
            <?php
            $a = 1;
        }
    }',
                self::$configurationOopPositionSameLine,
            ),
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    try {
        echo 1;
    } catch (Exception $e) {
        echo 2;
    }',
                '<?php
    try
    {
        echo 1;
    }
    catch (Exception $e)
    {
        echo 2;
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class Foo {
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
                self::$configurationOopPositionSameLine,
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
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    if (1) {
        do {
            $a = 1;
        } while (true);
    }',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    if /* 1 */ (2) {
    }',
                '<?php
    if /* 1 */ (2) {}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
                    if (1) {
                        echo $items{0}->foo;
                        echo $collection->items{1}->property;
                    }
                ',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php class A
/** */
{
}',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
class Foo
{
    public function foo()
    {
        foo();

        // baz
        bar();
    }
}',
                '<?php
class Foo
{
    public function foo(){
    foo();

    // baz
    bar();
    }
}',
            ),
            array(
                '<?php
class Foo
{
    public function foo($foo)
    {
        return $foo // foo
            ? \'foo\'
            : \'bar\'
        ;
    }
}',
            ),
            array(
                '<?php
class Foo
{
    /**
     * Foo.
     */
    public $foo;

    /**
     * Bar.
     */
    public $bar;
}',
                '<?php
class Foo {
  /**
   * Foo.
   */
  public $foo;

  /**
   * Bar.
   */
  public $bar;
}',
            ),
            array(
                '<?php
class Foo
{
    /*
     * Foo.
     */
    public $foo;

    /*
     * Bar.
     */
    public $bar;
}',
                '<?php
class Foo {
  /*
   * Foo.
   */
  public $foo;

  /*
   * Bar.
   */
  public $bar;
}',
            ),
            array(
                '<?php
if (1==1) {
    $a = 1;
    // test
    $b = 2;
}',
                '<?php
if (1==1) {
 $a = 1;
  // test
  $b = 2;
}',
            ),
            array(
                '<?php
if (1==1) {
    $a = 1;
    # test
    $b = 2;
}',
                '<?php
if (1==1) {
 $a = 1;
  # test
  $b = 2;
}',
            ),
            array(
                '<?php
if (1==1) {
    $a = 1;
    /** @var int $b */
    $b = a();
}',
                '<?php
if (1==1) {
    $a = 1;
    /** @var int $b */
$b = a();
}',
            ),
            array(
                '<?php
    if ($b) {
        if (1==1) {
            $a = 1;
            // test
            $b = 2;
        }
    }
',
                '<?php
    if ($b) {
        if (1==1) {
         $a = 1;
          // test
          $b = 2;
        }
    }
',
            ),
            array(
                '<?php
    if ($b) {
        if (1==1) {
            $a = 1;
            /* test */
            $b = 2;
            echo 123;//
        }
    }
',
                '<?php
    if ($b) {
        if (1==1) {
         $a = 1;
          /* test */
          $b = 2;
          echo 123;//
        }
    }
',
            ),
            array(
                '<?php
class A
{
    public function B()
    {/*
        */
        $a = 1;
    }
}',
                '<?php
class A {
    public function B()
    {/*
        */
      $a = 1;
    }
}',
            ),
            array(
                '<?php
class B
{
    public function B()
    {
        /*
            *//**/
        $a = 1;
    }
}',
                '<?php
class B {
    public function B()
    {
    /*
        *//**/
       $a = 1;
    }
}',
            ),
            array(
                '<?php
class C
{
    public function C()
    {
        /* */#
        $a = 1;
    }
}',
                '<?php
class C {
    public function C()
    {
    /* */#
       $a = 1;
    }
}',
            ),
            array(
                '<?php
if ($a) { /*
*/
    echo 1;
}',
                '<?php
if ($a){ /*
*/
echo 1;
}',
            ),
            array(
                '<?php
if ($a) { /**/ /*
*/
    echo 1;
    echo 2;
}',
                '<?php
if ($a){ /**/ /*
*/
echo 1;
echo 2;
}',
            ),
            array(
                '<?php
foreach ($foo as $bar) {
    if (true) {
    }
    // comment
    elseif (false) {
    }
}',
            ),
            array(
                '<?php
function foo()
{
    $bar = 1;                   // multiline ...
                                // ... comment
    $baz  = 2;                  // next comment
}',
            ),
            array(
                '<?php
function foo()
{
    $foo = 1;

    // multiline...
    // ... comment
    return $foo;
}',
                '<?php
function foo()
{
        $foo = 1;

        // multiline...
        // ... comment
        return $foo;
}',
            ),
            array(
                '<?php
function foo()
{
    $bar = 1;     /* bar */     // multiline ...
                                // ... comment
    $baz  = 2;    /* baz */     // next comment
}',
            ),
            array(
                '<?php
function test()
{
//    $closure = function ($callback) use ($query) {
//        doSomething();
//
//        return true;
//    };
    $a = 3;
}',
            ),
            array(
                '<?php
function test()
{
//    $closure = function ($callback) use ($query) {
//        doSomething();
//        '.'
//        return true;
//    };
    $a = 3;
}',
            ),
            array(
                '<?php
class Foo
{
    public function bar()
    {
        foreach (new Bar() as $file) {
            foo();
        }
    }
}',
                '<?php
class Foo {
    public function bar() {
        foreach (new Bar() as $file)
        {
            foo();
        }
    }
}',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixMissingBracesAndIndentCases
     */
    public function testFixMissingBracesAndIndent($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
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
for ($i = 1; $i < 10; ++$i) {
    echo $i;
}
for ($i = 1; $i < 10; ++$i) {
    echo $i;
}',
                '<?php
for ($i = 1; $i < 10; ++$i) echo $i;
for ($i = 1; $i < 10; ++$i) { echo $i; }',
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
    declare(ticks = 1) {
        $ticks = 1;
    }',
                '<?php
    declare  (
    ticks = 1  ) {
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
    return function () {
    };
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
            array(
                '<?php
    function myFunction($foo, $bar)
    {
        return \Foo::{$foo}($bar);
    }',
            ),
            array(
                '<?php
    class C
    {
        public function __construct(
        )
        //comment
        {
        }
    }',
                '<?php
    class C {
        public function __construct(
        )
        //comment
        {}
    }',
            ),
            array(
                '<?php
if (true):
    $foo = 0;
endif;',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (true)  :
    $foo = 0;
endif;',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    if (true) : $foo = 1; endif;',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (true) {
    $foo = 1;
}',
                '<?php
if (true)$foo = 1;',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (true) {
    $foo = 2;
}',
                '<?php
if (true)    $foo = 2;',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (true) {
    $foo = 3;
}',
                '<?php
if (true){$foo = 3;}',
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
for ($i = 1; $i < 10; ++$i) {
    echo $i;
}
for ($i = 1; $i < 10; ++$i) {
    echo $i;
}',
                '<?php
for ($i = 1; $i < 10; ++$i) echo $i;
for ($i = 1; $i < 10; ++$i) { echo $i; }',
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
do {
    echo 1;
} while (false);',
                '<?php
do { echo 1; } while (false);',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
while ($foo->next());',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
foreach ($foo as $bar) {
    echo $bar;
}',
                '<?php
foreach ($foo as $bar) echo $bar;',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {$a = 1;}',
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (true) {
    $a = 1;


    $b = 2;
}',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (1) {
    $a = 1;

    // comment at end
}',
                null,
                self::$configurationOopPositionSameLine,
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
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
foreach ($numbers as $num) {
    for ($i = 0; $i < $num; ++$i) {
        $a = "a";
    }
    $b = "b";
}',
                null,
                self::$configurationOopPositionSameLine,
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
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    declare(ticks = 1) {
        $ticks = 1;
    }',
                '<?php
    declare  (
    ticks = 1  ) {
  $ticks = 1;
    }',
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class ClassName {




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
                self::$configurationOopPositionSameLine,
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
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    interface Foo {
        public function setConfig(ConfigInterface $config);
    }',
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
function bar() {
    $a = 1; //comment
}',
                '<?php
function bar()
{
    $a = 1; //comment
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php

function & lambda() {
    return function () {
    };
}',
                '<?php

function & lambda()
{
    return function () {
    };
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
function nested() {
    $a = "a{$b->c()}d";
}',
                '<?php
function nested()
{
    $a = "a{$b->c()}d";
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
function foo() {
    $a = $b->{$c->d}($e);
    $f->{$g} = $h;
    $i->{$j}[$k] = $l;
    $m = $n->{$o};
    $p = array($q->{$r}, $s->{$t});
    $u->{$v}->w = 1;
}',
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
function mixed() {
    $a = $b->{"a{$c}d"}();
}',
                '<?php
function mixed()
{
    $a = $b->{"a{$c}d"}();
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
function mixedComplex() {
    $a = $b->{"a{$c->{\'foo-bar\'}()}d"}();
}',
                '<?php
function mixedComplex()
{
    $a = $b->{"a{$c->{\'foo-bar\'}()}d"}();
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
function mixedComplex() {
    $a = ${"b{$foo}"}->{"a{$c->{\'foo-bar\'}()}d"}();
}',
                '<?php
function mixedComplex()
{
    $a = ${"b{$foo}"}->{"a{$c->{\'foo-bar\'}()}d"}();
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    if (true):
        echo 1;
    else:
        echo 2;
    endif;
',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    if ($test) { //foo
        echo 1;
    }',
                null,
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php if (true) {
    echo "s";
} ?>x',
                '<?php if (true) echo "s" ?>x',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class Foo {
        public function getFaxNumbers() {
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),

            array(
                '<?php
    class Foo {
        public function main() {
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
                self::$configurationOopPositionSameLine,
            ),

            array(
                '<?php
class Foo {
    public function main() {
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class Foo {
        public $bar;
        public $baz;
    }',
                '<?php
    class Foo
    {
                public $bar;
                public $baz;
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    function myFunction($foo, $bar) {
        return \Foo::{$foo}($bar);
    }',
                '<?php
    function myFunction($foo, $bar)
    {
        return \Foo::{$foo}($bar);
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class C {
        public function __construct(
        )
        //comment
        {
        }
    }',
                '<?php
    class C {
        public function __construct(
        )
        //comment
        {}
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
class Something # a
{
    public function sth() //
    {
        return function (int $foo) use ($bar) {
            return $bar;
        };
    }
}

function C() /**/ //    # /**/
{
}

function D() /**
*
*/
{
}',
                '<?php
class Something # a
{
    public function sth() //
    {
        return function (int $foo) use ($bar) { return $bar; };
    }
}

function C() /**/ //    # /**/
{
}

function D() /**
*
*/
{
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if ($foo) {
    foo();

//    if ($bar === \'bar\') {
//        return [];
//    }
} else {
    bar();
}
',
            ),
            array(
                '<?php
if ($foo) {
    foo();

//    if ($bar === \'bar\') {
    //        return [];
//    }
} else {
    bar();
}
',
            ),
            array(
                '<?php
if ($foo) {
    foo();

//    if ($bar === \'bar\') {
//        return [];
//    }
    '.'
    $bar = \'bar\';
} else {
    bar();
}
',
            ),
            array(
                '<?php
if ($foo) {
    foo();

//    bar();
    '.'
    $bar = \'bar\';
} else {
    bar();
}
',
            ),
            array(
                '<?php
if ($foo) {
    foo();
//    bar();
    '.'
    $bar = \'bar\';
} else {
    bar();
}
',
            ),
            array(
                '<?php
if ($foo) {
    foo();
    '.'
//    bar();
    $bar = \'bar\';
} else {
    bar();
}
',
            ),
            array(
                '<?php
if ($foo) {
    foo();
    '.'
//    bar();
} else {
    bar();
}
',
            ),
            array(
                '<?php
function foo()
{
    $a = 1;
    // we will return sth
    return $a;
}
',
                '<?php
function foo()
{
    $a = 1;
// we will return sth
    return $a;
}
',
            ),
            array(
                '<?php
function foo()
{
    $a = 1;
    '.'
//    bar();
    // we will return sth
    return $a;
}
',
                '<?php
function foo()
{
    $a = 1;
    '.'
//    bar();
// we will return sth
    return $a;
}
',
            ),
            array(
                '<?php
function foo()
{
    $a = 1;
//    if ($a === \'bar\') {
//        return [];
//    }
    // we will return sth
    return $a;
}
',
                '<?php
function foo()
{
    $a = 1;
//    if ($a === \'bar\') {
//        return [];
//    }
// we will return sth
    return $a;
}
',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixClassyBracesCases
     */
    public function testFixClassyBraces($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
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
            array(
                '<?php
                    class FooA {
                    }',
                '<?php
                    class FooA {}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
                    class FooB {
                    }',
                '<?php
                    class FooB{}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
                    class FooC {
                    }',
                '<?php
                    class FooC
{}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
                    interface FooD {
                    }',
                '<?php
                    interface FooD {}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
                class TestClass extends BaseTestClass implements TestInterface {
                    private $foo;
                }',
                '<?php
                class TestClass extends BaseTestClass implements TestInterface { private $foo;}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
abstract class Foo {
    public function getProcess($foo) {
        return true;
    }
}',
                '<?php
abstract class Foo
{
    public function getProcess($foo)
    {
        return true;
    }
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
function foo() {
    return "$c ($d)";
}',
                '<?php
function foo()
{
    return "$c ($d)";
}',
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixClassyBraces54Cases
     * @requires PHP 5.4
     */
    public function testFixClassyBraces54($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
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
            array(
                '<?php
    trait TFoo {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixAnonFunctionInShortArraySyntax54Cases
     * @requires PHP 5.4
     */
    public function testFixAnonFunctionInShortArraySyntax54($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
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
                "callback" => function ($data) { return true; },
            ],
        ];
    }',
            ),
            array(
                '<?php
    function myFunction() {
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
                "callback" => function ($data) { return true; },
            ],
        ];
    }',
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixCommentBeforeBraceCases
     */
    public function testFixCommentBeforeBrace($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCommentBeforeBraceCases()
    {
        return array(
            array(
                '<?php ',
            ),
            array(
                '<?php
    if ($test) { // foo
        echo 1;
    }',
                '<?php
    if ($test) // foo
    {
        echo 1;
    }',
            ),
            array(
                '<?php
    $foo = function ($x) use ($y) { // foo
        echo 1;
    };',
                '<?php
    $foo = function ($x) use ($y) // foo
    {
        echo 1;
    };',
            ),
            array(
                '<?php ',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    if ($test) { // foo
        echo 1;
    }',
                '<?php
    if ($test) // foo
    {
        echo 1;
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $foo = function ($x) use ($y) { // foo
        echo 1;
    };',
                '<?php
    $foo = function ($x) use ($y) // foo
    {
        echo 1;
    };',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    // 2.5+ API
    if (isNewApi()) {
        echo "new API";
    // 2.4- API
    } elseif (isOldApi()) {
        echo "old API";
    // 2.4- API
    } else {
        echo "unknown API";
        // sth
    }

    return $this->guess($class, $property, function (Constraint $constraint) use ($guesser) {
        return $guesser->guessRequiredForConstraint($constraint);
    // Fallback to false...
    // ... due to sth...
    }, false);
    ',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixCommentBeforeBrace70Cases
     * @requires PHP 7.0
     */
    public function testFixCommentBeforeBrace70($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCommentBeforeBrace70Cases()
    {
        return array(
            array(
                '<?php
    $foo = new class ($a) extends Foo implements Bar { // foo
        private $x;
    };',
                '<?php
    $foo = new class ($a) extends Foo implements Bar // foo
    {
        private $x;
    };',
            ),
            array(
                '<?php
    $foo = new class ($a) extends Foo implements Bar { // foo
        private $x;
    };',
                '<?php
    $foo = new class ($a) extends Foo implements Bar // foo
    {
        private $x;
    };',
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixWhitespaceBeforeBraceCases
     */
    public function testFixWhitespaceBeforeBrace($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    while ($file = $this->getFile()) {
    }',
                '<?php
    while ($file = $this->getFile())
    {
    }',
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixFunctionsCases
     */
    public function testFixFunctions($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
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
    });
',
            ),
            array(
                '<?php
    filter(function   ($a) {
    });',
                '<?php
    filter(function   ($a)
    {});',
            ),
            array(
                '<?php
    filter(function   ($b) {
    });',
                '<?php
    filter(function   ($b){});',
            ),
            array(
                '<?php
    foo(array_map(function ($object) use ($x, $y) {
        return array_filter($object->bar(), function ($o) {
            return $o->isBaz();
        });
    }, $collection));',
                '<?php
    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));',
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
    $fnc = function ($a, $b) { // random comment
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) // random comment
    {
        return 0;
    };',
            ),
            array(
                '<?php
    $fnc = function ($a, $b) { # random comment
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) # random comment
    {
        return 0;
    };',
            ),
            array(
                '<?php
    $fnc = function ($a, $b) /* random comment */ {
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /* random comment */
    {
        return 0;
    };',
            ),
            array(
                '<?php
    $fnc = function ($a, $b) /** random comment */ {
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /** random comment */
    {
        return 0;
    };',
            ),
            array(
                '<?php
    function download() {
    }',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
class Foo {
    public function AAAA() {
    }

    public function BBBB() {
    }

    public function CCCC() {
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    filter(function () {
        return true;
    });
',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    filter(function   ($a) {
    });',
                '<?php
    filter(function   ($a)
    {});',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    filter(function   ($b) {
    });',
                '<?php
    filter(function   ($b){});',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    foo(array_map(function ($object) use ($x, $y) {
        return array_filter($object->bar(), function ($o) {
            return $o->isBaz();
        });
    }, $collection));',
                '<?php
    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
class Foo {
    public static function bar() {
        return 1;
    }
}',
                '<?php
class Foo
{
    public static function bar()
    {
        return 1;
    }
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    usort($this->fixers, function &($a, $b) use ($selfName) {
        return 1;
    });',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    usort(
        $this->fixers,
        function &($a, $b) use ($selfName) {
            return 1;
        }
    );',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $fnc = function ($a, $b) { // random comment
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) // random comment
    {
        return 0;
    };',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $fnc = function ($a, $b) { # random comment
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) # random comment
    {
        return 0;
    };',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $fnc = function ($a, $b) /* random comment */ {
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /* random comment */
    {
        return 0;
    };',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $fnc = function ($a, $b) /** random comment */ {
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /** random comment */
    {
        return 0;
    };',
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixSpaceAroundTokenCases
     */
    public function testFixSpaceAroundToken($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
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
    $foo = function& () use ($bar) {
    };',
                '<?php
    $foo = function& ()use($bar){};',
            ),
            array(
                '<?php

// comment
declare(strict_types=1);

// comment
while (true) {
}',
            ),
            array(
                '<?php
declare(ticks   =   1) {
}',
                '<?php
declare   (   ticks   =   1   )   {
}',
            ),
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
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
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $foo = function& () use ($bar) {
    };',
                '<?php
    $foo = function& ()use($bar){};',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php

// comment
declare(strict_types=1);

// comment
while (true) {
}',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
declare(ticks   =   1) {
}',
                '<?php
declare   (   ticks   =   1   )   {
}',
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFix55Cases
     * @requires PHP 5.5
     */
    public function testFix55($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFix55Cases()
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
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFix56Cases
     * @requires PHP 5.6
     */
    public function testFix56($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFix56Cases()
    {
        return array(
            array(
                '<?php
    use function Foo\bar;
    if (true) {
    }',
            ),
            array(
                '<?php
    use function Foo\bar;
    if (true) {
    }',
                null,
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return array(
            array(
                '<?php
    function foo($a)
    {
        // foo
        $foo = new class($a) extends Foo {
            public function bar()
            {
            }
        };
    }',
                '<?php
    function foo($a)
    {
        // foo
        $foo = new class($a) extends Foo { public function bar() {} };
    }',
            ),
            array(
                '<?php
    foo(1, new class implements Logger {
        public function log($message)
        {
            log($message);
        }
    }, 3);',
                '<?php
    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);',
            ),
            array(
                '<?php
$message = (new class() implements FooInterface {
});',
                '<?php
$message = (new class() implements FooInterface{});',
            ),
            array(
                '<?php $message = (new class() {
});',
                '<?php $message = (new class() {});',
            ),
            array(
                '<?php
if (1) {
    $message = (new class() extends Foo {
        public function bar()
        {
            echo 1;
        }
    });
}',
                '<?php
if (1) {
  $message = (new class() extends Foo
  {
    public function bar() { echo 1; }
  });
}',
            ),
            array(
                '<?php
    class Foo
    {
        public function use()
        {
        }

        public function use1(): string
        {
        }
    }
                ',
                '<?php
    class Foo
    {
        public function use() {
        }

        public function use1(): string {
        }
    }
                ',
            ),
            array(
                '<?php
    $a = function (int $foo): string {
        echo $foo;
    };

    $b = function (int $foo) use ($bar): string {
        echo $foo . $bar;
    };

    function a()
    {
    }
                ',
                '<?php
    $a = function (int $foo): string
    {
        echo $foo;
    };

    $b = function (int $foo) use($bar): string
    {
        echo $foo . $bar;
    };

    function a() {
    }
                ',
            ),
            array(
                '<?php
    class Something
    {
        public function sth(): string
        {
            return function (int $foo) use ($bar): string {
                return $bar;
            };
        }
    }',
                '<?php
    class Something
    {
        public function sth(): string
        {
            return function (int $foo) use ($bar): string { return $bar; };
        }
    }',
            ),
            array(
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
            ),
            array(
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
            ),
            array(
                '<?php
    function foo($a) {
        // foo
        $foo = new class($a) extends Foo {
            public function bar() {
            }
        };
    }',
                '<?php
    function foo($a)
    {
        // foo
        $foo = new class($a) extends Foo { public function bar() {} };
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    foo(1, new class implements Logger {
        public function log($message) {
            log($message);
        }
    }, 3);',
                '<?php
    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
$message = (new class() implements FooInterface {
});',
                '<?php
$message = (new class() implements FooInterface{});',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php $message = (new class() {
});',
                '<?php $message = (new class() {});',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
if (1) {
    $message = (new class() extends Foo {
        public function bar() {
            echo 1;
        }
    });
}',
                '<?php
if (1) {
  $message = (new class() extends Foo
  {
    public function bar() { echo 1; }
  });
}',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class Foo {
        public function use() {
        }

        public function use1(): string {
        }
    }
                ',
                '<?php
    class Foo
    {
        public function use() {
        }

        public function use1(): string {
        }
    }
                ',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    $a = function (int $foo): string {
        echo $foo;
    };

    $b = function (int $foo) use ($bar): string {
        echo $foo . $bar;
    };

    function a() {
    }
                ',
                '<?php
    $a = function (int $foo): string
    {
        echo $foo;
    };

    $b = function (int $foo) use($bar): string
    {
        echo $foo . $bar;
    };

    function a() {
    }
                ',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
    class Something {
        public function sth(): string {
            return function (int $foo) use ($bar): string {
                return $bar;
            };
        }
    }',
                '<?php
    class Something
    {
        public function sth(): string
        {
            return function (int $foo) use ($bar): string { return $bar; };
        }
    }',
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider providePreserveLineAfterControlBraceCases
     */
    public function testPreserveLineAfterControlBrace($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function providePreserveLineAfterControlBraceCases()
    {
        return array(
            array(
                '<?php
if (1==1) { // test
    $a = 1;
}
echo $a;',
                '<?php
if (1==1) // test
{ $a = 1; }
echo $a;',
            ),
            array(
                '<?php
if ($test) { // foo
    echo 1;
}
if (1 === 1) {//a
    $a = "b"; /*d*/
}//c
echo $a;
if ($a === 3) /**/
{echo 1;}
',
                '<?php
if ($test) // foo
 {
    echo 1;
}
if (1 === 1)//a
{$a = "b"; /*d*/}//c
echo $a;
if ($a === 3) /**/
{echo 1;}
',
            ),
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
            array(
                '<?php
if (true) {

    //  The blank line helps with legibility in nested control structures
    if (true) {
        // if body
    }

    // if body
}',
                null,
                self::$configurationOopPositionSameLine,
            ),
            array(
                "<?php if (true) {\r\n\r\n// CRLF newline\n}",
                null,
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixWithAllowOnelineLambdaCases
     */
    public function testFixWithAllowSingleLineClosure($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->fixer->configure(array(
            'allow_single_line_closure' => true,
        ));

        $this->doTest($expected, $input);
    }

    public function provideFixWithAllowOnelineLambdaCases()
    {
        return array(
            array(
                '<?php
    $callback = function () { return true; };',
            ),
            array(
                '<?php
    $callback = function () { if ($a) { return true; } return false; };',
                '<?php
    $callback = function () { if($a){ return true; } return false; };',
            ),
            array(
                '<?php
    $callback = function () { if ($a) { return true; } return false; };',
                '<?php
    $callback = function () { if($a) return true; return false; };',
            ),
            array(
                '<?php
    $callback = function () {
        if ($a) {
            return true;
        }
        return false;
    };',
                '<?php
    $callback = function () { if($a) return true;
    return false; };',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideDoWhileLoopInsideAnIfWithoutBracketsCases
     */
    public function testDoWhileLoopInsideAnIfWithoutBrackets($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideDoWhileLoopInsideAnIfWithoutBracketsCases()
    {
        return array(
            array(
                '<?php
if (true) {
    do {
        echo 1;
    } while (false);
}',
                '<?php
if (true)
    do {
        echo 1;
    } while (false);',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return array(
            array(
                '<?php
if (true) {'."\r\n"
    ."\t".'if (true) {'."\r\n"
        ."\t\t".'echo 1;'."\r\n"
    ."\t".'} elseif (true) {'."\r\n"
        ."\t\t".'echo 2;'."\r\n"
    ."\t".'} else {'."\r\n"
        ."\t\t".'echo 3;'."\r\n"
    ."\t".'}'."\r\n"
.'}',
                '<?php
if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;',
            ),
            array(
                '<?php
if (true) {'."\r\n"
    ."\t".'if (true) {'."\r\n"
        ."\t\t".'echo 1;'."\r\n"
    ."\t".'} elseif (true) {'."\r\n"
        ."\t\t".'echo 2;'."\r\n"
    ."\t".'} else {'."\r\n"
        ."\t\t".'echo 3;'."\r\n"
    ."\t".'}'."\r\n"
.'}',
                '<?php
if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;',
                self::$configurationOopPositionSameLine,
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideNowdocInTemplatesCases
     */
    public function testNowdocInTemplates($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideNowdocInTemplatesCases()
    {
        return array(
            array(
                <<<'EOT'
<?php
if (true) {
    $var = <<<'NOWDOC'
NOWDOC;
?>
<?php
}

EOT
,
                <<<'EOT'
<?php
if (true) {
$var = <<<'NOWDOC'
NOWDOC;
?>
<?php
}

EOT
,
            ),
            array(
                <<<'EOT'
<?php
if (true) {
    $var = <<<HEREDOC
HEREDOC;
?>
<?php
}

EOT
,
                <<<'EOT'
<?php
if (true) {
$var = <<<HEREDOC
HEREDOC;
?>
<?php
}

EOT
,
            ),
        );
    }
}
