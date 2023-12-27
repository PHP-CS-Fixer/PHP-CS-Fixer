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
            '<?php
foo();
bar();',
            '<?php
  foo();
       bar();',
        ];

        yield 'simple' => [
            '<?php
if ($foo) {
    foo();
    bar();
}',
            '<?php
if ($foo) {
  foo();
       bar();
 }',
        ];

        yield 'braces on same line as code' => [
            '<?php
if ($foo) {
    foo();
    if ($bar) { bar(); }
}',
            '<?php
if ($foo) {
 foo();
       if ($bar) { bar(); }
  }',
        ];

        yield 'with several closing braces on same line' => [
            '<?php
if ($foo) { foo();
    if ($bar) { bar();
        if ($baz) { baz(); }}
    foo();
}
foo();',
            '<?php
if ($foo) { foo();
 if ($bar) { bar();
  if ($baz) { baz(); }}
   foo();
   }
  foo();',
        ];

        yield 'with meaningful content on closing line' => [
            '<?php
if ($foo) {
    foo(); }
foo();',
            '<?php
if ($foo) {
    foo(); }
    foo();',
        ];

        // @TODO brace at line 6 should have one level of indentation
        yield 'with several opening braces on same line' => [
            '<?php
if ($foo) { if ($foo) { foo();
    if ($bar) { if ($bar) { bar(); }
        baz();
    }
}
    baz();
}
baz();',
            '<?php
if ($foo) { if ($foo) { foo();
  if ($bar) { if ($bar) { bar(); }
   baz();
  }
  }
   baz();
   }
  baz();',
        ];

        yield 'function definition arguments' => [
            '<?php
function foo(
    $bar,
    $baz
) {
}',
            '<?php
   function foo(
     $bar,
      $baz
 ) {
  }',
        ];

        yield 'anonymous function definition arguments' => [
            '<?php
$foo = function(
    $bar,
    $baz
) {
};',
            '<?php
   $foo = function(
     $bar,
      $baz
 ) {
  };',
        ];

        yield 'interface method definition arguments' => [
            '<?php
interface Foo {
    public function foo(
        $bar,
        $baz
    );
}',
            '<?php
interface Foo {
   public function foo(
     $bar,
      $baz
 );
 }',
        ];

        yield 'class method definition arguments' => [
            '<?php
class Foo {
    public function foo(
        $bar,
        $baz
    ) {
    }
}',
            '<?php
class Foo {
   public function foo(
     $bar,
      $baz
 ) {
  }
 }',
        ];

        yield 'multiple class methods with many permutations of visibility modifiers' => [
            '<?php
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
}',
            '<?php
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
}',
        ];

        yield 'trait method definition arguments' => [
            '<?php
trait Foo {
    public function foo(
        $bar,
        $baz
    ) {
    }
}',
            '<?php
trait Foo {
   public function foo(
     $bar,
      $baz
 ) {
  }
 }',
        ];

        yield 'function call arguments' => [
            '<?php
foo(
    $bar,
    $baz
);',
            '<?php
foo(
  $bar,
   $baz
    );',
        ];

        yield 'variable function call arguments' => [
            '<?php
$foo(
    $bar,
    $baz
);',
            '<?php
$foo(
  $bar,
   $baz
    );',
        ];

        yield 'chained method calls' => [
            '<?php
if ($foo) {
    $foo
               ->bar()
                 ->baz()
    ;
}',
            '<?php
  if ($foo) {
         $foo
                    ->bar()
                      ->baz()
                        ;
      }',
        ];

        yield 'nested arrays (long syntax)' => [
            '<?php
if ($foo) {
    $foo = array(
             $foo,
               $bar
                   ->bar()
                  ,
                   array($baz)
            )
    ;
}',
            '<?php
  if ($foo) {
         $foo = array(
                  $foo,
                    $bar
                        ->bar()
                       ,
                        array($baz)
                 )
                 ;
      }',
        ];

        yield 'nested arrays (short syntax)' => [
            '<?php
if ($foo) {
    $foo = [
             $foo,
               $bar
                   ->bar()
                  ,
                   [$baz]
            ]
    ;
}',
            '<?php
  if ($foo) {
         $foo = [
                  $foo,
                    $bar
                        ->bar()
                       ,
                        [$baz]
                 ]
                 ;
      }',
        ];

        yield 'array (long syntax) with function call' => [
            '<?php
if ($foo) {
    $foo = array(
             foo(
                 $bar,
                 $baz
             )
             )
    ;
}',
            '<?php
  if ($foo) {
         $foo = array(
                  foo(
                   $bar,
                     $baz
                     )
                  )
                 ;
      }',
        ];

        yield 'array (short syntax) with function call' => [
            '<?php
if ($foo) {
    $foo = [
             foo(
                 $bar,
                 $baz
             )
             ]
    ;
}',
            '<?php
  if ($foo) {
         $foo = [
                  foo(
                   $bar,
                     $baz
                     )
                  ]
                 ;
      }',
        ];

        yield 'array (long syntax) with class instantiation' => [
            '<?php
if ($foo) {
    $foo = array(
             new Foo(
                 $bar,
                 $baz
             )
             )
    ;
}',
            '<?php
  if ($foo) {
         $foo = array(
                  new Foo(
                   $bar,
                     $baz
                     )
                  )
                 ;
      }',
        ];

        yield 'array (short syntax) with class instantiation' => [
            '<?php
if ($foo) {
    $foo = [
             new Foo(
                 $bar,
                 $baz
             )
             ]
    ;
}',
            '<?php
  if ($foo) {
         $foo = [
                  new Foo(
                   $bar,
                     $baz
                     )
                  ]
                 ;
      }',
        ];

        yield 'implements list' => [
            '<?php
class Foo implements
    Bar,
    Baz
{}',
            '<?php
  class Foo implements
   Bar,
    Baz
     {}',
        ];

        yield 'extends list' => [
            '<?php
interface Foo extends
    Bar,
    Baz
{}',
            '<?php
  interface Foo extends
   Bar,
    Baz
     {}',
        ];

        yield 'use list' => [
            '<?php
class Foo {
    use Bar,
        Baz;
}',
            '<?php
  class Foo {
       use Bar,
              Baz;
 }',
        ];

        yield 'chained method call with argument' => [
            '<?php
$foo
 ->bar(
     $baz
 );',
            '<?php
$foo
 ->bar(
  $baz
 );',
        ];

        yield 'argument separator on its own line' => [
            '<?php
foo(
    1
    ,
    2
);',
            '<?php
foo(
 1
,
 2
);',
        ];

        yield 'statement end on its own line' => [
            '<?php
if (true) {
    $foo =
         $a
             && $b
    ;
}',
            '<?php
if (true) {
  $foo =
       $a
           && $b
             ;
}',
        ];

        yield 'multiline control structure conditions' => [
            '<?php
if ($a
       && $b) {
    foo();
}',
            '<?php
if ($a
       && $b) {
     foo();
 }',
        ];

        yield 'switch' => [
            '<?php
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
}',
            '<?php
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
}',
        ];

        yield 'array (long syntax) with anonymous class' => [
            '<?php
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
}',
            '<?php
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
      }',
        ];

        yield 'array (short syntax) with anonymous class' => [
            '<?php
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
}',
            '<?php
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
      }',
        ];

        yield 'expression function call arguments' => [
            '<?php
(\'foo\')(
    $bar,
    $baz
);',
            '<?php
(\'foo\')(
  $bar,
   $baz
    );',
        ];

        yield 'arrow function definition arguments' => [
            '<?php
$foo = fn(
    $bar,
    $baz
) => null;',
            '<?php
   $foo = fn(
     $bar,
      $baz
 ) => null;',
        ];

        yield 'multiline list in foreach' => [
            '<?php
foreach ($array as [
    "foo" => $foo,
    "bar" => $bar,
]) {
}',
        ];

        yield 'switch case with control structure' => [
            '<?php
switch ($foo) {
    case true:
        if ($bar) {
            bar();
        }
        return true;
}',
            '<?php
switch ($foo) {
    case true:
    if ($bar) {
      bar();
    }
return true;
}',
        ];

        yield 'comment in method calls chain' => [
            '<?php
$foo
    ->baz()
    /* ->baz() */
;',
        ];

        yield 'multiple anonymous functions as function arguments' => [
            '<?php
foo(function () {
    bar();
}, function () {
    baz();
});',
        ];

        yield 'multiple anonymous functions as method arguments' => [
            '<?php
$this
    ->bar(function ($a) {
        echo $a;
    }, function ($b) {
        echo $b;
    })
;',
        ];

        yield 'semicolon on a newline inside a switch case without break statement' => [
            '<?php
switch (true) {
    case $foo:
        $foo
            ->baz()
        ;
}',
        ];

        yield 'alternative syntax' => [
            '<?php if (1): ?>
    <div></div>
<?php else: ?>
    <?php if (2): ?>
        <div></div>
    <?php else: ?>
        <div></div>
    <?php endif; ?>
<?php endif; ?>
',
        ];

        yield 'trait import with conflict resolution' => [
            '<?php
class Foo {
    use Bar,
        Baz {
            Baz::baz insteadof Bar;
        }
}',
            '<?php
class Foo {
    use Bar,
      Baz {
       Baz::baz insteadof Bar;
       }
}',
        ];

        yield 'multiline class definition' => [
            '<?php
class Foo
extends
    BaseFoo
implements Bar,
    Baz {
    public function foo() {
    }
}',
            '<?php
class Foo
  extends
    BaseFoo
   implements Bar,
  Baz {
    public function foo() {
    }
}',
        ];

        yield 'comment at end of switch case' => [
            '<?php
switch ($foo) {
    case 1:
        // Nothing to do
}',
        ];

        yield 'comment at end of switch default' => [
            '<?php
switch ($foo) {
    case 1:
        break;
    case 2:
        break;
    default:
        // Nothing to do
}',
        ];

        yield 'switch ending with empty case' => [
            '<?php
switch ($foo) {
    case 1:
}',
        ];

        yield 'switch ending with empty default' => [
            '<?php
switch ($foo) {
    default:
}',
        ];

        yield 'function ending with a comment and followed by a comma' => [
            '<?php
foo(function () {
    bar();
    // comment
}, );',
        ];

        yield 'multiline arguments starting with "new" keyword' => [
            '<?php
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
);',
        ];

        yield 'if with only a comment and followed by else' => [
            '<?php
if (true) {
    // foo
} else {
    // bar
}',
            '<?php
if (true) {
// foo
} else {
        // bar
}',
        ];

        yield 'comment before else blocks WITHOUT stick_comment_to_next_continuous_control_statement' => [
            '<?php
// foo
if ($foo) {
    echo "foo";
    // bar
} else {
    $aaa = 1;
}',
            '<?php
        // foo
if ($foo) {
    echo "foo";
        // bar
} else {
    $aaa = 1;
}',
            ['stick_comment_to_next_continuous_control_statement' => false],
        ];

        yield 'comment before else blocks WITH stick_comment_to_next_continuous_control_statement' => [
            '<?php
// foo
if ($foo) {
    echo "foo";
// bar
} else {
    $aaa = 1;
}',
            '<?php
        // foo
if ($foo) {
    echo "foo";
        // bar
} else {
    $aaa = 1;
}',
            ['stick_comment_to_next_continuous_control_statement' => true],
        ];

        yield 'multiline comment in block - describing next block' => [
            '<?php
if (1) {
    $b = "a";
// multiline comment line 1
// multiline comment line 2
// multiline comment line 3
} else {
    $c = "b";
}',
            '<?php
if (1) {
    $b = "a";
    // multiline comment line 1
    // multiline comment line 2
    // multiline comment line 3
} else {
    $c = "b";
}',
            ['stick_comment_to_next_continuous_control_statement' => true],
        ];

        yield 'multiline comment in block - the only content in block' => [
            '<?php
if (1) {
    // multiline comment line 1
    // multiline comment line 2
    // multiline comment line 3
} else {
    $c = "b";
}',
            '<?php
if (1) {
 // multiline comment line 1
  // multiline comment line 2
// multiline comment line 3
} else {
    $c = "b";
}',
        ];

        yield 'comment before elseif blocks' => [
            '<?php
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
}',
            '<?php
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
}',
            ['stick_comment_to_next_continuous_control_statement' => true],
        ];

        yield 'comments at the end of if/elseif/else blocks' => [
            '<?php
if ($foo) {
    echo "foo";
// foo
} elseif ($bar) {
    echo "bar";
// bar
} else {
    echo "baz";
    // baz
}',
            '<?php
if ($foo) {
    echo "foo";
    // foo
} elseif ($bar) {
    echo "bar";
    // bar
} else {
    echo "baz";
    // baz
}',
            ['stick_comment_to_next_continuous_control_statement' => true],
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
            "<?php
if (\$foo) {
\tfoo();
\tbar();
}",
            '<?php
if ($foo) {
  foo();
       bar();
 }',
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
            '<?php
return match ($bool) {
    0 => false,
    1 => true,
    default => throw new Exception(),
};',
            '<?php
return match ($bool) {
 0 => false,
      1 => true,
   default => throw new Exception(),
};',
        ];

        yield 'attribute' => [
            '<?php
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
}',
            '<?php
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
}',
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
            '<?php
enum Color {
    case Red;
    case Green;
    case Blue;
}',
            '<?php
enum Color {
 case Red;
      case Green;
  case Blue;
}',
        ];

        yield 'backend enum' => [
            '<?php
enum Color: string {
    case Red = "R";
    case Green = "G";
    case Blue = "B";
}',
            '<?php
enum Color: string {
 case Red = "R";
      case Green = "G";
  case Blue = "B";
}',
        ];

        yield 'enum with method' => [
            '<?php
enum Color {
    case Red;
    case Green;
    case Blue;

    public function foo() {
        return true;
    }
}',
            '<?php
enum Color {
 case Red;
      case Green;
  case Blue;

      public function foo() {
            return true;
        }
}',
        ];
    }
}
