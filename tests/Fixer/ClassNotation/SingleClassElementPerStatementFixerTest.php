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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer
 */
final class SingleClassElementPerStatementFixerTest extends AbstractFixerTestCase
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
        yield from [
            [
                '<?php
class Foo
{
    private static $bar1 = array(1,2,3);
    private static $bar2 = [1,2,3];
    private static $baz1 = array(array(1,2), array(3, 4));
    private static $baz2 = array(1,2,3);
    private static $aaa1 = 1;
    private static $aaa2 = array(2, 2);
    private static $aaa3 = 3;
}',
                '<?php
class Foo
{
    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
    private static $baz1 = array(array(1,2), array(3, 4)), $baz2 = array(1,2,3);
    private static $aaa1 = 1, $aaa2 = array(2, 2), $aaa3 = 3;
}',
            ],
            [
                '<?php
class Foo
{
    const A = 1;
    const B = 2;
}

echo Foo::A, Foo::B;
',
                '<?php
class Foo
{
    const A = 1, B = 2;
}

echo Foo::A, Foo::B;
',
            ],
            [
                <<<'EOT'
<?php

class Foo { protected static $foo = 1; protected static $bar; protected static $baz=2 ; }
EOT
                , <<<'EOT'
<?php

class Foo { protected static $foo = 1,$bar,$baz=2 ; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo {}

class Bar
{
}
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { protected static $foo = 1; protected static $bar; protected static $baz=2 ; }
EOT
                , <<<'EOT'
<?php

class Foo { protected static $foo = 1, $bar,  $baz=2 ; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { const ONE = 1; const TWO = 2; protected static $foo = 1; protected static $bar; protected static $baz=2 ; const THREE = 3; }
EOT
                , <<<'EOT'
<?php

class Foo { const ONE = 1, TWO = 2; protected static $foo = 1, $bar,  $baz=2 ; const THREE = 3; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo {
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;
}
EOT
                , <<<'EOT'
<?php

class Foo {
    protected static $foo = 1,
    $bar,
   $baz=2;
}
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo {
    /**
     * Some great docblock
     */
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;
}
EOT
                , <<<'EOT'
<?php

class Foo {
    /**
     * Some great docblock
     */
    protected static $foo = 1,
    $bar,
   $baz=2;
}
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;
    // this is an inline comment, not a docblock
    private $var = false;
}
EOT
                , <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1,
    $bar,
   $baz=2;
    // this is an inline comment, not a docblock
    private $var = false;
}
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1,
    $bar,
    $baz=2;

    function doSomething()
    {
    }
}
EOT
            ],
            'line_breaks_1' => [
                <<<'EOT'
<?php

class Foo
{

    public $bar = null;
    public $initialized = false;
    public $configured = false;
    public $called = false;
    public $arguments = array();


    public $baz = null;
    public $drop = false;

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{

    public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();


    public $baz = null, $drop = false;

    function doSomething()
    {
    }
}
EOT
            ],
            'line_breaks_2' => [
                <<<'EOT'
<?php

class Foo
{
    const TWO = '2';


    public $bar = null;
    public $initialized = false;

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    const TWO = '2';


    public $bar = null, $initialized = false;

    function doSomething()
    {
    }
}
EOT
            ],
            'line_breaks_3' => [
                <<<'EOT'
<?php

class Foo
{
    const TWO = '2';

    public $bar = null;
    public $initialized = false;


    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    const TWO = '2';

    public $bar = null, $initialized = false;


    function doSomething()
    {
    }
}
EOT
            ],
            'line_breaks_4' => [
                <<<'EOT'
<?php

class Foo
{
    public $one = 1;
    public $bar = null;
    public $initialized = false;
    public $configured = false;
    public $called = false;
    public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    public $one = 1;
    public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();

    function doSomething()
    {
    }
}
EOT
            ],
            'line_breaks_5' => [
                <<<'EOT'
<?php

class Foo
{
    public $one = 1; public $bar = null; public $initialized = false; public $configured = false; public $called = false; public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    public $one = 1; public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();

    function doSomething()
    {
    }
}
EOT
            ],
            'line_breaks_6' => [
                <<<'EOT'
<?php

class Foo
{
    public $one = 1;public $bar = null;public $initialized = false;public $configured = false;public $called = false;public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    public $one = 1;public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();

    function doSomething()
    {
    }
}
EOT
            ],
            'whitespace_1' => [
                <<<'EOT'
<?php

class Foo {    public $one = 1; public $bar = null; public $initialized = false; public $configured = false; public $called = false; public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo {    public $one = 1; public $bar = null,$initialized = false,$configured = false,$called = false,$arguments = array();

    function doSomething()
    {
    }
}
EOT
            ],
            'whitespace_2' => [
                <<<'EOT'
<?php

class Foo {    public $one = 1;  public $bar = null;  public $initialized = false;  public $configured = false;  public $called=false;  public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo {    public $one = 1;  public $bar = null,$initialized = false,$configured = false,$called=false,$arguments = array();

    function doSomething()
    {
    }
}
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { protected static $foo = 1; protected static $bar; protected static $baz=1; }
EOT
                , <<<'EOT'
<?php

class Foo { protected static $foo = 1, $bar, $baz=1; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo {   protected static $foo = 1;   protected static $bar;   protected static $baz=1; }
EOT
                , <<<'EOT'
<?php

class Foo {   protected static $foo = 1, $bar, $baz=1; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { protected $foo = 1; protected $bar; protected $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { protected $foo = 1, $bar, $baz=2; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { var $foo = 1; var $bar; var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar, $baz=2; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { var $foo = 1; var $bar; public function doSomething1() {} var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar; public function doSomething1() {} var $baz=2; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { var $foo = 1; var $bar; public function doSomething2() { global $one, $two, $three; } var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar; public function doSomething2() { global $one, $two, $three; } var $baz=2; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { public function doSomething3() {} protected $foo = 1; protected $bar; protected $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomething3() {} protected $foo = 1, $bar, $baz=2; }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { public function doSomethingElse() {} protected $foo = 1; protected $bar; protected $baz=2; private $acme =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomethingElse() {} protected $foo = 1, $bar, $baz=2; private $acme =array(); }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { public function doSomewhere() {} protected $foo = 1; protected $bar; protected $baz=2; private $acme1 =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomewhere() {} protected $foo = 1, $bar, $baz=2; private $acme1 =array(); }
EOT
            ],
            [
                <<<'EOT'
<?php

class Foo { public function doThis() { global $one1, $two2, $three3; } protected $foo = 1; protected $bar; protected $baz=2; private $acme2 =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doThis() { global $one1, $two2, $three3; } protected $foo = 1, $bar, $baz=2; private $acme2 =array(); }
EOT
            ],
            [
                '<?php
class Foo
{
    const A = 1;
    const #
B#
=#
2#
;#
}

echo Foo::A, Foo::B;
',
                '<?php
class Foo
{
    const A = 1,#
B#
=#
2#
;#
}

echo Foo::A, Foo::B;
',
            ],
            [
                '<?php
                    class Token {
                        const PUBLIC_CONST = 0;
                        private const PRIVATE_CONST = 0;
                        protected const PROTECTED_CONST = 0;
                        public const PUBLIC_CONST_TWO = 0;
                        public const TEST_71 = 0;
                    }
                ',
                '<?php
                    class Token {
                        const PUBLIC_CONST = 0;
                        private const PRIVATE_CONST = 0;
                        protected const PROTECTED_CONST = 0;
                        public const PUBLIC_CONST_TWO = 0, TEST_71 = 0;
                    }
                ',
            ],
        ];

        yield [
            '<?php class Foo {
                private int $foo;
                private int $bar;
            }',
            '<?php class Foo {
                private int $foo, $bar;
            }',
        ];

        yield [
            '<?php class Foo {
                protected ?string $foo;
                protected ?string $bar;
            }',
            '<?php class Foo {
                protected ?string $foo, $bar;
            }',
        ];

        yield [
            '<?php class Foo {
                public ? string $foo;
                public ? string $bar;
            }',
            '<?php class Foo {
                public ? string $foo, $bar;
            }',
        ];

        yield [
            '<?php class Foo {
                var ? Foo\Bar $foo;
                var ? Foo\Bar $bar;
            }',
            '<?php class Foo {
                var ? Foo\Bar $foo, $bar;
            }',
        ];

        yield [
            '<?php class Foo {
                var array $foo;
                var array $bar;
            }',
            '<?php class Foo {
                var array $foo, $bar;
            }',
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideConfigurationCases
     */
    public function testFixWithConfiguration(array $configuration, string $expected): void
    {
        static $input = <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a', OTHER_CONST = 'b';
    protected static $foo = 1, $bar = 2;
}
EOT;

        $this->fixer->configure(['elements' => $configuration]);
        $this->doTest($expected, $input);
    }

    public function provideConfigurationCases(): array
    {
        return [
            [
                ['const', 'property'],
                <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a';
    const OTHER_CONST = 'b';
    protected static $foo = 1;
    protected static $bar = 2;
}
EOT
            ],
            [
                ['const'],
                <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a';
    const OTHER_CONST = 'b';
    protected static $foo = 1, $bar = 2;
}
EOT
            ],
            [
                ['property'],
                <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a', OTHER_CONST = 'b';
    protected static $foo = 1;
    protected static $bar = 2;
}
EOT
            ],
        ];
    }

    public function testWrongConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[single_class_element_per_statement\] Invalid configuration: The option "elements" .*\.$/');

        $this->fixer->configure(['elements' => ['foo']]);
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): iterable
    {
        yield [
            "<?php\r\n\tclass Foo {\r\n\t\tconst AAA=0;\r\n\t\tconst BBB=1;\r\n\t}",
            "<?php\r\n\tclass Foo {\r\n\t\tconst AAA=0, BBB=1;\r\n\t}",
        ];
    }

    public function testAnonymousClassFixing(): void
    {
        $this->doTest(
            '<?php
                $a = new class() {
                    const PUBLIC_CONST_TWO = 0;
                    const TEST_70 = 0;

                    public function a() {
                    }
                };

                class C
                {
                    public function A()
                    {
                        $a = new class() {
                            const PUBLIC_CONST_TWO = 0;
                            const TEST_70 = 0;
                            public function a() {}
                        };
                    }
                }
            ',
            '<?php
                $a = new class() {
                    const PUBLIC_CONST_TWO = 0, TEST_70 = 0;

                    public function a() {
                    }
                };

                class C
                {
                    public function A()
                    {
                        $a = new class() {
                            const PUBLIC_CONST_TWO = 0, TEST_70 = 0;
                            public function a() {}
                        };
                    }
                }
            '
        );
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): iterable
    {
        yield [
            '<?php
class Foo
{
    private string|int $prop1;
    private string|int $prop2;
}
',
            '<?php
class Foo
{
    private string|int $prop1, $prop2;
}
',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php
class Foo
{
    readonly int $a;
    readonly int $b;
    public readonly int $c;
    public readonly int $d;
    readonly private string /*1*/$e;
    readonly private string /*2*/$f;
    readonly float $g;
    protected readonly float $h1;
    protected readonly float $h2;
    readonly float $z1;
    readonly float $z2;
    readonly float $z3;
}',
            '<?php
class Foo
{
    readonly int $a, $b;
    public readonly int $c, $d;
    readonly private string /*1*/$e,/*2*/$f;
    readonly float $g;
    protected readonly float $h1, $h2;
    readonly float $z1, $z2, $z3;
}',
        ];

        yield [
            '<?php
class Foo
{
    final public const B1 = "2";
    final public const B2 = "2";
    readonly float $z2;
}
',
        ];

        yield [
            '<?php
class Foo
{
    private Foo&Bar $prop1;
    private Foo&Bar $prop2;
}
',
            '<?php
class Foo
{
    private Foo&Bar $prop1, $prop2;
}
',
        ];

        yield [
            "<?php

enum Foo: string {
    public const A = 'A';
    public const B = 'B';
    case Hearts = 'H';
    case Spades = 'S';
}

var_dump(Foo::A.Foo::B);",
            "<?php

enum Foo: string {
    public const A = 'A', B = 'B';
    case Hearts = 'H';
    case Spades = 'S';
}

var_dump(Foo::A.Foo::B);",
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix82Cases(): iterable
    {
        yield [
            '<?php trait Foo { public const Bar = 1; public const Baz = 1; }',
            '<?php trait Foo { public const Bar = 1, Baz = 1; }',
        ];
    }
}
