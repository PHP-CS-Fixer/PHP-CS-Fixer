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

namespace PhpCsFixer\Tests\Fixer\Basic;

use LogicException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\LaravelBracesFixer
 */
final class LaravelBracesFixerTest extends AbstractFixerTestCase
{
    public function testNoConfigurationClassyConstructs(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/Cannot configure using Abstract parent/');

        $this->fixer->configure(['some_option' => 'some_value']);
    }

    /**
     * @dataProvider provideFixControlContinuationBracesCases
     */
    public function testFixControlContinuationBraces(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixControlContinuationBracesCases(): array
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
            ],
            [
                '<?php
    $a = function() {
        $a = 1;
        while (false);
    };',
                '<?php
    $a = function()
    {
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
    $a = function() {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
                '<?php
    $a = function()    {
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
                '<?php
    class Foo{
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
                '<?php class A
/** */
{
}',
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
    $baz = 2;                   // next comment
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
    $baz = 2;     /* baz */     // next comment
}',
            ],
            [
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
            ],
            [
                '<?php if ($condition) { ?>
echo 1;
<?php } else { ?>
echo 2;
<?php } ?>
',
            ],
            [
                '<?php $arr = [true, false]; ?>
<?php foreach ($arr as $index => $item) if ($item): ?>
    <?php echo $index; ?>
<?php endif; ?>',
            ],
        ];
    }

    /**
     * @dataProvider provideFixMissingBracesAndIndentCases
     */
    public function testFixMissingBracesAndIndent(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixMissingBracesAndIndentCases(): array
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
    class ClassName {




        /**
         * comment
         */
        public $foo = null;


    }',
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
    class ClassName {




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
    while ($true) {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            // do nothing
        }
    }',
                '<?php
    while ($true) {
        try {
            throw new \Exception();
        }
        catch (\Exception $e)
        {
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
                '<?php
    interface Foo{
        public function setConfig(ConfigInterface $config);
    }',
            ],
            [
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
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
                '<?php
function bar() {
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
                '<?php

function & lambda(){
    return function () {
    };
}',
            ],
            [
                '<?php

function & lambda()
{
    return function () {
    };
}',
                '<?php

function & lambda()
{
    return function ()
    {
    };
}',
            ],
            [
                '<?php

function & lambda()
{
    return function () {
    };
}',
                '<?php

function & lambda() {
    return function ()
    {
    };
}',
            ],
            [
                '<?php
function nested()
{
    $a = "a{$b->c()}d";
}',
                '<?php
function nested() {
    $a = "a{$b->c()}d";
}',
            ],
            [
                '<?php
function nested()
{
    $a = "a{$b->c()}d";
}',
                '<?php
function nested() {
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
                '<?php
function foo() {
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
function foo()
{
    $a = $b->{$c->d}($e);
    $f->{$g} = $h;
    $i->{$j}[$k] = $l;
    $m = $n->{$o};
    $p = array($q->{$r}, $s->{$t});
    $u->{$v}->w = 1;
}',
                '<?php
function foo() {
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
                '<?php
function mixed() {
    $a = $b->{"a{$c}d"}();
}',
            ],
            [
                '<?php
function mixedComplex()
{
    $a = $b->{"a{$c->{\'foo-bar\'}()}d"}();
}',
                '<?php
function mixedComplex() {
    $a = $b->{"a{$c->{\'foo-bar\'}()}d"}();
}',
            ],
            [
                '<?php
function mixedComplex()
{
    $a = ${"b{$foo}"}->{"a{$c->{\'foo-bar\'}()}d"}();
}',
                '<?php
function mixedComplex() {
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
        public function getFaxNumbers(){
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
    class Foo {
        public function getFaxNumbers(){
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
    class Foo {
        public function getFaxNumbers() {
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
    class Foo {
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
class Foo {
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
class Foo {
  public function main() {
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
    class Foo {
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
                '<?php
    function myFunction($foo, $bar) {
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixClassyBracesCases
     */
    public function testFixClassyBraces(string $expected, ?string $input = null): void
    {
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
                '<?php
abstract class Foo
{
    public function getProcess($foo) {
        return true;
    }
}',
            ],
            [
                '<?php
function foo()
{
    return "$c ($d)";
}',
                '<?php
function foo() {
    return "$c ($d)";
}',
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
    trait TFoo
    {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
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
    trait TFoo
    {
        public $a;
    }',
                '<?php
    trait TFoo {public $a;}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixAnonFunctionInShortArraySyntaxCases
     */
    public function testFixAnonFunctionInShortArraySyntax(string $expected, ?string $input = null): void
    {
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
                \'callback\' => function ($data) {
                    return true;
                }
            ],
            [
                \'callback\' => function ($data) {
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
                \'callback\' => function ($data) {
                        return true;
                    }
            ],
            [
                \'callback\' => function ($data) { return true; },
            ],
        ];
    }',
            ],
            [
                '<?php
    function myFunction()
    {
        return [
            [
                \'callback\' => function ($data) {
                    return true;
                }
            ],
            [
                \'callback\' => function ($data) {
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
                \'callback\' => function ($data) {
                        return true;
                    }
            ],
            [
                \'callback\' => function ($data) { return true; },
            ],
        ];
    }',
            ],
            [
                '<?php
    function myFunction()
    {
        return [
            [
                \'callback\' => function ($data) {
                    return true;
                }
            ],
            [
                \'callback\' => function ($data) {
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
                \'callback\' => function ($data) {
                        return true;
                    }
            ],
            [
                \'callback\' => function ($data) { return true; },
            ],
        ];
    }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixCommentBeforeBraceCases
     */
    public function testFixCommentBeforeBrace(string $expected, ?string $input = null): void
    {
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
            ],
            [
                '<?php
if ($a) { //
?><?php ++$a;
} ?>',
            ],
            [
                '<?php
if ($a) { /* */ /* */ /* */ /* */ /* */
?><?php ++$a;
} ?>',
            ],
            [
                '<?php
    $foo = new class ($a) extends Foo implements Bar
    { // foo
        private $x;
    };',
                '<?php
    $foo = new class ($a) extends Foo implements Bar

    { // foo
        private $x;
    };',
            ],
            [
                '<?php
    $foo = new class ($a) extends Foo implements Bar
    { // foo
        private $x;
    };',
                '<?php
    $foo = new class ($a) extends Foo implements Bar{ // foo
        private $x;
    };',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWhitespaceBeforeBraceCases
     */
    public function testFixWhitespaceBeforeBrace(string $expected, ?string $input = null): void
    {
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
        ];
    }

    /**
     * @dataProvider provideFixFunctionsCases
     */
    public function testFixFunctions(string $expected, ?string $input = null): void
    {
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
    function download()
    {
    }',
                null,
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
                null,
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
                '<?php
class Foo {
    public static function bar()
    {
        return 1;
    }
}',
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
                '<?php
class Foo{
    public static function bar(){
        return 1;}
}',
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
                '<?php
class Foo
{
    public static function bar()  { return 1; }
}',
            ],
            [
                '<?php
    usort($this->fixers, function &($a, $b) use ($selfName) {
        return 1;
    });',
                null,
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
        ];
    }

    /**
     * @dataProvider provideFixMultiLineStructuresCases
     */
    public function testFixMultiLineStructures(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixMultiLineStructuresCases()
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
            ],
            [
                '<?php
    if ($foo) {
    } elseif (
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixSpaceAroundTokenCases
     */
    public function testFixSpaceAroundToken(string $expected, ?string $input = null): void
    {
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
                null,
            ],
            [
                '<?php
declare(ticks   =   1) {
}',
                '<?php
declare   (   ticks   =   1   )   {
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFinallyCases
     */
    public function testFinally(string $expected, ?string $input = null): void
    {
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
            ],
        ];
    }

    /**
     * @dataProvider provideFunctionImportCases
     */
    public function testFunctionImport(string $expected, ?string $input = null): void
    {
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
            ],
            [
                '<?php
    use function Foo\bar;
    if (true) {
    }',
                '<?php
    use function Foo\bar;
    if (true)
    {
    }',
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
     * @dataProvider provideFix70Cases
     * @requires     PHP 7.0
     */
    public function testFix70(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
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
            ],
            [
                '<?php
    foo(1, new class implements Logger
    {
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

$message = (new class() implements FooInterface {});',
                null,
            ],
            [
                '<?php $message = (new class(){});',
                null,
            ],
            [
                '<?php
if (1) {
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
            ],
            [
                '<?php
    foo(1, new class implements Logger
    {
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
    foo(1, new class implements Logger
    {
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
    foo(1, new class implements Logger
    {
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
$message = (new class() implements FooInterface
{
});',
                '<?php
$message = (new class() implements FooInterface
{

});',
            ],
            [
                '<?php
$message = (new class() implements FooInterface
{
});',
                '<?php
$message = (new class() implements FooInterface
{});',
            ],
            [
                '<?php
$message = (new class() implements FooInterface
{
});',
                '<?php
$message = (new class() implements FooInterface
   {
});',
            ],
            [
                '<?php $message = (new class() {});',
                null,
            ],
            [
                '<?php $message = (new class(){});',
                null,
            ],
            [
                '<?php $message = (new class()
{
});',
                '<?php $message = (new class() {

});',
            ],
            [
                '<?php
if (1) {
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
            ],
            [
                '<?php
if (1) {
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
            ],
            [
                '<?php
if (1) {
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
            ],
            [
                '<?php
if (1) {
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
            ],
            [
                '<?php
if (1) {
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
            ],
            [
                '<?php
if (1) {
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
                null,
            ],
            [
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                null,
            ],
            [
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                null,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
            ],
            [
                '<?php
$foo = new class () extends \Exception
{
};
',
                '<?php
$foo = new class () extends \Exception {

};
',
            ],
            [
                '<?php
$foo = new class () extends \Exception
{
};
',
                null,
            ],
            [
                '<?php
$foo = new class() {}; // comment
',
                null,
            ],
            [
                '<?php
$foo = new class() { /* comment */ }; // another comment
',
                null,
            ],
            [
                '<?php
$foo = new class () extends \Exception
{
    protected $message = "Surprise";
};
',
                '<?php
$foo = new class () extends \Exception { protected $message = "Surprise"; };
',
                ['allow_single_line_anonymous_class_with_empty_body' => true],
            ],
        ];
    }

    /**
     * @dataProvider providePreserveLineAfterControlBraceCases
     */
    public function testPreserveLineAfterControlBrace(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function providePreserveLineAfterControlBraceCases()
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
            ],
            [
                "<?php if (true) {\r\n\r\n// CRLF newline\n}",
                null,
            ],
            [
                "<?php if (true) {\r\n\r\n// CRLF newline\n}",
                "<?php if (true){\r\n\r\n// CRLF newline\n}",
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithAllowOnelineLambdaCases
     */
    public function testFixWithAllowSingleLineClosure(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixWithAllowOnelineLambdaCases()
    {
        return [
            [
                '<?php
    $callback = function () {
        return true;
    };',
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
    $callback = function () { if($a){ return true; } return false; };',
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
     * @dataProvider provideDoWhileLoopInsideAnIfWithoutBracketsCases
     */
    public function testDoWhileLoopInsideAnIfWithoutBrackets(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideDoWhileLoopInsideAnIfWithoutBracketsCases()
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
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                '<?php
if (true) {'."\r\n"
                .'    '.'if (true) {'."\r\n"
                .'        '.'echo 1;'."\r\n"
                .'    '.'} elseif (true) {'."\r\n"
                .'        '.'echo 2;'."\r\n"
                .'    '.'} else {'."\r\n"
                .'        '.'echo 3;'."\r\n"
                .'    '.'}'."\r\n"
                .'}',
                '<?php
if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;',
            ],
            [
                '<?php
if (true) {'."\r\n"
                .'    '.'if (true) {'."\r\n"
                .'        '.'echo 1;'."\r\n"
                .'    '.'} elseif (true) {'."\r\n"
                .'        '.'echo 2;'."\r\n"
                .'    '.'} else {'."\r\n"
                .'        '.'echo 3;'."\r\n"
                .'    '.'}'."\r\n"
                .'}',
                '<?php
if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;',
            ],
            [
                '<?php
if (true) {'."\r\n"
                .'    '.'if (true) {'."\r\n"
                .'        '.'echo 1;'."\r\n"
                .'    '.'} elseif (true) {'."\r\n"
                .'        '.'echo 2;'."\r\n"
                .'    '.'} else {'."\r\n"
                .'        '.'echo 3;'."\r\n"
                .'    '.'}'."\r\n"
                .'}',

                '<?php
if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;',
            ],
        ];
    }

    /**
     * @dataProvider provideNowdocInTemplatesCases
     */
    public function testNowdocInTemplates(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideNowdocInTemplatesCases()
    {
        return [
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixCommentsCases
     */
    public function testFixComments(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
        $this->doTest(str_replace('//', '#', $expected), null === $input ? null : str_replace('//', '#', $input));
    }

    public function provideFixCommentsCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
if ($foo) {
    foo();
    '.'
//    bar();
} else {
    bar();
}
',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    public function testDynamicStaticMethodCallNotTouched(): void
    {
        $this->doTest(
            '<?php
SomeClass::{$method}(new \stdClass());
SomeClass::{\'test\'}(new \stdClass());

function example()
{
    SomeClass::{$method}(new \stdClass());
    SomeClass::{\'test\'}(new \stdClass());
}'
        );
    }

    /**
     * @dataProvider provideIndentCommentCases
     */
    public function testIndentComment(string $expected, ?string $input, WhitespacesFixerConfig $config = null): void
    {
        if (null !== $config) {
            $this->fixer->setWhitespacesConfig($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideIndentCommentCases()
    {
        yield [
            '<?php
if (true) {
    $i += 2;
    return foo($i);
    /*
     $i += 3;

     // 1
  '.'
       return foo($i);
     */
}',
            '<?php
if (true) {
    $i += 2;
    return foo($i);
/*
 $i += 3;

 // 1
  '.'
   return foo($i);
 */
}',
            new WhitespacesFixerConfig('    ', "\n"),
        ];

        yield [
            '<?php
class MyClass extends SomeClass
{
    /*	public function myFunction() {

    		$MyItems = [];

    		return $MyItems;
    	}
    */
}',
            '<?php
class MyClass extends SomeClass {
/*	public function myFunction() {

		$MyItems = [];

		return $MyItems;
	}
*/
}',
        ];

        yield [
            '<?php
if (true) {
    $i += 2;
    return foo($i);
    /*
    $i += 3;

    return foo($i);
     */
}',
            '<?php
if (true) {
    $i += 2;
    return foo($i);
/*
$i += 3;

return foo($i);
 */
}',
        ];
    }

    /**
     * @dataProvider provideFixAlternativeSyntaxCases
     */
    public function testFixAlternativeSyntax(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixAlternativeSyntaxCases()
    {
        yield [
            '<?php if (foo()) {
    while (bar()) {
    }
}',
            '<?php if (foo()) while (bar()) {}',
        ];

        yield [
            '<?php if ($a) {
    foreach ($b as $c) {
    }
}',
            '<?php if ($a) foreach ($b as $c) {}',
        ];

        yield [
            '<?php if ($a) foreach ($b as $c): ?> X <?php endforeach; ?>',
        ];

        yield [
            '<?php if ($a) while ($b): ?> X <?php endwhile; ?>',
        ];

        yield [
            '<?php if ($a) for (;;): ?> X <?php endfor; ?>',
        ];

        yield [
            '<?php if ($a) switch ($a): case 1: ?> X <?php endswitch; ?>',
        ];

        yield [
            '<?php if ($a): elseif ($b): for (;;): ?> X <?php endfor; endif; ?>',
        ];

        yield [
            '<?php switch ($a): case 1: for (;;): ?> X <?php endfor; endswitch; ?>,',
        ];

        yield [
            '<?php
if ($a) foreach ($b as $c): ?>
    <?php if ($a) for (;;): ?>
        <?php if ($a) foreach ($b as $c): ?>
            <?php if ($a) for (;;): ?>
                <?php if ($a) while ($b): ?>
                    <?php if ($a) while ($b): ?>
                        <?php if ($a) foreach ($b as $c): ?>
                            <?php if ($a) for (;;): ?>
                                <?php if ($a) while ($b): ?>
                                    <?php if ($a) while ($b): ?>
                                    <?php endwhile; ?>
                                <?php endwhile; ?>
                            <?php endfor; ?>
                        <?php endforeach; ?>
                    <?php endwhile; ?>
                <?php endwhile; ?>
            <?php endfor; ?>
        <?php endforeach; ?>
    <?php endfor; ?>
<?php endforeach; ?>',
        ];
    }

    /**
     * @requires     PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases()
    {
        yield 'match' => [
            '<?php echo match ($x) {
    1, 2 => "Same for 1 and 2",
};',
            '<?php echo match($x)
{
    1, 2 => "Same for 1 and 2",
};',
        ];
    }
}
