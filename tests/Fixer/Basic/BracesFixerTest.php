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

    public static function provideFixControlContinuationBracesCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    $a = function() {
                        $a = 1;
                        while (false);
                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = function() {
                        $a = 1;
                        for ($i=0;$i<5;++$i);
                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function A()
                        {
                            ?>
                            Test<?php echo $foobar; ?>Test
                            <?php
                            $a = 1;
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        $a = 1;
                    } else {
                        $b = 2;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true) {
                        $a = 1;
                    }
                    else {
                        $b = 2;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    try {
                        throw new \Exception();
                    } catch (\LogicException $e) {
                        // do nothing
                    } catch (\Exception $e) {
                        // do nothing
                    }
                EOD,
            <<<'EOD'
                <?php
                    try {
                        throw new \Exception();
                    }catch (\LogicException $e) {
                        // do nothing
                    }
                    catch (\Exception $e) {
                        // do nothing
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    } elseif (true) {
                        echo 2;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    } elseif (true)
                    {
                        echo 2;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    try {
                        echo 1;
                    } catch (Exception $e) {
                        echo 2;
                    }
                EOD,
            <<<'EOD'
                <?php
                    try
                    {
                        echo 1;
                    }
                    catch (Exception $e)
                    {
                        echo 2;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function bar(
                            FooInterface $foo,
                            BarInterface $bar,
                            array $data = []
                        ) {
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function bar(
                            FooInterface $foo,
                            BarInterface $bar,
                            array $data = []
                        ){
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (1) {
                        self::${$key} = $val;
                        self::${$type}[$rule] = $pattern;
                        self::${$type}[$rule] = array_merge($pattern, self::${$type}[$rule]);
                        self::${$type}[$rule] = $pattern + self::${$type}["rules"];
                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    if (1) {
                        do {
                            $a = 1;
                        } while (true);
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if /* 1 */ (2) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if /* 1 */ (2) {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    if (1) {
                                        echo $items{0}->foo;
                                        echo $collection->items{1}->property;
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = function() {
                        $a = 1;
                        while (false);
                    };
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = function()
                    {
                        $a = 1;
                        while (false);
                    };
                EOD,
            <<<'EOD'
                <?php
                    $a = function() {
                        $a = 1;
                        while (false);
                    };
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = function() {
                        $a = 1;
                        for ($i=0;$i<5;++$i);
                    };
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = function()
                    {
                        $a = 1;
                        for ($i=0;$i<5;++$i);
                    };
                EOD,
            <<<'EOD'
                <?php
                    $a = function() {
                        $a = 1;
                        for ($i=0;$i<5;++$i);
                    };
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo {
                        public function A() {
                            ?>
                            Test<?php echo $foobar; ?>Test
                            <?php
                            $a = 1;
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function A()
                        {
                            ?>
                            Test<?php echo $foobar; ?>Test
                            <?php
                            $a = 1;
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        $a = 1;
                    } else {
                        $b = 2;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true) {
                        $a = 1;
                    }
                    else {
                        $b = 2;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true)
                    {
                        $a = 1;
                    }
                    else
                    {
                        $b = 2;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true) {
                        $a = 1;
                    }
                    else {
                        $b = 2;
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    try {
                        throw new \Exception();
                    } catch (\LogicException $e) {
                        // do nothing
                    } catch (\Exception $e) {
                        // do nothing
                    }
                EOD,
            <<<'EOD'
                <?php
                    try {
                        throw new \Exception();
                    }catch (\LogicException $e) {
                        // do nothing
                    }
                    catch (\Exception $e) {
                        // do nothing
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                EOD,
            <<<'EOD'
                <?php
                    try {
                        throw new \Exception();
                    }catch (\LogicException $e) {
                        // do nothing
                    }
                    catch (\Exception $e) {
                        // do nothing
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    } elseif (true) {
                        echo 2;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    } elseif (true)
                    {
                        echo 2;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    try {
                        echo 1;
                    } catch (Exception $e) {
                        echo 2;
                    }
                EOD,
            <<<'EOD'
                <?php
                    try
                    {
                        echo 1;
                    }
                    catch (Exception $e)
                    {
                        echo 2;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo {
                        public function bar(
                            FooInterface $foo,
                            BarInterface $bar,
                            array $data = []
                        ) {
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function bar(
                            FooInterface $foo,
                            BarInterface $bar,
                            array $data = []
                        ){
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (1) {
                        self::${$key} = $val;
                        self::${$type}[$rule] = $pattern;
                        self::${$type}[$rule] = array_merge($pattern, self::${$type}[$rule]);
                        self::${$type}[$rule] = $pattern + self::${$type}["rules"];
                    }
                EOD."\n                ",
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (1) {
                        do {
                            $a = 1;
                        } while (true);
                    }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if /* 1 */ (2) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if /* 1 */ (2) {}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                                    if (1) {
                                        echo $items{0}->foo;
                                        echo $collection->items{1}->property;
                                    }
                EOD."\n                ",
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php class A {
                    /** */
                }
                EOD,
            <<<'EOD'
                <?php class A
                /** */
                {
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    public function foo()
                    {
                        foo();

                        // baz
                        bar();
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    public function foo(){
                    foo();

                    // baz
                    bar();
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    public function foo($foo)
                    {
                        return $foo // foo
                            ? 'foo'
                            : 'bar'
                        ;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                  /**
                   * Foo.
                   */
                  public $foo;

                  /**
                   * Bar.
                   */
                  public $bar;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                  /*
                   * Foo.
                   */
                  public $foo;

                  /*
                   * Bar.
                   */
                  public $bar;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1==1) {
                    $a = 1;
                    // test
                    $b = 2;
                }
                EOD,
            <<<'EOD'
                <?php
                if (1==1) {
                 $a = 1;
                  // test
                  $b = 2;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1==1) {
                    $a = 1;
                    # test
                    $b = 2;
                }
                EOD,
            <<<'EOD'
                <?php
                if (1==1) {
                 $a = 1;
                  # test
                  $b = 2;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1==1) {
                    $a = 1;
                    /** @var int $b */
                    $b = a();
                }
                EOD,
            <<<'EOD'
                <?php
                if (1==1) {
                    $a = 1;
                    /** @var int $b */
                $b = a();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if ($b) {
                        if (1==1) {
                            $a = 1;
                            // test
                            $b = 2;
                        }
                    }

                EOD,
            <<<'EOD'
                <?php
                    if ($b) {
                        if (1==1) {
                         $a = 1;
                          // test
                          $b = 2;
                        }
                    }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if ($b) {
                        if (1==1) {
                            $a = 1;
                            /* test */
                            $b = 2;
                            echo 123;//
                        }
                    }

                EOD,
            <<<'EOD'
                <?php
                    if ($b) {
                        if (1==1) {
                         $a = 1;
                          /* test */
                          $b = 2;
                          echo 123;//
                        }
                    }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class A
                {
                    public function B()
                    {/*
                        */
                        $a = 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class A {
                    public function B()
                    {/*
                        */
                      $a = 1;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class B
                {
                    public function B()
                    {
                        /*
                            *//**/
                        $a = 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class B {
                    public function B()
                    {
                    /*
                        *//**/
                       $a = 1;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class C
                {
                    public function C()
                    {
                        /* */#
                        $a = 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class C {
                    public function C()
                    {
                    /* */#
                       $a = 1;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($a) { /*
                */
                    echo 1;
                }
                EOD,
            <<<'EOD'
                <?php
                if ($a){ /*
                */
                echo 1;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($a) { /**/ /*
                */
                    echo 1;
                    echo 2;
                }
                EOD,
            <<<'EOD'
                <?php
                if ($a){ /**/ /*
                */
                echo 1;
                echo 2;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                foreach ($foo as $bar) {
                    if (true) {
                    }
                    // comment
                    elseif (false) {
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $bar = 1;                   // multiline ...
                                                // ... comment
                    $baz  = 2;                  // next comment
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $foo = 1;

                    // multiline...
                    // ... comment
                    return $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo()
                {
                        $foo = 1;

                        // multiline...
                        // ... comment
                        return $foo;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $bar = 1;     /* bar */     // multiline ...
                                                // ... comment
                    $baz  = 2;    /* baz */     // next comment
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    public function bar()
                    {
                        foreach (new Bar() as $file) {
                            foo();
                        }
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    public function bar() {
                        foreach (new Bar() as $file)
                        {
                            foo();
                        }
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php if ($condition) { ?>
                echo 1;
                <?php } else { ?>
                echo 2;
                <?php } ?>

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php $arr = [true, false]; ?>
                <?php foreach ($arr as $index => $item) {
                    if ($item): ?>
                    <?php echo $index; ?>
                <?php endif;
                } ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                do {
                    foo();
                } // comment
                while (false);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                if (true) {
                    ?>
                <hr />
                    <?php
                    if (true) {
                        echo 'x';
                    }
                    ?>
                <hr />
                    <?php
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                    function foo()
                    {
                    }
                EOD,
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

    public static function provideFixMissingBracesAndIndentCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if (true):
                    $foo = 0;
                endif;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true)  :
                    $foo = 0;
                endif;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) : $foo = 1; endif;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $foo = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true)$foo = 1;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $foo = 2;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true)    $foo = 2;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $foo = 3;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true){$foo = 3;}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    echo 1;
                } else {
                    echo 2;
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) { echo 1; } else echo 2;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    echo 3;
                } else {
                    echo 4;
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) echo 3; else { echo 4; }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    echo 5;
                } else {
                    echo 6;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) echo 5; else echo 6;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    while (true) {
                        $foo = 1;
                        $bar = 2;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) while (true) { $foo = 1; $bar = 2;}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                } else {
                    echo 3;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) if (true) echo 1; else echo 2; else echo 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    // sth here...

                    if ($a && ($b || $c)) {
                        $d = 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                    // sth here...

                    if ($a && ($b || $c)) $d = 1;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                for ($i = 1; $i < 10; ++$i) {
                    echo $i;
                }
                for ($i = 1; $i < 10; ++$i) {
                    echo $i;
                }
                EOD,
            <<<'EOD'
                <?php
                for ($i = 1; $i < 10; ++$i) echo $i;
                for ($i = 1; $i < 10; ++$i) { echo $i; }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                for ($i = 1; $i < 5; ++$i) {
                    for ($i = 1; $i < 10; ++$i) {
                        echo $i;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                for ($i = 1; $i < 5; ++$i) for ($i = 1; $i < 10; ++$i) { echo $i; }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                do {
                    echo 1;
                } while (false);
                EOD,
            <<<'EOD'
                <?php
                do { echo 1; } while (false);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while ($foo->next());
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                foreach ($foo as $bar) {
                    echo $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                foreach ($foo as $bar) echo $bar;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {$a = 1;}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                 $a = 1;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;
                    $b = 2;
                    while (true) {
                        $c = 3;
                    }
                    $d = 4;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                 $a = 1;
                        $b = 2;
                  while (true) {
                            $c = 3;
                                        }
                        $d = 4;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;


                    $b = 2;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    $a = 1;

                    // comment at end
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                foreach ($numbers as $num) {
                    for ($i = 0; $i < $num; ++$i) {
                        $a = "a";
                    }
                    $b = "b";
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    if (2) {
                        $foo = 2;

                        if (3) {
                            $foo = 3;
                        }
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    declare(ticks = 1) {
                        $ticks = 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    declare  (
                    ticks = 1  ) {
                  $ticks = 1;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        foo();
                    } elseif (true) {
                        bar();
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                    {
                        foo();
                    } elseif (true)
                    {
                        bar();
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    while (true) {
                        foo();
                    }
                EOD,
            <<<'EOD'
                <?php
                    while (true)
                    {
                        foo();
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    do {
                        echo $test;
                    } while ($test = $this->getTest());
                EOD,
            <<<'EOD'
                <?php
                    do
                    {
                        echo $test;
                    }
                    while ($test = $this->getTest());
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    do {
                        echo $test;
                    } while ($test = $this->getTest());
                EOD,
            <<<'EOD'
                <?php
                    do
                    {
                        echo $test;
                    }while ($test = $this->getTest());
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class ClassName
                    {
                        /**
                         * comment
                         */
                        public $foo = null;
                    }
                EOD,
            <<<'EOD'
                <?php
                    class ClassName
                    {




                        /**
                         * comment
                         */
                        public $foo = null;


                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    while ($true) {
                        try {
                            throw new \Exception();
                        } catch (\Exception $e) {
                            // do nothing
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    interface Foo
                    {
                        public function setConfig(ConfigInterface $config);
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function bar()
                {
                    $a = 1; //comment
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                function & lambda()
                {
                    return function () {
                    };
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function nested()
                {
                    $a = "a{$b->c()}d";
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = $b->{$c->d}($e);
                    $f->{$g} = $h;
                    $i->{$j}[$k] = $l;
                    $m = $n->{$o};
                    $p = array($q->{$r}, $s->{$t});
                    $u->{$v}->w = 1;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function mixed()
                {
                    $a = $b->{"a{$c}d"}();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function mixedComplex()
                {
                    $a = $b->{"a{$c->{'foo-bar'}()}d"}();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function mixedComplex()
                {
                    $a = ${"b{$foo}"}->{"a{$c->{'foo-bar'}()}d"}();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true):
                        echo 1;
                    else:
                        echo 2;
                    endif;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if ($test) { //foo
                        echo 1;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        // foo
                        // bar
                        if (true) {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                        // foo
                        // bar
                            {
                        if (true)
                        {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        // foo
                        /* bar */
                        if (true) {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                        // foo
                        /* bar */{
                        if (true)
                        {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php if (true) {
                    echo "s";
                } ?>x
                EOD,
            '<?php if (true) echo "s" ?>x',
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } elseif (true) {
                        echo 2;
                    } else {
                        echo 3;
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                if(true)
                    if(true)
                        echo 1;
                    elseif(true)
                        echo 2;
                    else
                        echo 3;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
                if(true)
                    if(true)
                        echo 1;
                    elseif(true)
                        echo 2;
                    else
                        echo 3;
                echo 4;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } elseif (true) {
                        echo 2;
                    } else {
                        echo 3;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                } else {
                    echo 3;
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) if(true) echo 1; else echo 2; else echo 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                foreach ($data as $val) {
                    // test val
                    if ($val === "errors") {
                        echo "!";
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                foreach ($data as $val)
                    // test val
                    if ($val === "errors") {
                        echo "!";
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    foreach ($data as $val) {
                        // test val
                        if ($val === "errors") {
                            echo "!";
                        }
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if (1)
                    foreach ($data as $val)
                        // test val
                        if ($val === "errors") {
                            echo "!";
                        }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function main()
                        {
                            echo "Hello";
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                      public function main()
                      {
                        echo "Hello";
                      }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    public function main()
                    {
                        echo "Hello";
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                  public function main()
                  {
                    echo "Hello";
                  }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public $bar;
                        public $baz;
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                                public $bar;
                                public $baz;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function myFunction($foo, $bar)
                    {
                        return \Foo::{$foo}($bar);
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class C
                    {
                        public function __construct(
                        ) {
                            //comment
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class C {
                        public function __construct(
                        )
                        //comment
                        {}
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true):
                    $foo = 0;
                endif;
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true)  :
                    $foo = 0;
                endif;
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) : $foo = 1; endif;
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $foo = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true)$foo = 1;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $foo = 2;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true)    $foo = 2;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $foo = 3;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true){$foo = 3;}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    echo 1;
                } else {
                    echo 2;
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) { echo 1; } else echo 2;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    echo 3;
                } else {
                    echo 4;
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) echo 3; else { echo 4; }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    echo 5;
                } else {
                    echo 6;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) echo 5; else echo 6;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    while (true) {
                        $foo = 1;
                        $bar = 2;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) while (true) { $foo = 1; $bar = 2;}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                } else {
                    echo 3;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) if (true) echo 1; else echo 2; else echo 3;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    // sth here...

                    if ($a && ($b || $c)) {
                        $d = 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                    // sth here...

                    if ($a && ($b || $c)) $d = 1;
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                for ($i = 1; $i < 10; ++$i) {
                    echo $i;
                }
                for ($i = 1; $i < 10; ++$i) {
                    echo $i;
                }
                EOD,
            <<<'EOD'
                <?php
                for ($i = 1; $i < 10; ++$i) echo $i;
                for ($i = 1; $i < 10; ++$i) { echo $i; }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                for ($i = 1; $i < 5; ++$i) {
                    for ($i = 1; $i < 10; ++$i) {
                        echo $i;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                for ($i = 1; $i < 5; ++$i) for ($i = 1; $i < 10; ++$i) { echo $i; }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                do {
                    echo 1;
                } while (false);
                EOD,
            <<<'EOD'
                <?php
                do { echo 1; } while (false);
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                while ($foo->next());
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                foreach ($foo as $bar) {
                    echo $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                foreach ($foo as $bar) echo $bar;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {$a = 1;}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                 $a = 1;
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;
                    $b = 2;
                    while (true) {
                        $c = 3;
                    }
                    $d = 4;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                 $a = 1;
                        $b = 2;
                  while (true) {
                            $c = 3;
                                        }
                        $d = 4;
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $a = 1;


                    $b = 2;
                }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    $a = 1;

                    // comment at end
                }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
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
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                foreach ($numbers as $num) {
                    for ($i = 0; $i < $num; ++$i) {
                        $a = "a";
                    }
                    $b = "b";
                }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    if (2) {
                        $foo = 2;

                        if (3) {
                            $foo = 3;
                        }
                    }
                }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    declare(ticks = 1) {
                        $ticks = 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    declare  (
                    ticks = 1  ) {
                  $ticks = 1;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        foo();
                    } elseif (true) {
                        bar();
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                    {
                        foo();
                    } elseif (true)
                    {
                        bar();
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    while (true) {
                        foo();
                    }
                EOD,
            <<<'EOD'
                <?php
                    while (true)
                    {
                        foo();
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    do {
                        echo $test;
                    } while ($test = $this->getTest());
                EOD,
            <<<'EOD'
                <?php
                    do
                    {
                        echo $test;
                    }
                    while ($test = $this->getTest());
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    do {
                        echo $test;
                    } while ($test = $this->getTest());
                EOD,
            <<<'EOD'
                <?php
                    do
                    {
                        echo $test;
                    }while ($test = $this->getTest());
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class ClassName {
                        /**
                         * comment
                         */
                        public $foo = null;
                    }
                EOD,
            <<<'EOD'
                <?php
                    class ClassName
                    {




                        /**
                         * comment
                         */
                        public $foo = null;


                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class ClassName {
                        /**
                         * comment
                         */
                        public $foo = null;
                    }
                EOD,
            <<<'EOD'
                <?php
                    class ClassName
                    {




                        /**
                         * comment
                         */
                        public $foo = null;


                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    while ($true) {
                        try {
                            throw new \Exception();
                        } catch (\Exception $e) {
                            // do nothing
                        }
                    }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
                    while ($true) {
                        try {
                            throw new \Exception();
                        } catch (\Exception $e) {
                            // do nothing
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    interface Foo {
                        public function setConfig(ConfigInterface $config);
                    }
                EOD,
            <<<'EOD'
                <?php
                    interface Foo
                    {
                        public function setConfig(ConfigInterface $config);
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    interface Foo {
                        public function setConfig(ConfigInterface $config);
                    }
                EOD,
            <<<'EOD'
                <?php
                    interface Foo
                    {
                        public function setConfig(ConfigInterface $config);
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function bar() {
                    $a = 1; //comment
                }
                EOD,
            <<<'EOD'
                <?php
                function bar()
                {
                    $a = 1; //comment
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php

                function & lambda() {
                    return function () {
                    };
                }
                EOD,
            <<<'EOD'
                <?php

                function & lambda()
                {
                    return function () {
                    };
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php

                function & lambda() {
                    return function ()
                    {
                    };
                }
                EOD,
            <<<'EOD'
                <?php

                function & lambda()
                {
                    return function () {
                    };
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php

                function & lambda() {
                    return function () {
                    };
                }
                EOD,
            <<<'EOD'
                <?php

                function & lambda()
                {
                    return function () {
                    };
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function nested() {
                    $a = "a{$b->c()}d";
                }
                EOD,
            <<<'EOD'
                <?php
                function nested()
                {
                    $a = "a{$b->c()}d";
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function nested() {
                    $a = "a{$b->c()}d";
                }
                EOD,
            <<<'EOD'
                <?php
                function nested()
                {
                    $a = "a{$b->c()}d";
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    $a = $b->{$c->d}($e);
                    $f->{$g} = $h;
                    $i->{$j}[$k] = $l;
                    $m = $n->{$o};
                    $p = array($q->{$r}, $s->{$t});
                    $u->{$v}->w = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = $b->{$c->d}($e);
                    $f->{$g} = $h;
                    $i->{$j}[$k] = $l;
                    $m = $n->{$o};
                    $p = array($q->{$r}, $s->{$t});
                    $u->{$v}->w = 1;
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    $a = $b->{$c->d}($e);
                    $f->{$g} = $h;
                    $i->{$j}[$k] = $l;
                    $m = $n->{$o};
                    $p = array($q->{$r}, $s->{$t});
                    $u->{$v}->w = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = $b->{$c->d}($e);
                    $f->{$g} = $h;
                    $i->{$j}[$k] = $l;
                    $m = $n->{$o};
                    $p = array($q->{$r}, $s->{$t});
                    $u->{$v}->w = 1;
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function mixed() {
                    $a = $b->{"a{$c}d"}();
                }
                EOD,
            <<<'EOD'
                <?php
                function mixed()
                {
                    $a = $b->{"a{$c}d"}();
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function mixedComplex() {
                    $a = $b->{"a{$c->{'foo-bar'}()}d"}();
                }
                EOD,
            <<<'EOD'
                <?php
                function mixedComplex()
                {
                    $a = $b->{"a{$c->{'foo-bar'}()}d"}();
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function mixedComplex() {
                    $a = ${"b{$foo}"}->{"a{$c->{'foo-bar'}()}d"}();
                }
                EOD,
            <<<'EOD'
                <?php
                function mixedComplex()
                {
                    $a = ${"b{$foo}"}->{"a{$c->{'foo-bar'}()}d"}();
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true):
                        echo 1;
                    else:
                        echo 2;
                    endif;

                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true):
                        echo 1;
                    else:
                        echo 2;
                    endif;

                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if ($test) { //foo
                        echo 1;
                    }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        // foo
                        // bar
                        if (true) {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                        // foo
                        // bar
                            {
                        if (true)
                        {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true)
                    // foo
                    // bar
                    {
                        if (true)
                        {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                        // foo
                        // bar
                            {
                        if (true)
                        {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        // foo
                        /* bar */
                        if (true) {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                        // foo
                        /* bar */{
                        if (true)
                        {
                            print("foo");
                            print("bar");
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php if (true) {
                    echo "s";
                } ?>x
                EOD,
            '<?php if (true) echo "s" ?>x',
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } elseif (true) {
                        echo 2;
                    } else {
                        echo 3;
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                if(true)
                    if(true)
                        echo 1;
                    elseif(true)
                        echo 2;
                    else
                        echo 3;

                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
                if(true)
                    if(true)
                        echo 1;
                    elseif(true)
                        echo 2;
                    else
                        echo 3;
                echo 4;

                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } elseif (true) {
                        echo 2;
                    } else {
                        echo 3;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    if (true) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                } else {
                    echo 3;
                }
                EOD,
            <<<'EOD'
                <?php
                if(true) if(true) echo 1; else echo 2; else echo 3;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                foreach ($data as $val) {
                    // test val
                    if ($val === "errors") {
                        echo "!";
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                foreach ($data as $val)
                    // test val
                    if ($val === "errors") {
                        echo "!";
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    foreach ($data as $val) {
                        // test val
                        if ($val === "errors") {
                            echo "!";
                        }
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                if (1)
                    foreach ($data as $val)
                        // test val
                        if ($val === "errors") {
                            echo "!";
                        }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo {
                        public function main() {
                            echo "Hello";
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                      public function main()
                      {
                        echo "Hello";
                      }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo {
                    public function main() {
                        echo "Hello";
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                  public function main()
                  {
                    echo "Hello";
                  }
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo {
                    public function main() {
                        echo "Hello";
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                  public function main()
                  {
                    echo "Hello";
                  }
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo {
                        public $bar;
                        public $baz;
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                                public $bar;
                                public $baz;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    function myFunction($foo, $bar) {
                        return \Foo::{$foo}($bar);
                    }
                EOD,
            <<<'EOD'
                <?php
                    function myFunction($foo, $bar)
                    {
                        return \Foo::{$foo}($bar);
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class C {
                        public function __construct(
                        ) {
                            //comment
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class C {
                        public function __construct(
                        )
                        //comment
                        {}
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
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
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    #[Baz]
                    public function bar()
                    {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                 #[Baz]
                       public function bar()
                 {
                   }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    public function bar($arg1,
                                        $arg2,
                                   $arg3)
                    {
                    }
                }
                EOD,
            null,
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

    public static function provideFixClassyBracesCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    class FooA
                                    {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    class FooA {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class FooB
                                    {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    class FooB{}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class FooC
                                    {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    class FooC
                {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    interface FooD
                                    {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    interface FooD {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                class TestClass extends BaseTestClass implements TestInterface
                                {
                                    private $foo;
                                }
                EOD,
            <<<'EOD'
                <?php
                                class TestClass extends BaseTestClass implements TestInterface { private $foo;}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                abstract class Foo
                {
                    public function getProcess($foo)
                    {
                        return true;
                    }
                }
                EOD,
        ];

        yield [<<<'EOD'
            <?php
            function foo()
            {
                return "$c ($d)";
            }
            EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class FooA {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    class FooA {}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class FooB {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    class FooB{}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class FooC {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    class FooC
                {}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                                    interface FooD {
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    interface FooD {}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                                class TestClass extends BaseTestClass implements TestInterface {
                                    private $foo;
                                }
                EOD,
            <<<'EOD'
                <?php
                                class TestClass extends BaseTestClass implements TestInterface { private $foo;}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                abstract class Foo {
                    public function getProcess($foo) {
                        return true;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                abstract class Foo
                {
                    public function getProcess($foo)
                    {
                        return true;
                    }
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    return "$c ($d)";
                }
                EOD,
            <<<'EOD'
                <?php
                function foo()
                {
                    return "$c ($d)";
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    trait TFoo
                    {
                        public $a;
                    }
                EOD,
            <<<'EOD'
                <?php
                    trait TFoo {public $a;}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    trait TFoo {
                        public $a;
                    }
                EOD,
            <<<'EOD'
                <?php
                    trait TFoo {public $a;}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    trait TFoo
                    {
                        public $a;
                    }
                EOD,
            <<<'EOD'
                <?php
                    trait TFoo {public $a;}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    trait TFoo {
                        public $a;
                    }
                EOD,
            <<<'EOD'
                <?php
                    trait TFoo {public $a;}
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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

    public static function provideFixAnonFunctionInShortArraySyntaxCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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

    public static function provideFixCommentBeforeBraceCases(): iterable
    {
        yield [
            '<?php ',
        ];

        yield [
            <<<'EOD'
                <?php
                    if ($test) { // foo
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if ($test) // foo
                    {
                        echo 1;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $foo = function ($x) use ($y) { // foo
                        echo 1;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $foo = function ($x) use ($y) // foo
                    {
                        echo 1;
                    };
                EOD,
        ];

        yield [
            '<?php ',
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if ($test) { // foo
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if ($test) // foo
                    {
                        echo 1;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $foo = function ($x) use ($y) { // foo
                        echo 1;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $foo = function ($x) use ($y) // foo
                    {
                        echo 1;
                    };
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($a) { //
                    ?><?php ++$a;
                } ?>
                EOD,
            <<<'EOD'
                <?php
                if ($a) { //
                ?><?php ++$a;
                } ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($a) { /* */ /* */ /* */ /* */ /* */
                    ?><?php ++$a;
                } ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $foo = new class ($a) extends Foo implements Bar { // foo
                        private $x;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $foo = new class ($a) extends Foo implements Bar // foo
                    {
                        private $x;
                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $foo = new class ($a) extends Foo implements Bar { // foo
                        private $x;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $foo = new class ($a) extends Foo implements Bar // foo
                    {
                        private $x;
                    };
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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

    public static function provideFixWhitespaceBeforeBraceCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                    {
                        echo 1;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true){
                        echo 1;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)           {
                        echo 1;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    while ($file = $this->getFile()) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    while ($file = $this->getFile())
                    {
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    switch (n) {
                        case label1:
                            echo 1;
                            echo 2;
                            break;
                        default:
                            echo 3;
                            echo 4;
                    }
                EOD,
            <<<'EOD'
                <?php
                    switch (n)
                    {
                        case label1:
                            echo 1;
                            echo 2;
                            break;
                        default:
                            echo 3;
                            echo 4;
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                    {
                        echo 1;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true){
                        echo 1;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)           {
                        echo 1;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    while ($file = $this->getFile()) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    while ($file = $this->getFile())
                    {
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    switch (n) {
                        case label1:
                            echo 1;
                            echo 2;
                            break;
                        default:
                            echo 3;
                            echo 4;
                    }
                EOD,
            <<<'EOD'
                <?php
                    switch (n)
                    {
                        case label1:
                            echo 1;
                            echo 2;
                            break;
                        default:
                            echo 3;
                            echo 4;
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)
                    {
                        echo 1;
                    }
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true){
                        echo 1;
                    }
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                        echo 1;
                    }
                EOD,
            <<<'EOD'
                <?php
                    if (true)           {
                        echo 1;
                    }
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    while ($file = $this->getFile()) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    while ($file = $this->getFile())
                    {
                    }
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    switch (n) {
                        case label1:
                            echo 1;
                            echo 2;
                            break;
                        default:
                            echo 3;
                            echo 4;
                    }
                EOD,
            <<<'EOD'
                <?php
                    switch (n)
                    {
                        case label1:
                            echo 1;
                            echo 2;
                            break;
                        default:
                            echo 3;
                            echo 4;
                    }
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
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

    public static function provideFixFunctionsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    function download()
                    {
                    }
                EOD,
            <<<'EOD'
                <?php
                    function download() {
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    public function AAAA(){
                    }

                    public function BBBB()   {
                    }

                    public function CCCC()
                    {
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    filter(function () {
                        return true;
                    });

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    filter(function   ($a) {
                    });
                EOD,
            <<<'EOD'
                <?php
                    filter(function   ($a)
                    {});
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    filter(function   ($b) {
                    });
                EOD,
            <<<'EOD'
                <?php
                    filter(function   ($b){});
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    foo(array_map(function ($object) use ($x, $y) {
                        return array_filter($object->bar(), function ($o) {
                            return $o->isBaz();
                        });
                    }, $collection));
                EOD,
            <<<'EOD'
                <?php
                    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    public static function bar()
                    {
                        return 1;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    usort($this->fixers, function &($a, $b) use ($selfName) {
                        return 1;
                    });
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    usort(
                        $this->fixers,
                        function &($a, $b) use ($selfName) {
                            return 1;
                        }
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { // random comment
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) // random comment
                    {
                        return 0;
                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { # random comment
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) # random comment
                    {
                        return 0;
                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { /* random comment */
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) /* random comment */
                    {
                        return 0;
                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { /** random comment */
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) /** random comment */
                    {
                        return 0;
                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function download() {
                    }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo {
                    public function AAAA() {
                    }

                    public function BBBB() {
                    }

                    public function CCCC() {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    public function AAAA(){
                    }

                    public function BBBB()   {
                    }

                    public function CCCC()
                    {
                    }
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    filter(function () {
                        return true;
                    });

                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    filter(function   ($a) {
                    });
                EOD,
            <<<'EOD'
                <?php
                    filter(function   ($a)
                    {});
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    filter(function   ($b) {
                    });
                EOD,
            <<<'EOD'
                <?php
                    filter(function   ($b){});
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    foo(array_map(function ($object) use ($x, $y) {
                        return array_filter($object->bar(), function ($o) {
                            return $o->isBaz();
                        });
                    }, $collection));
                EOD,
            <<<'EOD'
                <?php
                    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    foo(array_map(function ($object) use ($x, $y)
                    {
                        return array_filter($object->bar(), function ($o)
                        {
                            return $o->isBaz();
                        });
                    }, $collection));
                EOD,
            <<<'EOD'
                <?php
                    foo(array_map(function ($object) use ($x, $y) { return array_filter($object->bar(), function ($o) { return $o->isBaz(); }); }, $collection));
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo {
                    public static function bar() {
                        return 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    public static function bar()
                    {
                        return 1;
                    }
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo {
                    public static function bar() {
                        return 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    public static function bar()
                    {
                        return 1;
                    }
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo {
                    public static function bar() {
                        return 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    public static function bar()
                    {
                        return 1;
                    }
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    usort($this->fixers, function &($a, $b) use ($selfName) {
                        return 1;
                    });
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    usort(
                        $this->fixers,
                        function &($a, $b) use ($selfName) {
                            return 1;
                        }
                    );
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { // random comment
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) // random comment
                    {
                        return 0;
                    };
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { # random comment
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) # random comment
                    {
                        return 0;
                    };
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { /* random comment */
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) /* random comment */
                    {
                        return 0;
                    };
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) { /** random comment */
                        return 0;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $fnc = function ($a, $b) /** random comment */
                    {
                        return 0;
                    };
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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

    public static function provideFixMultiLineStructuresCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    if (true === true
                        && true === true
                    ) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(true === true
                        && true === true
                    )
                    {
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    foreach (
                        $boo as $bar => $fooBarBazBuzz
                    ) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    foreach (
                        $boo as $bar => $fooBarBazBuzz
                    )
                    {
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $foo = function (
                        $baz,
                        $boo
                    ) {
                    };
                EOD,
            <<<'EOD'
                <?php
                    $foo = function (
                        $baz,
                        $boo
                    )
                    {
                    };
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public static function bar(
                            $baz,
                            $boo
                        ) {
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public static function bar(
                            $baz,
                            $boo
                        )
                        {
                        }
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true === true
                        && true === true
                    ) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(true === true
                        && true === true
                    )
                    {
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if ($foo)
                    {
                    }
                    elseif (
                        true === true
                        && true === true
                    ) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if ($foo)
                    {
                    }
                    elseif (
                        true === true
                        && true === true
                    )
                    {
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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

    public static function provideFixSpaceAroundTokenCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    try {
                        throw new Exception();
                    } catch (Exception $e) {
                        log($e);
                    }
                EOD,
            <<<'EOD'
                <?php
                    try{
                        throw new Exception();
                    }catch (Exception $e){
                        log($e);
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    do {
                        echo 1;
                    } while ($test);
                EOD,
            <<<'EOD'
                <?php
                    do{
                        echo 1;
                    }while($test);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true === true
                        && true === true
                    ) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(true === true
                        && true === true
                    )     {
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (1) {
                    }
                    if ($this->tesT ($test)) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(1){
                    }
                    if ($this->tesT ($test)) {
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                    } elseif (false) {
                    } else {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(true){
                    }elseif(false){
                    }else{
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $foo = function& () use ($bar) {
                    };
                EOD,
            <<<'EOD'
                <?php
                    $foo = function& ()use($bar){};
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                // comment
                declare(strict_types=1);

                // comment
                while (true) {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                declare(ticks   =   1) {
                }
                EOD,
            <<<'EOD'
                <?php
                declare   (   ticks   =   1   )   {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    try {
                        throw new Exception();
                    } catch (Exception $e) {
                        log($e);
                    }
                EOD,
            <<<'EOD'
                <?php
                    try{
                        throw new Exception();
                    }catch (Exception $e){
                        log($e);
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    do {
                        echo 1;
                    } while ($test);
                EOD,
            <<<'EOD'
                <?php
                    do{
                        echo 1;
                    }while($test);
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true === true
                        && true === true
                    ) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(true === true
                        && true === true
                    )     {
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (1) {
                    }
                    if ($this->tesT ($test)) {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(1){
                    }
                    if ($this->tesT ($test)) {
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    if (true) {
                    } elseif (false) {
                    } else {
                    }
                EOD,
            <<<'EOD'
                <?php
                    if(true){
                    }elseif(false){
                    }else{
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $foo = function& () use ($bar) {
                    };
                EOD,
            <<<'EOD'
                <?php
                    $foo = function& ()use($bar){};
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php

                // comment
                declare(strict_types=1);

                // comment
                while (true) {
                }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                declare(ticks   =   1) {
                }
                EOD,
            <<<'EOD'
                <?php
                declare   (   ticks   =   1   )   {
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
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

    public static function provideFinallyCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    try {
                        throw new \Exception();
                    } catch (\LogicException $e) {
                        // do nothing
                    } catch (\Exception $e) {
                        // do nothing
                    } finally {
                        echo "finish!";
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    try {
                        throw new \Exception();
                    } catch (\LogicException $e) {
                        // do nothing
                    } catch (\Exception $e) {
                        // do nothing
                    } finally {
                        echo "finish!";
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                    }
                EOD,
            <<<'EOD'
                <?php
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
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
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

    public static function provideFunctionImportCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    use function Foo\bar;
                    if (true) {
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    use function Foo\bar;
                    if (true) {
                    }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    use function Foo\bar;
                    if (true)
                    {
                    }
                EOD,
            <<<'EOD'
                <?php
                    use function Foo\bar;
                    if (true) {
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    use function Foo\bar;
                    if (true) {
                    }
                EOD,
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo {
                            public function bar()
                            {
                            }
                        };
                    }
                EOD,
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo { public function bar() {} };
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger {
                        public function log($message)
                        {
                            log($message);
                        }
                    }, 3);
                EOD,
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface {
                });
                EOD,
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface{});
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php $message = (new class() {
                });
                EOD,
            '<?php $message = (new class() {});',
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    $message = (new class() extends Foo {
                        public function bar()
                        {
                            echo 1;
                        }
                    });
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                  $message = (new class() extends Foo
                  {
                    public function bar() { echo 1; }
                  });
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function use()
                        {
                        }

                        public function use1(): string
                        {
                        }
                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function use() {
                        }

                        public function use1(): string {
                        }
                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = function (int $foo): string {
                        echo $foo;
                    };

                    $b = function (int $foo) use ($bar): string {
                        echo $foo . $bar;
                    };

                    function a()
                    {
                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
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
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    class Something
                    {
                        public function sth(): string
                        {
                            return function (int $foo) use ($bar): string {
                                return $bar;
                            };
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Something
                    {
                        public function sth(): string
                        {
                            return function (int $foo) use ($bar): string { return $bar; };
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                use function some\a\{
                    test1,
                    test2
                };
                test();
                EOD,
            <<<'EOD'
                <?php
                use function some\a\{
                     test1,
                    test2
                 };
                test();
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                use some\a\{ClassA, ClassB, ClassC as C};
                use function some\a\{fn_a, fn_b, fn_c};
                use const some\a\{ConstA, ConstB, ConstC};

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo($a) {
                        // foo
                        $foo = new class($a) extends Foo {
                            public function bar() {
                            }
                        };
                    }
                EOD,
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo { public function bar() {} };
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo {
                            public function bar()
                            {
                            }
                        };
                    }
                EOD,
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo { public function bar() {} };
                    }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo($a) {
                        // foo
                        $foo = new class($a) extends Foo {
                            public function bar() {
                            }
                        };
                    }
                EOD,
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo { public function bar() {} };
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo
                        {
                            public function bar()
                            {
                            }
                        };
                    }
                EOD,
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo { public function bar() {} };
                    }
                EOD,
            self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo($a) {
                        // foo
                        $foo = new class($a) extends Foo
                        {
                            public function bar() {
                            }
                        };
                    }
                EOD,
            <<<'EOD'
                <?php
                    function foo($a)
                    {
                        // foo
                        $foo = new class($a) extends Foo { public function bar() {} };
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger {
                        public function log($message) {
                            log($message);
                        }
                    }, 3);
                EOD,
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger {
                        public function log($message)
                        {
                            log($message);
                        }
                    }, 3);
                EOD,
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger
                    {
                        public function log($message) {
                            log($message);
                        }
                    }, 3);
                EOD,
            <<<'EOD'
                <?php
                    foo(1, new class implements Logger { public function log($message) { log($message); } }, 3);
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface {
                });
                EOD,
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface{});
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface {
                });
                EOD,
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface{});
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface
                {
                });
                EOD,
            <<<'EOD'
                <?php
                $message = (new class() implements FooInterface{});
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php $message = (new class() {
                });
                EOD,
            '<?php $message = (new class() {});',
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php $message = (new class() {
                });
                EOD,
            '<?php $message = (new class() {});',
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php $message = (new class()
                {
                });
                EOD,
            '<?php $message = (new class() {});',
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    $message = (new class() extends Foo {
                        public function bar() {
                            echo 1;
                        }
                    });
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                  $message = (new class() extends Foo
                  {
                    public function bar() { echo 1; }
                  });
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1)
                {
                    $message = (new class() extends Foo {
                        public function bar()
                        {
                            echo 1;
                        }
                    });
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                  $message = (new class() extends Foo
                  {
                    public function bar() { echo 1; }
                  });
                }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    $message = (new class() extends Foo
                    {
                        public function bar() {
                            echo 1;
                        }
                    });
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                  $message = (new class() extends Foo
                  {
                    public function bar() { echo 1; }
                  });
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1) {
                    $message = (new class() extends Foo
                    {
                        public function bar() {
                            echo 1;
                        }
                    });
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                  $message = (new class() extends Foo
                  {
                    public function bar() { echo 1; }
                  });
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1)
                {
                    $message = (new class() extends Foo
                    {
                        public function bar()
                        {
                            echo 1;
                        }
                    });
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                  $message = (new class() extends Foo
                  {
                    public function bar() { echo 1; }
                  });
                }
                EOD,
            self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (1)
                {
                    $message = (new class() extends Foo
                    {
                        public function bar() {
                            echo 1;
                        }
                    });
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                  $message = (new class() extends Foo
                  {
                    public function bar() { echo 1; }
                  });
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo {
                        public function use() {
                        }

                        public function use1(): string {
                        }
                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function use() {
                        }

                        public function use1(): string {
                        }
                    }
                EOD."\n                ",
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Foo {
                        public function use() {
                        }

                        public function use1(): string {
                        }
                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                    class Foo
                    {
                        public function use() {
                        }

                        public function use1(): string {
                        }
                    }
                EOD."\n                ",
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = function (int $foo): string {
                        echo $foo;
                    };

                    $b = function (int $foo) use ($bar): string {
                        echo $foo . $bar;
                    };

                    function a() {
                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
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
                EOD."\n                ",
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
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
                EOD."\n                ",
            <<<'EOD'
                <?php
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
                EOD."\n                ",
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Something {
                        public function sth(): string {
                            return function (int $foo) use ($bar): string {
                                return $bar;
                            };
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Something
                    {
                        public function sth(): string
                        {
                            return function (int $foo) use ($bar): string { return $bar; };
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                    class Something {
                        public function sth(): string {
                            return function (int $foo) use ($bar): string
                            {
                                return $bar;
                            };
                        }
                    }
                EOD,
            <<<'EOD'
                <?php
                    class Something
                    {
                        public function sth(): string
                        {
                            return function (int $foo) use ($bar): string { return $bar; };
                        }
                    }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                use function some\a\{
                    test1,
                    test2
                };
                test();
                EOD,
            <<<'EOD'
                <?php
                use function some\a\{
                     test1,
                    test2
                 };
                test();
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                use function some\a\{
                    test1,
                    test2
                };
                test();
                EOD,
            <<<'EOD'
                <?php
                use function some\a\{
                     test1,
                    test2
                 };
                test();
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                use function some\a\{
                    test1,
                    test2
                };
                test();
                EOD,
            <<<'EOD'
                <?php
                use function some\a\{
                     test1,
                    test2
                 };
                test();
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                use some\a\{ClassA, ClassB, ClassC as C};
                use function some\a\{fn_a, fn_b, fn_c};
                use const some\a\{ConstA, ConstB, ConstC};

                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                use some\a\{ClassA, ClassB, ClassC as C};
                use function some\a\{fn_a, fn_b, fn_c};
                use const some\a\{ConstA, ConstB, ConstC};

                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                use some\a\{ClassA, ClassB, ClassC as C};
                use function some\a\{fn_a, fn_b, fn_c};
                use const some\a\{ConstA, ConstB, ConstC};

                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_ANONYMOUS_POSITION_NEXT_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = new class () extends \Exception {
                };

                EOD,
            <<<'EOD'
                <?php
                $foo = new class () extends \Exception {};

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = new class () extends \Exception {};

                EOD,
            null,
            ['allow_single_line_anonymous_class_with_empty_body' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = new class() {}; // comment

                EOD,
            null,
            ['allow_single_line_anonymous_class_with_empty_body' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = new class() { /* comment */ }; // another comment

                EOD,
            null,
            ['allow_single_line_anonymous_class_with_empty_body' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = new class () extends \Exception {
                    protected $message = "Surprise";
                };

                EOD,
            <<<'EOD'
                <?php
                $foo = new class () extends \Exception { protected $message = "Surprise"; };

                EOD,
            ['allow_single_line_anonymous_class_with_empty_body' => true],
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

    public static function providePreserveLineAfterControlBraceCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if (1==1) { // test
                    $a = 1;
                }
                echo $a;
                EOD,
            <<<'EOD'
                <?php
                if (1==1) // test
                { $a = 1; }
                echo $a;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
                if ($test) // foo
                 {
                    echo 1;
                }
                if (1 === 1)//a
                {$a = "b"; /*d*/}//c
                echo $a;
                if ($a === 3) /**/
                {echo 1;}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    //  The blank line helps with legibility in nested control structures
                    if (true) {
                        // if body
                    }

                    // if body
                }
                EOD,
        ];

        yield [
            "<?php if (true) {\n    // CRLF newline\n}",
            "<?php if (true) {\r\n\r\n// CRLF newline\n}",
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    //  The blank line helps with legibility in nested control structures
                    if (true) {
                        // if body
                    }

                    // if body
                }
                EOD,
            null,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    //  The blank line helps with legibility in nested control structures
                    if (true) {
                        // if body
                    }

                    // if body
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {

                    //  The blank line helps with legibility in nested control structures
                    if (true) {
                        // if body
                    }

                    // if body
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true)
                {
                    //  The blank line helps with legibility in nested control structures
                    if (true)
                    {
                        // if body
                    }

                    // if body
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {

                    //  The blank line helps with legibility in nested control structures
                    if (true) {
                        // if body
                    }

                    // if body
                }
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];

        yield [
            "<?php if (true) {\n    // CRLF newline\n}",
            "<?php if (true) {\r\n\r\n    // CRLF newline\n}",
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<EOD
                <?php if (true)
                {\n    // CRLF newline\n}
                EOD,
            "<?php if (true){\r\n\r\n// CRLF newline\n}",
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];
    }

    /**
     * @dataProvider provideFixWithAllowSingleLineClosureCases
     */
    public function testFixWithAllowSingleLineClosure(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'allow_single_line_closure' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithAllowSingleLineClosureCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    $callback = function () { return true; };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $callback = function () { if ($a) { return true; } return false; };
                EOD,
            <<<'EOD'
                <?php
                    $callback = function () { if($a){ return true; } return false; };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $callback = function () { if ($a) { return true; } return false; };
                EOD,
            <<<'EOD'
                <?php
                    $callback = function () { if($a) return true; return false; };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $callback = function () {
                        if ($a) {
                            return true;
                        }
                        return false;
                    };
                EOD,
            <<<'EOD'
                <?php
                    $callback = function () { if($a) return true;
                    return false; };
                EOD,
        ];
    }

    /**
     * @dataProvider provideDoWhileLoopInsideAnIfWithoutBracketsCases
     */
    public function testDoWhileLoopInsideAnIfWithoutBrackets(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideDoWhileLoopInsideAnIfWithoutBracketsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if (true) {
                    do {
                        echo 1;
                    } while (false);
                }
                EOD,
            <<<'EOD'
                <?php
                if (true)
                    do {
                        echo 1;
                    } while (false);
                EOD,
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

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if (true) {
                EOD."\r\n"
    ."\t".'if (true) {'."\r\n"
        ."\t\t".'echo 1;'."\r\n"
    ."\t".'} elseif (true) {'."\r\n"
        ."\t\t".'echo 2;'."\r\n"
    ."\t".'} else {'."\r\n"
        ."\t\t".'echo 3;'."\r\n"
    ."\t".'}'."\r\n"
.'}',
            <<<'EOD'
                <?php
                if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                EOD."\r\n"
    ."\t".'if (true) {'."\r\n"
        ."\t\t".'echo 1;'."\r\n"
    ."\t".'} elseif (true) {'."\r\n"
        ."\t\t".'echo 2;'."\r\n"
    ."\t".'} else {'."\r\n"
        ."\t\t".'echo 3;'."\r\n"
    ."\t".'}'."\r\n"
.'}',
            <<<'EOD'
                <?php
                if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true)
                EOD
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
            <<<'EOD'
                <?php
                if(true) if(true) echo 1; elseif(true) echo 2; else echo 3;
                EOD,
            self::CONFIGURATION_OOP_POSITION_SAME_LINE + self::CONFIGURATION_CTRL_STRUCT_POSITION_NEXT_LINE,
        ];
    }

    /**
     * @dataProvider provideNowdocInTemplatesCases
     */
    public function testNowdocInTemplates(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideNowdocInTemplatesCases(): iterable
    {
        yield [
            <<<'EOT'
                <?php
                if (true) {
                    $var = <<<'NOWDOC'
                NOWDOC;
                    ?>
                <?php
                }

                EOT,
            <<<'EOT'
                <?php
                if (true) {
                $var = <<<'NOWDOC'
                NOWDOC;
                ?>
                <?php
                }

                EOT,
        ];

        yield [
            <<<'EOT'
                <?php
                if (true) {
                    $var = <<<HEREDOC
                HEREDOC;
                    ?>
                <?php
                }

                EOT,
            <<<'EOT'
                <?php
                if (true) {
                $var = <<<HEREDOC
                HEREDOC;
                ?>
                <?php
                }

                EOT,
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

    public static function provideFixCommentsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                function test()
                {
                //    $closure = function ($callback) use ($query) {
                //        doSomething();
                //
                //        return true;
                //    };
                    $a = 3;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function test()
                {
                //    $closure = function ($callback) use ($query) {
                //        doSomething();
                //
                EOD.'        '.<<<'EOD'

                //        return true;
                //    };
                    $a = 3;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();

                //    if ($bar === 'bar') {
                //        return [];
                //    }
                } else {
                    bar();
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();

                //    if ($bar === 'bar') {
                    //        return [];
                //    }
                } else {
                    bar();
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();

                //    if ($bar === 'bar') {
                //        return [];
                //    }
                EOD."\n    ".<<<'EOD'

                    $bar = 'bar';
                } else {
                    bar();
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();

                //    bar();
                EOD."\n    ".<<<'EOD'

                    $bar = 'bar';
                } else {
                    bar();
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();
                //    bar();
                EOD."\n    ".<<<'EOD'

                    $bar = 'bar';
                } else {
                    bar();
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();
                EOD."\n    ".<<<'EOD'

                //    bar();
                    $bar = 'bar';
                } else {
                    bar();
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();
                EOD."\n    ".<<<'EOD'

                //    bar();
                } else {
                    bar();
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = 1;
                    // we will return sth
                    return $a;
                }

                EOD,
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = 1;
                // we will return sth
                    return $a;
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = 1;
                EOD."\n    ".<<<'EOD'

                //    bar();
                    // we will return sth
                    return $a;
                }

                EOD,
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = 1;
                EOD."\n    ".<<<'EOD'

                //    bar();
                // we will return sth
                    return $a;
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = 1;
                //    if ($a === 'bar') {
                //        return [];
                //    }
                    // we will return sth
                    return $a;
                }

                EOD,
            <<<'EOD'
                <?php
                function foo()
                {
                    $a = 1;
                //    if ($a === 'bar') {
                //        return [];
                //    }
                // we will return sth
                    return $a;
                }

                EOD,
        ];
    }

    public function testDynamicStaticMethodCallNotTouched(): void
    {
        $this->doTest(
            <<<'EOD'
                <?php
                SomeClass::{$method}(new \stdClass());
                SomeClass::{'test'}(new \stdClass());

                function example()
                {
                    SomeClass::{$method}(new \stdClass());
                    SomeClass::{'test'}(new \stdClass());
                }
                EOD
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

    public static function provideIndentCommentCases(): iterable
    {
        yield [
            <<<EOD
                <?php
                if (true) {
                \t\$i += 2;
                \treturn foo(\$i);
                \t/*
                \t \$i += 3;

                \t // 1
                EOD."\n  ".<<<EOD

                \t   return foo(\$i);
                \t */
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                    $i += 2;
                    return foo($i);
                /*
                 $i += 3;

                 // 1
                EOD."\n  ".<<<'EOD'

                   return foo($i);
                 */
                }
                EOD,
            new WhitespacesFixerConfig("\t", "\n"),
        ];

        yield [
            <<<'EOD'
                <?php
                class MyClass extends SomeClass
                {
                    /*	public function myFunction() {

                    		$MyItems = [];

                    		return $MyItems;
                    	}
                    */
                }
                EOD,
            <<<'EOD'
                <?php
                class MyClass extends SomeClass {
                /*	public function myFunction() {

                		$MyItems = [];

                		return $MyItems;
                	}
                */
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    $i += 2;
                    return foo($i);
                    /*
                    $i += 3;

                    return foo($i);
                     */
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                    $i += 2;
                    return foo($i);
                /*
                $i += 3;

                return foo($i);
                 */
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixAlternativeSyntaxCases
     */
    public function testFixAlternativeSyntax(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixAlternativeSyntaxCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php if (foo()) {
                    while (bar()) {
                    }
                }
                EOD,
            '<?php if (foo()) while (bar()) {}',
        ];

        yield [
            <<<'EOD'
                <?php if ($a) {
                    foreach ($b as $c) {
                    }
                }
                EOD,
            '<?php if ($a) foreach ($b as $c) {}',
        ];

        yield [
            <<<'EOD'
                <?php if ($a) {
                    foreach ($b as $c): ?> X <?php endforeach;
                } ?>
                EOD,
            '<?php if ($a) foreach ($b as $c): ?> X <?php endforeach; ?>',
        ];

        yield [
            <<<'EOD'
                <?php if ($a) {
                    while ($b): ?> X <?php endwhile;
                } ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php if ($a) {
                    for (;;): ?> X <?php endfor;
                } ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php if ($a) {
                    switch ($a): case 1: ?> X <?php endswitch;
                } ?>
                EOD,
        ];

        yield [
            '<?php if ($a): elseif ($b): for (;;): ?> X <?php endfor; endif; ?>',
        ];

        yield [
            '<?php switch ($a): case 1: for (;;): ?> X <?php endfor; endswitch; ?>,',
        ];

        yield [
            <<<'EOD'
                <?php
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
                } ?>
                EOD,
            <<<'EOD'
                <?php
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
                <?php endforeach; ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                switch (n) {
                    case label1:
                        echo 1;
                        echo 2;
                        break;
                    default:
                        echo 3;
                        echo 4;
                }
                EOD,
            <<<'EOD'
                <?php
                switch (n)
                {
                 case label1:
                    echo 1;
                        echo 2;
                        break;
                    default:
                        echo 3;
                        echo 4;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 'bar': if (5) {
                        echo 6;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                switch ($foo)
                {
                case 'bar': if (5) echo 6;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

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
                }
                EOD,
            <<<'EOD'
                <?php

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
                }
                EOD,
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

    public static function provideFix80Cases(): iterable
    {
        yield 'match' => [
            <<<'EOD'
                <?php echo match ($x) {
                    1, 2 => "Same for 1 and 2",
                };
                EOD,
            <<<'EOD'
                <?php echo match($x)
                {
                    1, 2 => "Same for 1 and 2",
                };
                EOD,
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

    public static function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            <<<'EOD'
                <?php
                 enum Foo
                 {
                     case Bar;

                     public function abc()
                     {
                     }
                 }
                EOD,
            <<<'EOD'
                <?php
                 enum Foo {
                     case Bar;

                     public function abc() {
                     }
                 }
                EOD,
        ];

        yield 'backed-enum' => [
            <<<'EOD'
                <?php
                 enum Foo: string
                 {
                     case Bar = "bar";
                 }
                EOD,
            <<<'EOD'
                <?php
                 enum Foo: string {
                 case Bar = "bar";}
                EOD,
        ];
    }
}
