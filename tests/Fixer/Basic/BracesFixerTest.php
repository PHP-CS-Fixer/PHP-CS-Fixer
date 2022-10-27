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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\Basic\BracesFixer;
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
    private const CONFIGURATION_OOP_POSITION_SAME_LINE = ['position_after_functions_and_oop_constructs' => BracesFixer::LINE_SAME];
    private const CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE = ['position_after_control_structures' => BracesFixer::LINE_NEXT];
    private const CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE = ['position_after_anonymous_constructs' => BracesFixer::LINE_NEXT];

    public function testInvalidConfigurationClassyConstructs(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[braces\] Invalid configuration: The option "position_after_functions_and_oop_constructs" with value "neither" is invalid\. Accepted values are: "next", "same"\.$#');

        $this->fixer->configure(['position_after_functions_and_oop_constructs' => 'neither']);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixControlContinuationBracesCases
     */
    public function testFixControlContinuationBraces(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixControlContinuationBracesCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php
    $a = function() {
        $a = 1;
        for ($i=0;$i<5;++$i);
    };',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    if (1) {
        do {
            $a = 1;
        } while (true);
    }',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    if /* 1 */ (2) {
    }',
                '<?php
    if /* 1 */ (2) {}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
                    if (1) {
                        echo $items{0}->foo;
                        echo $collection->items{1}->property;
                    }
                ',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php class A {
    /** */
}',
                '<?php class A
/** */
{
}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
<?php foreach ($arr as $index => $item) {
    if ($item): ?>
    <?php echo $index; ?>
<?php endif;
} ?>',
            ],
            [
                '<?php
do {
    foo();
} // comment
while (false);
',
            ],
            [
                '<?php

if (true) {
    ?>
<hr />
    <?php
    if (true) {
        echo \'x\';
    }
    ?>
<hr />
    <?php
}',
            ],
            [
                '<?php

    function foo()
    {
    }',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixMissingBracesAndIndentCases
     */
    public function testFixMissingBracesAndIndent(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixMissingBracesAndIndentCases(): iterable
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
        ) {
            //comment
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (true)  :
    $foo = 0;
endif;',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    if (true) : $foo = 1; endif;',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (true) {
    $foo = 1;
}',
                '<?php
if (true)$foo = 1;',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (true) {
    $foo = 2;
}',
                '<?php
if (true)    $foo = 2;',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (true) {
    $foo = 3;
}',
                '<?php
if (true){$foo = 3;}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
do {
    echo 1;
} while (false);',
                '<?php
do { echo 1; } while (false);',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
while ($foo->next());',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
foreach ($foo as $bar) {
    echo $bar;
}',
                '<?php
foreach ($foo as $bar) echo $bar;',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (true) {
    $a = 1;
}',
                '<?php
if (true) {$a = 1;}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (true) {
    $a = 1;


    $b = 2;
}',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (1) {
    $a = 1;

    // comment at end
}',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
if (1) {
    if (2) {
        $a = "a";
    } elseif (3) {
        $b = "b";
    // comment line 1
    // comment line 2
    // comment line 3
    // comment line 4
    } else {
        $c = "c";
    }
    $d = "d";
}',
                '<?php
if (1) {
    if (2) {
        $a = "a";
    } elseif (3) {
        $b = "b";
        // comment line 1
        // comment line 2
// comment line 3
            // comment line 4
    } else {
        $c = "c";
    }
    $d = "d";
}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
            [
                '<?php
    if ($test) { //foo
        echo 1;
    }',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php if (true) {
    echo "s";
} ?>x',
                '<?php if (true) echo "s" ?>x',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    class C {
        public function __construct(
        ) {
            //comment
        }
    }',
                '<?php
    class C {
        public function __construct(
        )
        //comment
        {}
    }',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
class Something { # a
    public function sth() { //
        return function (int $foo) use ($bar) {
            return $bar;
        };
    }
}

function C() { /**/ //    # /**/
}

function D() { /**
*
*/
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
class Foo
{
    #[Baz]
    public function bar()
    {
    }
}',
                '<?php
class Foo
{
 #[Baz]
       public function bar()
 {
   }
}',
            ],
            [
                '<?php
class Foo
{
    public function bar($arg1,
                        $arg2,
                   $arg3)
    {
    }
}',
                null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixClassyBracesCases
     */
    public function testFixClassyBraces(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixClassyBracesCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
                    class FooB {
                    }',
                '<?php
                    class FooB{}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
                    class FooC {
                    }',
                '<?php
                    class FooC
{}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
                    interface FooD {
                    }',
                '<?php
                    interface FooD {}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
                class TestClass extends BaseTestClass implements TestInterface {
                    private $foo;
                }',
                '<?php
                class TestClass extends BaseTestClass implements TestInterface { private $foo;}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixAnonFunctionInShortArraySyntaxCases
     */
    public function testFixAnonFunctionInShortArraySyntax(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixAnonFunctionInShortArraySyntaxCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCommentBeforeBraceCases
     */
    public function testFixCommentBeforeBrace(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixCommentBeforeBraceCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixWhitespaceBeforeBraceCases
     */
    public function testFixWhitespaceBeforeBrace(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixWhitespaceBeforeBraceCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    while ($file = $this->getFile()) {
    }',
                '<?php
    while ($file = $this->getFile())
    {
    }',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php
    while ($file = $this->getFile()) {
    }',
                '<?php
    while ($file = $this->getFile())
    {
    }',
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixFunctionsCases
     */
    public function testFixFunctions(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixFunctionsCases(): iterable
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
    $fnc = function ($a, $b) { /* random comment */
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
    $fnc = function ($a, $b) { /** random comment */
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    filter(function () {
        return true;
    });
',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    filter(function   ($a) {
    });',
                '<?php
    filter(function   ($a)
    {});',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    filter(function   ($b) {
    });',
                '<?php
    filter(function   ($b){});',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php
    usort($this->fixers, function &($a, $b) use ($selfName) {
        return 1;
    });',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    $fnc = function ($a, $b) { /* random comment */
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /* random comment */
    {
        return 0;
    };',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    $fnc = function ($a, $b) { /** random comment */
        return 0;
    };',
                '<?php
    $fnc = function ($a, $b) /** random comment */
    {
        return 0;
    };',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixMultiLineStructuresCases
     */
    public function testFixMultiLineStructures(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixMultiLineStructuresCases(): iterable
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixSpaceAroundTokenCases
     */
    public function testFixSpaceAroundToken(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixSpaceAroundTokenCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
    $foo = function& () use ($bar) {
    };',
                '<?php
    $foo = function& ()use($bar){};',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php

// comment
declare(strict_types=1);

// comment
while (true) {
}',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
declare(ticks   =   1) {
}',
                '<?php
declare   (   ticks   =   1   )   {
}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFinallyCases
     */
    public function testFinally(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFinallyCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFunctionImportCases
     */
    public function testFunctionImport(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFunctionImportCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFix70Cases
     */
    public function testFix70(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFix70Cases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php
$message = (new class() implements FooInterface {
});',
                '<?php
$message = (new class() implements FooInterface{});',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
$message = (new class() implements FooInterface {
});',
                '<?php
$message = (new class() implements FooInterface{});',
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
            [
                '<?php
$message = (new class() implements FooInterface
{
});',
                '<?php
$message = (new class() implements FooInterface{});',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php $message = (new class() {
});',
                '<?php $message = (new class() {});',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php $message = (new class() {
});',
                '<?php $message = (new class() {});',
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
            [
                '<?php $message = (new class()
{
});',
                '<?php $message = (new class() {});',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php
use function some\a\{
    test1,
    test2
};
test();',
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
use function some\a\{
    test1,
    test2
};
test();',
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
            [
                '<?php
use function some\a\{
    test1,
    test2
};
test();',
                '<?php
use function some\a\{
     test1,
    test2
 };
test();',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                null,
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
            ],
            [
                '<?php
$foo = new class () extends \Exception {
};
',
                '<?php
$foo = new class () extends \Exception {};
',
            ],
            [
                '<?php
$foo = new class () extends \Exception {};
',
                null,
                ['allow_single_line_anonymous_class_with_empty_body' => true],
            ],
            [
                '<?php
$foo = new class() {}; // comment
',
                null,
                ['allow_single_line_anonymous_class_with_empty_body' => true],
            ],
            [
                '<?php
$foo = new class() { /* comment */ }; // another comment
',
                null,
                ['allow_single_line_anonymous_class_with_empty_body' => true],
            ],
            [
                '<?php
$foo = new class () extends \Exception {
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
     * @param array<string, mixed> $configuration
     *
     * @dataProvider providePreserveLineAfterControlBraceCases
     */
    public function testPreserveLineAfterControlBrace(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function providePreserveLineAfterControlBraceCases(): iterable
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
if ($a === 3) { /**/
    echo 1;
}
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
                "<?php if (true) {\n    // CRLF newline\n}",
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
if (true) {

    //  The blank line helps with legibility in nested control structures
    if (true) {
        // if body
    }

    // if body
}',
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
            [
                "<?php if (true) {\n    // CRLF newline\n}",
                "<?php if (true) {\r\n\r\n    // CRLF newline\n}",
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
            ],
            [
                "<?php if (true)
{\n    // CRLF newline\n}",
                "<?php if (true){\r\n\r\n// CRLF newline\n}",
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithAllowOnelineLambdaCases
     */
    public function testFixWithAllowSingleLineClosure(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'allow_single_line_closure' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAllowOnelineLambdaCases(): iterable
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
     * @dataProvider provideDoWhileLoopInsideAnIfWithoutBracketsCases
     */
    public function testDoWhileLoopInsideAnIfWithoutBrackets(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideDoWhileLoopInsideAnIfWithoutBracketsCases(): iterable
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
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): iterable
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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
                self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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

    public function provideNowdocInTemplatesCases(): iterable
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

    public function provideFixCommentsCases(): iterable
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

    public function provideIndentCommentCases(): iterable
    {
        yield [
            "<?php
if (true) {
\t\$i += 2;
\treturn foo(\$i);
\t/*
\t \$i += 3;

\t // 1
  "."
\t   return foo(\$i);
\t */
}",
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
            new WhitespacesFixerConfig("\t", "\n"),
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

    public function provideFixAlternativeSyntaxCases(): iterable
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
            '<?php if ($a) {
    foreach ($b as $c): ?> X <?php endforeach;
} ?>',
            '<?php if ($a) foreach ($b as $c): ?> X <?php endforeach; ?>',
        ];

        yield [
            '<?php if ($a) {
    while ($b): ?> X <?php endwhile;
} ?>',
        ];

        yield [
            '<?php if ($a) {
    for (;;): ?> X <?php endfor;
} ?>',
        ];

        yield [
            '<?php if ($a) {
    switch ($a): case 1: ?> X <?php endswitch;
} ?>',
        ];

        yield [
            '<?php if ($a): elseif ($b): for (;;): ?> X <?php endfor; endif; ?>',
        ];

        yield [
            '<?php switch ($a): case 1: for (;;): ?> X <?php endfor; endswitch; ?>,',
        ];

        yield [
            '<?php
if ($a) {
    foreach ($b as $c): ?>
    <?php if ($a) {
        for (;;): ?>
        <?php if ($a) {
            foreach ($b as $c): ?>
            <?php if ($a) {
                for (;;): ?>
                <?php if ($a) {
                    while ($b): ?>
                    <?php if ($a) {
                        while ($b): ?>
                        <?php if ($a) {
                            foreach ($b as $c): ?>
                            <?php if ($a) {
                                for (;;): ?>
                                <?php if ($a) {
                                    while ($b): ?>
                                    <?php if ($a) {
                                        while ($b): ?>
                                    <?php endwhile;
                                    } ?>
                                <?php endwhile;
                                } ?>
                            <?php endfor;
                            } ?>
                        <?php endforeach;
                        } ?>
                    <?php endwhile;
                    } ?>
                <?php endwhile;
                } ?>
            <?php endfor;
            } ?>
        <?php endforeach;
        } ?>
    <?php endfor;
    } ?>
<?php endforeach;
} ?>',
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

        yield [
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
        ];

        yield [
            '<?php
switch ($foo) {
    case \'bar\': if (5) {
        echo 6;
    }
}',
            '<?php
switch ($foo)
{
case \'bar\': if (5) echo 6;
}',
        ];

        yield [
            '<?php

class mySillyClass
{
    public function mrMethod()
    {
        switch ($i) {
            case 0:
                echo "i equals 0";
                break;
            case 1:
                echo "i equals 1";
                break;
            case 2:
                echo "i equals 2";
                break;
        }
    }
}',
            '<?php

class mySillyClass
{
public function mrMethod() {
switch ($i) {
case 0:
echo "i equals 0";
break;
case 1:
echo "i equals 1";
break;
case 2:
echo "i equals 2";
break;
}
}
}',
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): iterable
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

    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            '<?php
 enum Foo
 {
     case Bar;

     public function abc()
     {
     }
 }',
            '<?php
 enum Foo {
     case Bar;

     public function abc() {
     }
 }',
        ];

        yield 'backed-enum' => [
            '<?php
 enum Foo: string
 {
     case Bar = "bar";
 }',
            '<?php
 enum Foo: string {
 case Bar = "bar";}',
        ];
    }
}
