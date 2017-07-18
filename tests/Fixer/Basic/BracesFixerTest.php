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
    private static $configurationOopPositionSameLine = ['position_after_functions_and_oop_constructs' => 'same'];
    private static $configurationCtrlStructPositionNextLine = ['position_after_control_structures' => 'next'];
    private static $configurationAnonymousPositionNextLine = ['position_after_anonymous_constructs' => 'next'];

    public function testInvalidConfigurationClassyConstructs()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '#^\[braces\] Invalid configuration: The option "position_after_functions_and_oop_constructs" with value "neither" is invalid\. Accepted values are: "next", "same"\.$#'
        );

        $this->fixer->configure(['position_after_functions_and_oop_constructs' => 'neither']);
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
        return [
            [
                '<?php
    $a = function() {
        $a = 1;
        while (false);
    };',
            ],
            [
                '<?php
    $a = function() {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    if (1) {
        self::${$key} = $val;
        self::${$type}[$rule] = $pattern;
        self::${$type}[$rule] = array_merge($pattern, self::${$type}[$rule]);
        self::${$type}[$rule] = $pattern + self::${$type}["rules"];
    }
                ',
            ],
            [
                '<?php
    if (1) {
        do {
            $a = 1;
        } while (true);
    }',
            ],
            [
                '<?php
    if /* 1 */ (2) {
    }',
                '<?php
    if /* 1 */ (2) {}',
            ],
            [
                '<?php
                    if (1) {
                        echo $items{0}->foo;
                        echo $collection->items{1}->property;
                    }
                ',
            ],
            [
                '<?php
    $a = function() {
        $a = 1;
        while (false);
    };',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    $a = function()
    {
        $a = 1;
        while (false);
    };',
                '<?php
    $a = function() {
        $a = 1;
        while (false);
    };',
                self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    $a = function() {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    $a = function()
    {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
                '<?php
    $a = function() {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
                self::$configurationAnonymousPositionNextLine,
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    if (true)
    {
        $a = 1;
    }
    else
    {
        $b = 2;
    }',
                '<?php
    if (true) {
        $a = 1;
    }
    else {
        $b = 2;
    }',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php
    try
    {
        throw new \Exception();
    }
    catch (\LogicException $e)
    {
        // do nothing
    }
    catch (\Exception $e)
    {
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
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    if (1) {
        do {
            $a = 1;
        } while (true);
    }',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    if /* 1 */ (2) {
    }',
                '<?php
    if /* 1 */ (2) {}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
                    if (1) {
                        echo $items{0}->foo;
                        echo $collection->items{1}->property;
                    }
                ',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php class A
/** */
{
}',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
foreach ($foo as $bar) {
    if (true) {
    }
    // comment
    elseif (false) {
    }
}',
            ],
            [
                '<?php
function foo()
{
    $bar = 1;                   // multiline ...
                                // ... comment
    $baz  = 2;                  // next comment
}',
            ],
            [
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
            ],
            [
                '<?php
function foo()
{
    $bar = 1;     /* bar */     // multiline ...
                                // ... comment
    $baz  = 2;    /* baz */     // next comment
}',
            ],
        ];
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
        return [
            [
                '<?php
if (true):
    $foo = 0;
endif;',
            ],
            [
                '<?php
if (true)  :
    $foo = 0;
endif;',
            ],
            [
                '<?php
    if (true) : $foo = 1; endif;',
            ],
            [
                '<?php
if (true) {
    $foo = 1;
}',
                '<?php
if (true)$foo = 1;',
            ],
            [
                '<?php
if (true) {
    $foo = 2;
}',
                '<?php
if (true)    $foo = 2;',
            ],
            [
                '<?php
if (true) {
    $foo = 3;
}',
                '<?php
if (true){$foo = 3;}',
            ],
            [
                '<?php
if (true) {
    echo 1;
} else {
    echo 2;
}',
                '<?php
if(true) { echo 1; } else echo 2;',
            ],
            [
                '<?php
if (true) {
    echo 3;
} else {
    echo 4;
}',
                '<?php
if(true) echo 3; else { echo 4; }',
            ],
            [
                '<?php
if (true) {
    echo 5;
} else {
    echo 6;
}',
                '<?php
if (true) echo 5; else echo 6;',
            ],
            [
                '<?php
if (true) {
    while (true) {
        $foo = 1;
        $bar = 2;
    }
}',
                '<?php
if (true) while (true) { $foo = 1; $bar = 2;}',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
for ($i = 1; $i < 5; ++$i) {
    for ($i = 1; $i < 10; ++$i) {
        echo $i;
    }
}',
                '<?php
for ($i = 1; $i < 5; ++$i) for ($i = 1; $i < 10; ++$i) { echo $i; }',
            ],
            [
                '<?php
do {
    echo 1;
} while (false);',
                '<?php
do { echo 1; } while (false);',
            ],
            [
                '<?php
while ($foo->next());',
            ],
            [
                '<?php
foreach ($foo as $bar) {
    echo $bar;
}',
                '<?php
foreach ($foo as $bar) echo $bar;',
            ],
            [
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {$a = 1;}',
            ],
            [
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {
 $a = 1;
}',
            ],
            [
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
            ],
            [
                '<?php
if (true) {
    $a = 1;


    $b = 2;
}',
            ],
            [
                '<?php
if (1) {
    $a = 1;

    // comment at end
}',
            ],
            [
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
            ],
            [
                '<?php
foreach ($numbers as $num) {
    for ($i = 0; $i < $num; ++$i) {
        $a = "a";
    }
    $b = "b";
}',
            ],
            [
                '<?php
if (1) {
    if (2) {
        $foo = 2;

        if (3) {
            $foo = 3;
        }
    }
}',
            ],
            [
                '<?php
    declare(ticks = 1) {
        $ticks = 1;
    }',
                '<?php
    declare  (
    ticks = 1  ) {
  $ticks = 1;
    }',
            ],
            [
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
            ],
            [
                '<?php
    while (true) {
        foo();
    }',
                '<?php
    while (true)
    {
        foo();
    }',
            ],
            [
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
            ],
            [
                '<?php
    do {
        echo $test;
    } while ($test = $this->getTest());',
                '<?php
    do
    {
        echo $test;
    }while ($test = $this->getTest());',
            ],
            [
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
            ],
            [
                '<?php
    while ($true) {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            // do nothing
        }
    }',
            ],
            [
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
            ],
            [
                '<?php
function bar()
{
    $a = 1; //comment
}',
            ],
            [
                '<?php

function & lambda()
{
    return function () {
    };
}',
            ],
            [
                '<?php
function nested()
{
    $a = "a{$b->c()}d";
}',
            ],
            [
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
            ],
            [
                '<?php
function mixed()
{
    $a = $b->{"a{$c}d"}();
}',
            ],
            [
                '<?php
function mixedComplex()
{
    $a = $b->{"a{$c->{\'foo-bar\'}()}d"}();
}',
            ],
            [
                '<?php
function mixedComplex()
{
    $a = ${"b{$foo}"}->{"a{$c->{\'foo-bar\'}()}d"}();
}',
            ],
            [
                '<?php
    if (true):
        echo 1;
    else:
        echo 2;
    endif;
',
            ],
            [
                '<?php
    if ($test) { //foo
        echo 1;
    }',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php if (true) {
    echo "s";
} ?>x',
                '<?php if (true) echo "s" ?>x',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],

            [
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
            ],

            [
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
            ],
            [
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
            ],
            [
                '<?php
    function myFunction($foo, $bar)
    {
        return \Foo::{$foo}($bar);
    }',
            ],
            [
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
            ],
            [
                '<?php
if (true):
    $foo = 0;
endif;',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true)  :
    $foo = 0;
endif;',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    if (true) : $foo = 1; endif;',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    $foo = 1;
}',
                '<?php
if (true)$foo = 1;',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    $foo = 2;
}',
                '<?php
if (true)    $foo = 2;',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    $foo = 3;
}',
                '<?php
if (true){$foo = 3;}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    echo 1;
} else {
    echo 2;
}',
                '<?php
if(true) { echo 1; } else echo 2;',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    echo 3;
} else {
    echo 4;
}',
                '<?php
if(true) echo 3; else { echo 4; }',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    echo 5;
} else {
    echo 6;
}',
                '<?php
if (true) echo 5; else echo 6;',
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
for ($i = 1; $i < 5; ++$i) {
    for ($i = 1; $i < 10; ++$i) {
        echo $i;
    }
}',
                '<?php
for ($i = 1; $i < 5; ++$i) for ($i = 1; $i < 10; ++$i) { echo $i; }',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
do {
    echo 1;
} while (false);',
                '<?php
do { echo 1; } while (false);',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
while ($foo->next());',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
foreach ($foo as $bar) {
    echo $bar;
}',
                '<?php
foreach ($foo as $bar) echo $bar;',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {$a = 1;}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {
 $a = 1;
}',
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
                '<?php
if (true) {
    $a = 1;


    $b = 2;
}',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
if (1) {
    $a = 1;

    // comment at end
}',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
                '<?php
foreach ($numbers as $num) {
    for ($i = 0; $i < $num; ++$i) {
        $a = "a";
    }
    $b = "b";
}',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php
    while ($true)
    {
        try
        {
            throw new \Exception();
        }
        catch (\Exception $e)
        {
            // do nothing
        }
    }',
                '<?php
    while ($true) {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            // do nothing
        }
    }',
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php
    interface Foo {
        public function setConfig(ConfigInterface $config);
    }',
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php

function & lambda() {
    return function ()
    {
    };
}',
                '<?php

function & lambda()
{
    return function () {
    };
}',
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php
function nested() {
    $a = "a{$b->c()}d";
}',
                '<?php
function nested()
{
    $a = "a{$b->c()}d";
}',
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    if (true):
        echo 1;
    else:
        echo 2;
    endif;
',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    if (true):
        echo 1;
    else:
        echo 2;
    endif;
',
                null,
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    if ($test) { //foo
        echo 1;
    }',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
                '<?php
    if (true)
    {
        // foo
        // bar
        if (true)
        {
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php if (true) {
    echo "s";
} ?>x',
                '<?php if (true) echo "s" ?>x',
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
                '<?php
    class Foo {
        public function getFaxNumbers() {
            if (1)
            {
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    class Foo {
        public function getFaxNumbers() {
            if (1)
            {
                return $this->phoneNumbers->filter(function ($phone)
                {
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],

            [
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
            ],
            [
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
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
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
        return [
            [
                '<?php
                    class FooA
                    {
                    }',
                '<?php
                    class FooA {}',
            ],
            [
                '<?php
                    class FooB
                    {
                    }',
                '<?php
                    class FooB{}',
            ],
            [
                '<?php
                    class FooC
                    {
                    }',
                '<?php
                    class FooC
{}',
            ],
            [
                '<?php
                    interface FooD
                    {
                    }',
                '<?php
                    interface FooD {}',
            ],
            [
                '<?php
                class TestClass extends BaseTestClass implements TestInterface
                {
                    private $foo;
                }',
                '<?php
                class TestClass extends BaseTestClass implements TestInterface { private $foo;}',
            ],
            [
                '<?php
abstract class Foo
{
    public function getProcess($foo)
    {
        return true;
    }
}',
            ],
            ['<?php
function foo()
{
    return "$c ($d)";
}',
            ],
            [
                '<?php
                    class FooA {
                    }',
                '<?php
                    class FooA {}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
                    class FooB {
                    }',
                '<?php
                    class FooB{}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
                    class FooC {
                    }',
                '<?php
                    class FooC
{}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
                    interface FooD {
                    }',
                '<?php
                    interface FooD {}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
                class TestClass extends BaseTestClass implements TestInterface {
                    private $foo;
                }',
                '<?php
                class TestClass extends BaseTestClass implements TestInterface { private $foo;}',
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    trait TFoo
    {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
            ],
            [
                '<?php
    trait TFoo {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    trait TFoo
    {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
            ],
            [
                '<?php
    trait TFoo {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
                self::$configurationOopPositionSameLine,
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixAnonFunctionInShortArraySyntaxCases
     */
    public function testFixAnonFunctionInShortArraySyntax($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixAnonFunctionInShortArraySyntaxCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
                '<?php
    function myFunction() {
        return [
            [
                "callback" => function ($data)
                {
                    return true;
                }
            ],
            [
                "callback" => function ($data)
                {
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
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
        ];
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
        return [
            [
                '<?php ',
            ],
            [
                '<?php
    if ($test) { // foo
        echo 1;
    }',
                '<?php
    if ($test) // foo
    {
        echo 1;
    }',
            ],
            [
                '<?php
    $foo = function ($x) use ($y) { // foo
        echo 1;
    };',
                '<?php
    $foo = function ($x) use ($y) // foo
    {
        echo 1;
    };',
            ],
            [
                '<?php ',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
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
            ],
        ];
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
        return [
            [
                '<?php
    $foo = new class ($a) extends Foo implements Bar { // foo
        private $x;
    };',
                '<?php
    $foo = new class ($a) extends Foo implements Bar // foo
    {
        private $x;
    };',
            ],
            [
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
            ],
        ];
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
        return [
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true)
    {
        echo 1;
    }',
            ],
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true){
        echo 1;
    }',
            ],
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true)           {
        echo 1;
    }',
            ],
            [
                '<?php
    while ($file = $this->getFile()) {
    }',
                '<?php
    while ($file = $this->getFile())
    {
    }',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true){
        echo 1;
    }',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true)           {
        echo 1;
    }',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    while ($file = $this->getFile()) {
    }',
                '<?php
    while ($file = $this->getFile())
    {
    }',
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true)
    {
        echo 1;
    }',
                self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true){
        echo 1;
    }',
                self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    if (true) {
        echo 1;
    }',
                '<?php
    if (true)           {
        echo 1;
    }',
                self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    while ($file = $this->getFile()) {
    }',
                '<?php
    while ($file = $this->getFile())
    {
    }',
                self::$configurationAnonymousPositionNextLine,
            ],
            [
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
                self::$configurationAnonymousPositionNextLine,
            ],
        ];
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
        return [
            [
                '<?php
    function download()
    {
    }',
                '<?php
    function download() {
    }',
            ],
            [
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
            ],
            [
                '<?php
    filter(function () {
        return true;
    });
',
            ],
            [
                '<?php
    filter(function   ($a) {
    });',
                '<?php
    filter(function   ($a)
    {});',
            ],
            [
                '<?php
    filter(function   ($b) {
    });',
                '<?php
    filter(function   ($b){});',
            ],
            [
                '<?php
    foo(array_map(function ($object) use ($x, $y) {
        return array_filter($object->bar(), function ($o) {
            return $o->isBaz();
        });
    }, $collection));',
                '<?php
    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));',
            ],
            [
                '<?php
class Foo
{
    public static function bar()
    {
        return 1;
    }
}',
            ],
            [
                '<?php
    usort($this->fixers, function &($a, $b) use ($selfName) {
        return 1;
    });',
            ],
            [
                '<?php
    usort(
        $this->fixers,
        function &($a, $b) use ($selfName) {
            return 1;
        }
    );',
            ],
            [
                '<?php
    $fnc = function ($a, $b) { // random comment
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) // random comment
    {
        return 0;
    };',
            ],
            [
                '<?php
    $fnc = function ($a, $b) { # random comment
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) # random comment
    {
        return 0;
    };',
            ],
            [
                '<?php
    $fnc = function ($a, $b) /* random comment */ {
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /* random comment */
    {
        return 0;
    };',
            ],
            [
                '<?php
    $fnc = function ($a, $b) /** random comment */ {
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /** random comment */
    {
        return 0;
    };',
            ],
            [
                '<?php
    function download() {
    }',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
                '<?php
    filter(function () {
        return true;
    });
',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    filter(function   ($a) {
    });',
                '<?php
    filter(function   ($a)
    {});',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    filter(function   ($b) {
    });',
                '<?php
    filter(function   ($b){});',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    foo(array_map(function ($object) use ($x, $y) {
        return array_filter($object->bar(), function ($o) {
            return $o->isBaz();
        });
    }, $collection));',
                '<?php
    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    foo(array_map(function ($object) use ($x, $y)
    {
        return array_filter($object->bar(), function ($o)
        {
            return $o->isBaz();
        });
    }, $collection));',
                '<?php
    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));',
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
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
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    usort($this->fixers, function &($a, $b) use ($selfName) {
        return 1;
    });',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    usort(
        $this->fixers,
        function &($a, $b) use ($selfName) {
            return 1;
        }
    );',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixMultiLineStructures
     */
    public function testFixMultiLineStructures($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixMultiLineStructures()
    {
        return [
            [
                '<?php
    if (true === true
        && true === true
    ) {
    }',
                '<?php
    if(true === true
        && true === true
    )
    {
    }',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    foreach (
        $boo as $bar => $fooBarBazBuzz
    ) {
    }',
                '<?php
    foreach (
        $boo as $bar => $fooBarBazBuzz
    )
    {
    }',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    $foo = function (
        $baz,
        $boo
    ) {
    };',
                '<?php
    $foo = function (
        $baz,
        $boo
    )
    {
    };',
                self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    class Foo
    {
        public static function bar(
            $baz,
            $boo
        ) {
        }
    }',
                '<?php
    class Foo
    {
        public static function bar(
            $baz,
            $boo
        )
        {
        }
    }',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    if (true === true
        && true === true
    ) {
    }',
                '<?php
    if(true === true
        && true === true
    )
    {
    }',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    if ($foo)
    {
    }
    elseif (
        true === true
        && true === true
    ) {
    }',
                '<?php
    if ($foo)
    {
    }
    elseif (
        true === true
        && true === true
    )
    {
    }',
                self::$configurationCtrlStructPositionNextLine,
            ],
        ];
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
        return [
            [
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
            ],
            [
                '<?php
    do {
        echo 1;
    } while ($test);',
                '<?php
    do{
        echo 1;
    }while($test);',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    $foo = function& () use ($bar) {
    };',
                '<?php
    $foo = function& ()use($bar){};',
            ],
            [
                '<?php

// comment
declare(strict_types=1);

// comment
while (true) {
}',
            ],
            [
                '<?php
declare(ticks   =   1) {
}',
                '<?php
declare   (   ticks   =   1   )   {
}',
            ],
            [
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
            ],
            [
                '<?php
    do {
        echo 1;
    } while ($test);',
                '<?php
    do{
        echo 1;
    }while($test);',
                self::$configurationOopPositionSameLine,
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
    $foo = function& () use ($bar) {
    };',
                '<?php
    $foo = function& ()use($bar){};',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php

// comment
declare(strict_types=1);

// comment
while (true) {
}',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
declare(ticks   =   1) {
}',
                '<?php
declare   (   ticks   =   1   )   {
}',
                self::$configurationOopPositionSameLine,
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFinallyCases
     */
    public function testFinally($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFinallyCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
                '<?php
    try
    {
        throw new \Exception();
    }
    catch (\LogicException $e)
    {
        // do nothing
    }
    catch (\Exception $e)
    {
        // do nothing
    }
    finally
    {
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFunctionImportCases
     */
    public function testFunctionImport($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFunctionImportCases()
    {
        return [
            [
                '<?php
    use function Foo\bar;
    if (true) {
    }',
            ],
            [
                '<?php
    use function Foo\bar;
    if (true) {
    }',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    use function Foo\bar;
    if (true)
    {
    }',
                '<?php
    use function Foo\bar;
    if (true) {
    }',
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    use function Foo\bar;
    if (true) {
    }',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provide70Cases
     * @requires PHP 7.0
     */
    public function test70($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provide70Cases()
    {
        return [
            [
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
            ],
            [
                '<?php
    foo(1, new class implements Logger {
        public function log($message)
        {
            log($message);
        }
    }, 3);',
                '<?php
    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);',
            ],
            [
                '<?php
$message = (new class() implements FooInterface {
});',
                '<?php
$message = (new class() implements FooInterface{});',
            ],
            [
                '<?php $message = (new class() {
});',
                '<?php $message = (new class() {});',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
            ],
            [
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
            ],
            [
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
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    function foo($a)
    {
        // foo
        $foo = new class($a) extends Foo
        {
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
                self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    function foo($a) {
        // foo
        $foo = new class($a) extends Foo
        {
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
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
    foo(1, new class implements Logger {
        public function log($message) {
            log($message);
        }
    }, 3);',
                '<?php
    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
    foo(1, new class implements Logger {
        public function log($message)
        {
            log($message);
        }
    }, 3);',
                '<?php
    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
    foo(1, new class implements Logger
    {
        public function log($message) {
            log($message);
        }
    }, 3);',
                '<?php
    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);',
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
$message = (new class() implements FooInterface {
});',
                '<?php
$message = (new class() implements FooInterface{});',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
$message = (new class() implements FooInterface {
});',
                '<?php
$message = (new class() implements FooInterface{});',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
$message = (new class() implements FooInterface
{
});',
                '<?php
$message = (new class() implements FooInterface{});',
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php $message = (new class() {
});',
                '<?php $message = (new class() {});',
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php $message = (new class() {
});',
                '<?php $message = (new class() {});',
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php $message = (new class()
{
});',
                '<?php $message = (new class() {});',
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php
if (1)
{
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
                self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
if (1) {
    $message = (new class() extends Foo
    {
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
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
if (1) {
    $message = (new class() extends Foo
    {
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
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
if (1)
{
    $message = (new class() extends Foo
    {
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
                self::$configurationCtrlStructPositionNextLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
if (1)
{
    $message = (new class() extends Foo
    {
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
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
            ],
            [
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
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php
    $a = function (int $foo): string
    {
        echo $foo;
    };

    $b = function (int $foo) use ($bar): string
    {
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
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
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
            ],
            [
                '<?php
    class Something {
        public function sth(): string {
            return function (int $foo) use ($bar): string
            {
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
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                null,
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                null,
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
                self::$configurationOopPositionSameLine + self::$configurationAnonymousPositionNextLine,
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider providePreserveLineAfterControlBrace
     */
    public function testPreserveLineAfterControlBrace($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function providePreserveLineAfterControlBrace()
    {
        return [
            [
                '<?php
if (1==1) { // test
    $a = 1;
}
echo $a;',
                '<?php
if (1==1) // test
{ $a = 1; }
echo $a;',
            ],
            [
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
            ],
            [
                '<?php
if (true) {

    //  The blank line helps with legibility in nested control structures
    if (true) {
        // if body
    }

    // if body
}',
            ],
            [
                "<?php if (true) {\r\n\r\n// CRLF newline\n}",
            ],
            [
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
            ],
            [
                '<?php
if (true)
{

    //  The blank line helps with legibility in nested control structures
    if (true)
    {
        // if body
    }

    // if body
}',
                '<?php
if (true) {

    //  The blank line helps with legibility in nested control structures
    if (true) {
        // if body
    }

    // if body
}',
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
            [
                "<?php if (true) {\r\n\r\n// CRLF newline\n}",
                null,
                self::$configurationOopPositionSameLine,
            ],
            [
                "<?php if (true)
{\r\n\r\n// CRLF newline\n}",
                "<?php if (true){\r\n\r\n// CRLF newline\n}",
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
        ];
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

        $this->fixer->configure([
            'allow_single_line_closure' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAllowOnelineLambdaCases()
    {
        return [
            [
                '<?php
    $callback = function () { return true; };',
            ],
            [
                '<?php
    $callback = function () { if ($a) { return true; } return false; };',
                '<?php
    $callback = function () { if($a){ return true; } return false; };',
            ],
            [
                '<?php
    $callback = function () { if ($a) { return true; } return false; };',
                '<?php
    $callback = function () { if($a) return true; return false; };',
            ],
            [
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
            ],
        ];
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
        return [
            [
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
            ],
            [
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
            ],
            [
                '<?php
if (true)'
."\r\n".'{'."\r\n"
    ."\t".'if (true)'."\r\n\t".'{'."\r\n"
        ."\t\t".'echo 1;'."\r\n"
    ."\t".'}'
    ."\r\n\t".'elseif (true)'
    ."\r\n\t".'{'."\r\n"
        ."\t\t".'echo 2;'."\r\n"
    ."\t".'}'
    ."\r\n\t".'else'
    ."\r\n\t".'{'."\r\n"
        ."\t\t".'echo 3;'."\r\n"
    ."\t".'}'."\r\n"
.'}',
                '<?php
if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;',
                self::$configurationOopPositionSameLine + self::$configurationCtrlStructPositionNextLine,
            ],
        ];
    }

    public function provideDoWhileLoopInsideAnIfWithoutBrackets()
    {
        return [
            [
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
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideDoWhileLoopInsideAnIfWithoutBrackets
     */
    public function testDoWhileLoopInsideAnIfWithoutBrackets($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }
}
