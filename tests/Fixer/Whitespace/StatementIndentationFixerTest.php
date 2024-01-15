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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer
 */
final class StatementIndentationFixerTest extends AbstractFixerTestCase
{
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

    /**
     * @return iterable<array{0: string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'no brace block' => [
            <<<'EOD'
                <?php
                foo();
                bar();
                EOD,
            <<<'EOD'
                <?php
                  foo();
                       bar();
                EOD,
        ];

        yield 'simple' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();
                    bar();
                }
                EOD,
            <<<'EOD'
                <?php
                if ($foo) {
                  foo();
                       bar();
                 }
                EOD,
        ];

        yield 'braces on same line as code' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo();
                    if ($bar) { bar(); }
                }
                EOD,
            <<<'EOD'
                <?php
                if ($foo) {
                 foo();
                       if ($bar) { bar(); }
                  }
                EOD,
        ];

        yield 'with several closing braces on same line' => [
            <<<'EOD'
                <?php
                if ($foo) { foo();
                    if ($bar) { bar();
                        if ($baz) { baz(); }}
                    foo();
                }
                foo();
                EOD,
            <<<'EOD'
                <?php
                if ($foo) { foo();
                 if ($bar) { bar();
                  if ($baz) { baz(); }}
                   foo();
                   }
                  foo();
                EOD,
        ];

        yield 'with meaningful content on closing line' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    foo(); }
                foo();
                EOD,
            <<<'EOD'
                <?php
                if ($foo) {
                    foo(); }
                    foo();
                EOD,
        ];

        // @TODO brace at line 6 should have one level of indentation
        yield 'with several opening braces on same line' => [
            <<<'EOD'
                <?php
                if ($foo) { if ($foo) { foo();
                    if ($bar) { if ($bar) { bar(); }
                        baz();
                    }
                }
                    baz();
                }
                baz();
                EOD,
            <<<'EOD'
                <?php
                if ($foo) { if ($foo) { foo();
                  if ($bar) { if ($bar) { bar(); }
                   baz();
                  }
                  }
                   baz();
                   }
                  baz();
                EOD,
        ];

        yield 'function definition arguments' => [
            <<<'EOD'
                <?php
                function foo(
                    $bar,
                    $baz
                ) {
                }
                EOD,
            <<<'EOD'
                <?php
                   function foo(
                     $bar,
                      $baz
                 ) {
                  }
                EOD,
        ];

        yield 'anonymous function definition arguments' => [
            <<<'EOD'
                <?php
                $foo = function(
                    $bar,
                    $baz
                ) {
                };
                EOD,
            <<<'EOD'
                <?php
                   $foo = function(
                     $bar,
                      $baz
                 ) {
                  };
                EOD,
        ];

        yield 'interface method definition arguments' => [
            <<<'EOD'
                <?php
                interface Foo {
                    public function foo(
                        $bar,
                        $baz
                    );
                }
                EOD,
            <<<'EOD'
                <?php
                interface Foo {
                   public function foo(
                     $bar,
                      $baz
                 );
                 }
                EOD,
        ];

        yield 'class method definition arguments' => [
            <<<'EOD'
                <?php
                class Foo {
                    public function foo(
                        $bar,
                        $baz
                    ) {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                   public function foo(
                     $bar,
                      $baz
                 ) {
                  }
                 }
                EOD,
        ];

        yield 'multiple class methods with many permutations of visibility modifiers' => [
            <<<'EOD'
                <?php
                abstract class Test {
                    final protected function test_final_protected() {}
                    static private function test_static_private() {}
                    private function test_private() {}
                    private static function test_private_static() {}
                    abstract public static function test_abstract_public_static();
                    abstract static public function test_abstract_static_public();
                    abstract public function test_abstract_public();
                    protected abstract function test_protected_abstract();
                    public abstract function test_public_abstract();
                    final static protected function test_final_static_protected() {}
                    final private static function test_final_private_static() {}
                    public final function test_public_final() {}
                    final private function test_final_private() {}
                    static final public function test_static_final_public() {}
                    protected abstract static function test_protected_abstract_static();
                    public static abstract function test_public_static_abstract();
                    protected static abstract function test_protected_static_abstract();
                    static final function test_static_final() {}
                    final static private function test_final_static_private() {}
                    static protected abstract function test_static_protected_abstract();
                    public abstract static function test_public_abstract_static();
                    static final protected function test_static_final_protected() {}
                    final public static function test_final_public_static() {}
                    static final private function test_static_final_private() {}
                    abstract protected function test_abstract_protected();
                    abstract static protected function test_abstract_static_protected();
                    private static final function test_private_static_final() {}
                    final static function test_final_static() {}
                    protected static function test_protected_static() {}
                    protected function test_protected() {}
                    public static function test_public_static() {}
                    final function test_final() {}
                    abstract protected static function test_abstract_protected_static();
                    static protected function test_static_protected() {}
                    static abstract function test_static_abstract();
                    static abstract protected function test_static_abstract_protected();
                    protected final static function test_protected_final_static() {}
                    static public final function test_static_public_final() {}
                    public final static function test_public_final_static() {}
                    abstract static function test_abstract_static();
                    public static final function test_public_static_final() {}
                    static function test_static() {}
                    abstract function test_abstract();
                    static protected final function test_static_protected_final() {}
                    static private final function test_static_private_final() {}
                    private final function test_private_final() {}
                    static public abstract function test_static_public_abstract();
                    protected static final function test_protected_static_final() {}
                    final protected static function test_final_protected_static() {}
                    final static public function test_final_static_public() {}
                    static public function test_static_public() {}
                    function test_() {}
                    static abstract public function test_static_abstract_public();
                    final public function test_final_public() {}
                    private final static function test_private_final_static() {}
                    protected final function test_protected_final() {}
                    public function test_public() {}
                }
                EOD,
            <<<'EOD'
                <?php
                abstract class Test {
                                      final protected function test_final_protected() {}
                                 static private function test_static_private() {}
                                    private function test_private() {}
                             private static function test_private_static() {}
                        abstract public static function test_abstract_public_static();
                                 abstract static public function test_abstract_static_public();
                abstract public function test_abstract_public();
                protected abstract function test_protected_abstract();
                       public abstract function test_public_abstract();
                       final static protected function test_final_static_protected() {}
                                     final private static function test_final_private_static() {}
                           public final function test_public_final() {}
                                      final private function test_final_private() {}
                            static final public function test_static_final_public() {}
                           protected abstract static function test_protected_abstract_static();
                                 public static abstract function test_public_static_abstract();
                                       protected static abstract function test_protected_static_abstract();
                                      static final function test_static_final() {}
                                final static private function test_final_static_private() {}
                             static protected abstract function test_static_protected_abstract();
                 public abstract static function test_public_abstract_static();
                     static final protected function test_static_final_protected() {}
                      final public static function test_final_public_static() {}
                     static final private function test_static_final_private() {}
                  abstract protected function test_abstract_protected();
                      abstract static protected function test_abstract_static_protected();
                                    private static final function test_private_static_final() {}
                               final static function test_final_static() {}
                           protected static function test_protected_static() {}
                        protected function test_protected() {}
                   public static function test_public_static() {}
                         final function test_final() {}
                                   abstract protected static function test_abstract_protected_static();
                     static protected function test_static_protected() {}
                      static abstract function test_static_abstract();
                        static abstract protected function test_static_abstract_protected();
                               protected final static function test_protected_final_static() {}
                static public final function test_static_public_final() {}
                       public final static function test_public_final_static() {}
                                    abstract static function test_abstract_static();
                                    public static final function test_public_static_final() {}
                   static function test_static() {}
                          abstract function test_abstract();
                                      static protected final function test_static_protected_final() {}
                                       static private final function test_static_private_final() {}
                        private final function test_private_final() {}
                                  static public abstract function test_static_public_abstract();
                                     protected static final function test_protected_static_final() {}
                                  final protected static function test_final_protected_static() {}
                               final static public function test_final_static_public() {}
                                  static public function test_static_public() {}
                                    function test_() {}
                                       static abstract public function test_static_abstract_public();
                          final public function test_final_public() {}
                                 private final static function test_private_final_static() {}
                                protected final function test_protected_final() {}
                  public function test_public() {}
                }
                EOD,
        ];

        yield 'trait method definition arguments' => [
            <<<'EOD'
                <?php
                trait Foo {
                    public function foo(
                        $bar,
                        $baz
                    ) {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                trait Foo {
                   public function foo(
                     $bar,
                      $baz
                 ) {
                  }
                 }
                EOD,
        ];

        yield 'function call arguments' => [
            <<<'EOD'
                <?php
                foo(
                    $bar,
                    $baz
                );
                EOD,
            <<<'EOD'
                <?php
                foo(
                  $bar,
                   $baz
                    );
                EOD,
        ];

        yield 'variable function call arguments' => [
            <<<'EOD'
                <?php
                $foo(
                    $bar,
                    $baz
                );
                EOD,
            <<<'EOD'
                <?php
                $foo(
                  $bar,
                   $baz
                    );
                EOD,
        ];

        yield 'chained method calls' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo
                               ->bar()
                                 ->baz()
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo
                                    ->bar()
                                      ->baz()
                                        ;
                      }
                EOD,
        ];

        yield 'nested arrays (long syntax)' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = array(
                             $foo,
                               $bar
                                   ->bar()
                                  ,
                                   array($baz)
                            )
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = array(
                                  $foo,
                                    $bar
                                        ->bar()
                                       ,
                                        array($baz)
                                 )
                                 ;
                      }
                EOD,
        ];

        yield 'nested arrays (short syntax)' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = [
                             $foo,
                               $bar
                                   ->bar()
                                  ,
                                   [$baz]
                            ]
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = [
                                  $foo,
                                    $bar
                                        ->bar()
                                       ,
                                        [$baz]
                                 ]
                                 ;
                      }
                EOD,
        ];

        yield 'array (long syntax) with function call' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = array(
                             foo(
                                 $bar,
                                 $baz
                             )
                             )
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = array(
                                  foo(
                                   $bar,
                                     $baz
                                     )
                                  )
                                 ;
                      }
                EOD,
        ];

        yield 'array (short syntax) with function call' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = [
                             foo(
                                 $bar,
                                 $baz
                             )
                             ]
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = [
                                  foo(
                                   $bar,
                                     $baz
                                     )
                                  ]
                                 ;
                      }
                EOD,
        ];

        yield 'array (long syntax) with class instantiation' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = array(
                             new Foo(
                                 $bar,
                                 $baz
                             )
                             )
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = array(
                                  new Foo(
                                   $bar,
                                     $baz
                                     )
                                  )
                                 ;
                      }
                EOD,
        ];

        yield 'array (short syntax) with class instantiation' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = [
                             new Foo(
                                 $bar,
                                 $baz
                             )
                             ]
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = [
                                  new Foo(
                                   $bar,
                                     $baz
                                     )
                                  ]
                                 ;
                      }
                EOD,
        ];

        yield 'implements list' => [
            <<<'EOD'
                <?php
                class Foo implements
                    Bar,
                    Baz
                {}
                EOD,
            <<<'EOD'
                <?php
                  class Foo implements
                   Bar,
                    Baz
                     {}
                EOD,
        ];

        yield 'extends list' => [
            <<<'EOD'
                <?php
                interface Foo extends
                    Bar,
                    Baz
                {}
                EOD,
            <<<'EOD'
                <?php
                  interface Foo extends
                   Bar,
                    Baz
                     {}
                EOD,
        ];

        yield 'use list' => [
            <<<'EOD'
                <?php
                class Foo {
                    use Bar,
                        Baz;
                }
                EOD,
            <<<'EOD'
                <?php
                  class Foo {
                       use Bar,
                              Baz;
                 }
                EOD,
        ];

        yield 'chained method call with argument' => [
            <<<'EOD'
                <?php
                $foo
                 ->bar(
                     $baz
                 );
                EOD,
            <<<'EOD'
                <?php
                $foo
                 ->bar(
                  $baz
                 );
                EOD,
        ];

        yield 'argument separator on its own line' => [
            <<<'EOD'
                <?php
                foo(
                    1
                    ,
                    2
                );
                EOD,
            <<<'EOD'
                <?php
                foo(
                 1
                ,
                 2
                );
                EOD,
        ];

        yield 'statement end on its own line' => [
            <<<'EOD'
                <?php
                if (true) {
                    $foo =
                         $a
                             && $b
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                  $foo =
                       $a
                           && $b
                             ;
                }
                EOD,
        ];

        yield 'multiline control structure conditions' => [
            <<<'EOD'
                <?php
                if ($a
                       && $b) {
                    foo();
                }
                EOD,
            <<<'EOD'
                <?php
                if ($a
                       && $b) {
                     foo();
                 }
                EOD,
        ];

        yield 'switch' => [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                        echo "foo";
                        break;
                    case 2:
                        echo "bar";
                        break;
                    case 3:
                    default:
                        echo "baz";
                }
                EOD,
            <<<'EOD'
                <?php
                switch ($foo) {
                  case 1:
                     echo "foo";
                  break;
                  case 2:
                     echo "bar";
                 break;
                  case 3:
                   default:
                    echo "baz";
                }
                EOD,
        ];

        yield 'array (long syntax) with anonymous class' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = array(
                             new class (
                                 $bar,
                                 $baz
                             ) {
                                 private $foo;

                                 public function foo(
                                     $foo
                                 ) {
                                     return $foo;
                                 }
                             }
                             )
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = array(
                                  new class (
                                   $bar,
                                     $baz
                                      ) {
                                        private $foo;

                                       public function foo(
                                       $foo
                                        ) {
                                             return $foo;
                                            }
                                     }
                                  )
                                 ;
                      }
                EOD,
        ];

        yield 'array (short syntax) with anonymous class' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    $foo = [
                             new class (
                                 $bar,
                                 $baz
                             ) {
                                 private $foo;

                                 public function foo(
                                     $foo
                                 ) {
                                     return $foo;
                                 }
                             }
                             ]
                    ;
                }
                EOD,
            <<<'EOD'
                <?php
                  if ($foo) {
                         $foo = [
                                  new class (
                                   $bar,
                                     $baz
                                      ) {
                                        private $foo;

                                       public function foo(
                                       $foo
                                        ) {
                                             return $foo;
                                            }
                                     }
                                  ]
                                 ;
                      }
                EOD,
        ];

        yield 'expression function call arguments' => [
            <<<'EOD'
                <?php
                ('foo')(
                    $bar,
                    $baz
                );
                EOD,
            <<<'EOD'
                <?php
                ('foo')(
                  $bar,
                   $baz
                    );
                EOD,
        ];

        yield 'arrow function definition arguments' => [
            <<<'EOD'
                <?php
                $foo = fn(
                    $bar,
                    $baz
                ) => null;
                EOD,
            <<<'EOD'
                <?php
                   $foo = fn(
                     $bar,
                      $baz
                 ) => null;
                EOD,
        ];

        yield 'multiline list in foreach' => [
            <<<'EOD'
                <?php
                foreach ($array as [
                    "foo" => $foo,
                    "bar" => $bar,
                ]) {
                }
                EOD,
        ];

        yield 'switch case with control structure' => [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case true:
                        if ($bar) {
                            bar();
                        }
                        return true;
                }
                EOD,
            <<<'EOD'
                <?php
                switch ($foo) {
                    case true:
                    if ($bar) {
                      bar();
                    }
                return true;
                }
                EOD,
        ];

        yield 'comment in method calls chain' => [
            <<<'EOD'
                <?php
                $foo
                    ->baz()
                    /* ->baz() */
                ;
                EOD,
        ];

        yield 'multiple anonymous functions as function arguments' => [
            <<<'EOD'
                <?php
                foo(function () {
                    bar();
                }, function () {
                    baz();
                });
                EOD,
        ];

        yield 'multiple anonymous functions as method arguments' => [
            <<<'EOD'
                <?php
                $this
                    ->bar(function ($a) {
                        echo $a;
                    }, function ($b) {
                        echo $b;
                    })
                ;
                EOD,
        ];

        yield 'semicolon on a newline inside a switch case without break statement' => [
            <<<'EOD'
                <?php
                switch (true) {
                    case $foo:
                        $foo
                            ->baz()
                        ;
                }
                EOD,
        ];

        yield 'alternative syntax' => [
            <<<'EOD'
                <?php if (1): ?>
                    <div></div>
                <?php else: ?>
                    <?php if (2): ?>
                        <div></div>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>
                <?php endif; ?>

                EOD,
        ];

        yield 'trait import with conflict resolution' => [
            <<<'EOD'
                <?php
                class Foo {
                    use Bar,
                        Baz {
                            Baz::baz insteadof Bar;
                        }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use Bar,
                      Baz {
                       Baz::baz insteadof Bar;
                       }
                }
                EOD,
        ];

        yield 'multiline class definition' => [
            <<<'EOD'
                <?php
                class Foo
                extends
                    BaseFoo
                implements Bar,
                    Baz {
                    public function foo() {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                  extends
                    BaseFoo
                   implements Bar,
                  Baz {
                    public function foo() {
                    }
                }
                EOD,
        ];

        yield 'comment at end of switch case' => [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                        // Nothing to do
                }
                EOD,
        ];

        yield 'comment at end of switch default' => [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                        break;
                    case 2:
                        break;
                    default:
                        // Nothing to do
                }
                EOD,
        ];

        yield 'switch ending with empty case' => [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                }
                EOD,
        ];

        yield 'switch ending with empty default' => [
            <<<'EOD'
                <?php
                switch ($foo) {
                    default:
                }
                EOD,
        ];

        yield 'function ending with a comment and followed by a comma' => [
            <<<'EOD'
                <?php
                foo(function () {
                    bar();
                    // comment
                }, );
                EOD,
        ];

        yield 'multiline arguments starting with "new" keyword' => [
            <<<'EOD'
                <?php
                $result1 = foo(
                    new Bar1(),
                    1
                );
                $result2 = ($function)(
                    new Bar2(),
                    2
                );
                $result3 = (new Argument())(
                    new Bar3(),
                    3
                );
                EOD,
        ];

        yield 'if with only a comment and followed by else' => [
            <<<'EOD'
                <?php
                if (true) {
                    // foo
                } else {
                    // bar
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                // foo
                } else {
                        // bar
                }
                EOD,
        ];

        yield 'comment before else blocks WITHOUT stick_comment_to_next_continuous_control_statement' => [
            <<<'EOD'
                <?php
                // foo
                if ($foo) {
                    echo "foo";
                    // bar
                } else {
                    $aaa = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                        // foo
                if ($foo) {
                    echo "foo";
                        // bar
                } else {
                    $aaa = 1;
                }
                EOD,
            ['stick_comment_to_next_continuous_control_statement' => false],
        ];

        yield 'comment before else blocks WITH stick_comment_to_next_continuous_control_statement' => [
            <<<'EOD'
                <?php
                // foo
                if ($foo) {
                    echo "foo";
                // bar
                } else {
                    $aaa = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                        // foo
                if ($foo) {
                    echo "foo";
                        // bar
                } else {
                    $aaa = 1;
                }
                EOD,
            ['stick_comment_to_next_continuous_control_statement' => true],
        ];

        yield 'multiline comment in block - describing next block' => [
            <<<'EOD'
                <?php
                if (1) {
                    $b = "a";
                // multiline comment line 1
                // multiline comment line 2
                // multiline comment line 3
                } else {
                    $c = "b";
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                    $b = "a";
                    // multiline comment line 1
                    // multiline comment line 2
                    // multiline comment line 3
                } else {
                    $c = "b";
                }
                EOD,
            ['stick_comment_to_next_continuous_control_statement' => true],
        ];

        yield 'multiline comment in block - the only content in block' => [
            <<<'EOD'
                <?php
                if (1) {
                    // multiline comment line 1
                    // multiline comment line 2
                    // multiline comment line 3
                } else {
                    $c = "b";
                }
                EOD,
            <<<'EOD'
                <?php
                if (1) {
                 // multiline comment line 1
                  // multiline comment line 2
                // multiline comment line 3
                } else {
                    $c = "b";
                }
                EOD,
        ];

        yield 'comment before elseif blocks' => [
            <<<'EOD'
                <?php
                // foo
                if ($foo) {
                    echo "foo";
                // bar
                } elseif(1) {
                    echo "bar";
                } elseif(2) {
                    // do nothing
                } elseif(3) {
                    $aaa = 1;
                    // end comment in final block
                }
                EOD,
            <<<'EOD'
                <?php
                    // foo
                if ($foo) {
                    echo "foo";
                    // bar
                } elseif(1) {
                    echo "bar";
                } elseif(2) {
                // do nothing
                } elseif(3) {
                    $aaa = 1;
                    // end comment in final block
                }
                EOD,
            ['stick_comment_to_next_continuous_control_statement' => true],
        ];

        yield 'comments at the end of if/elseif/else blocks' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    echo "foo";
                // foo
                } elseif ($bar) {
                    echo "bar";
                // bar
                } else {
                    echo "baz";
                    // baz
                }
                EOD,
            <<<'EOD'
                <?php
                if ($foo) {
                    echo "foo";
                    // foo
                } elseif ($bar) {
                    echo "bar";
                    // bar
                } else {
                    echo "baz";
                    // baz
                }
                EOD,
            ['stick_comment_to_next_continuous_control_statement' => true],
        ];

        yield 'if-elseif-else without braces' => [
            <<<'EOD'
                <?php
                if ($foo)
                    foo();
                elseif ($bar)
                    bar();
                else
                    baz();
                EOD,
            <<<'EOD'
                <?php
                if ($foo)
                foo();
                elseif ($bar)
                  bar();
                else
                        baz();
                EOD,
        ];

        yield 'for without braces' => [
            <<<'EOD'
                <?php
                for (;;)
                    foo();
                EOD,
            <<<'EOD'
                <?php
                for (;;)
                  foo();
                EOD,
        ];

        yield 'foreach without braces' => [
            <<<'EOD'
                <?php
                foreach ($foo as $bar)
                    foo();
                EOD,
            <<<'EOD'
                <?php
                foreach ($foo as $bar)
                  foo();
                EOD,
        ];

        yield 'while without braces' => [
            <<<'EOD'
                <?php
                while (true)
                    foo();
                EOD,
            <<<'EOD'
                <?php
                while (true)
                  foo();
                EOD,
        ];

        yield 'do-while without braces' => [
            <<<'EOD'
                <?php
                do
                    foo();
                while (true);
                EOD,
            <<<'EOD'
                <?php
                do
                  foo();
                 while (true);
                EOD,
        ];

        yield 'nested control structures without braces' => [
            <<<'EOD'
                <?php
                if (true)
                    if (true)
                        if (true)
                            for ($i = 0; $i < 1; ++$i)
                                echo 1;
                        elseif (true)
                            foreach ([] as $foo)
                                echo 2;
                        else if (true)
                            while (true)
                                echo 3;
                        else
                            do
                                echo 4;
                            while (true);
                    else
                        echo 5;
                EOD,
            <<<'EOD'
                <?php
                if (true)
                if (true)
                 if (true)
                    for ($i = 0; $i < 1; ++$i)
                  echo 1;
                elseif (true)
                  foreach ([] as $foo)
                   echo 2;
                else if (true)
                  while (true)
                   echo 3;
                  else
                  do
                      echo 4;
                      while (true);
                    else
                     echo 5;
                EOD,
        ];

        yield 'mixex if-else with and without braces' => [
            <<<'EOD'
                <?php
                if (true)
                    if (true) {
                        if (true)
                            echo 1;
                        else
                            echo 2;
                    }
                    else {
                        echo 3;
                    }
                else
                    echo 4;
                EOD,
            <<<'EOD'
                <?php
                if (true)
                  if (true) {
                          if (true)
                               echo 1;
                  else
                        echo 2;
                   }
                 else {
                    echo 3;
                 }
                    else
                     echo 4;
                EOD,
        ];

        yield 'empty if and else without braces' => [
            <<<'EOD'
                <?php
                if (true) {
                    if (false);
                    elseif (false);
                    else if (false);
                    else
                        echo 1;
                }
                EOD,
            <<<'EOD'
                <?php
                  if (true) {
                   if (false);
                  elseif (false);
                 else if (false);
                else
                echo 1;
                }
                EOD,
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixWithTabsCases
     */
    public function testFixWithTabs(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t"));
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFixWithTabsCases(): iterable
    {
        yield 'simple' => [
            <<<EOD
                <?php
                if (\$foo) {
                \tfoo();
                \tbar();
                }
                EOD,
            <<<'EOD'
                <?php
                if ($foo) {
                  foo();
                       bar();
                 }
                EOD,
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFixPhp80Cases(): iterable
    {
        yield 'match expression' => [
            <<<'EOD'
                <?php
                return match ($bool) {
                    0 => false,
                    1 => true,
                    default => throw new Exception(),
                };
                EOD,
            <<<'EOD'
                <?php
                return match ($bool) {
                 0 => false,
                      1 => true,
                   default => throw new Exception(),
                };
                EOD,
        ];

        yield 'attribute' => [
            <<<'EOD'
                <?php
                class Foo {
                    #[SimpleAttribute]
                    #[
                        MultilineAttribute
                    ]
                    #[ComplexAttribute(
                        foo: true,
                        bar: [
                                    1,
                                        2,
                                  3,
                         ]
                    )]
                    public function bar()
                    {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                 #[SimpleAttribute]
                 #[
                 MultilineAttribute
                 ]
                #[ComplexAttribute(
                 foo: true,
                    bar: [
                                1,
                                    2,
                              3,
                     ]
                 )]
                  public function bar()
                     {
                     }
                }
                EOD,
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testFixPhp81(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFixPhp81Cases(): iterable
    {
        yield 'simple enum' => [
            <<<'EOD'
                <?php
                enum Color {
                    case Red;
                    case Green;
                    case Blue;
                }
                EOD,
            <<<'EOD'
                <?php
                enum Color {
                 case Red;
                      case Green;
                  case Blue;
                }
                EOD,
        ];

        yield 'backend enum' => [
            <<<'EOD'
                <?php
                enum Color: string {
                    case Red = "R";
                    case Green = "G";
                    case Blue = "B";
                }
                EOD,
            <<<'EOD'
                <?php
                enum Color: string {
                 case Red = "R";
                      case Green = "G";
                  case Blue = "B";
                }
                EOD,
        ];

        yield 'enum with method' => [
            <<<'EOD'
                <?php
                enum Color {
                    case Red;
                    case Green;
                    case Blue;

                    public function foo() {
                        return true;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                enum Color {
                 case Red;
                      case Green;
                  case Blue;

                      public function foo() {
                            return true;
                        }
                }
                EOD,
        ];
    }
}
