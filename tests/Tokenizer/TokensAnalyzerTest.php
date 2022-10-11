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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Max Voloshin <voloshin.dp@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\TokensAnalyzer
 */
final class TokensAnalyzerTest extends TestCase
{
    /**
     * @param array<int, array{classIndex: int, type: string}> $expectedElements
     *
     * @dataProvider provideGetClassyElementsCases
     */
    public function testGetClassyElements(array $expectedElements, string $source): void
    {
        $tokens = Tokens::fromCode($source);

        array_walk(
            $expectedElements,
            static function (array &$element, $index) use ($tokens): void {
                $element['token'] = $tokens[$index];
                ksort($element);
            }
        );

        $tokensAnalyzer = new TokensAnalyzer($tokens);

        static::assertSame(
            $expectedElements,
            $tokensAnalyzer->getClassyElements()
        );
    }

    public function provideGetClassyElementsCases(): iterable
    {
        yield 'trait import' => [
            [
                10 => [
                    'classIndex' => 4,
                    'type' => 'trait_import',
                ],
                19 => [
                    'classIndex' => 4,
                    'type' => 'trait_import',
                ],
                24 => [
                    'classIndex' => 4,
                    'type' => 'const',
                ],
                35 => [
                    'classIndex' => 4,
                    'type' => 'method',
                ],
                55 => [
                    'classIndex' => 49,
                    'type' => 'trait_import',
                ],
                64 => [
                    'classIndex' => 49,
                    'type' => 'method',
                ],
            ],
            '<?php
            /**  */
            class Foo
            {
                use A\B;
                //
                use Foo;

                const A = 1;

                public function foo()
                {
                    $a = new class()
                    {
                        use Z; // nested trait import

                        public function bar()
                        {
                            echo 123;
                        }
                    };

                    $a->bar();
                }
            }',
        ];

        yield [
            [
                9 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                14 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                19 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                28 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                42 => [
                    'classIndex' => 1,
                    'type' => 'const',
                ],
                53 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                83 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                140 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                164 => [
                    'classIndex' => 158,
                    'type' => 'const',
                ],
                173 => [
                    'classIndex' => 158,
                    'type' => 'trait_import',
                ],
            ],
            <<<'PHP'
<?php
class Foo
{
    public $prop0;
    protected $prop1;
    private $prop2 = 1;
    var $prop3 = array(1,2,3);
    const CONSTANT = 'constant value';

    public function bar4()
    {
        $a = 5;

        return " ({$a})";
    }
    public function bar5($data)
    {
        $message = $data;
        $example = function ($arg) use ($message) {
            echo $arg . ' ' . $message;
        };
        $example('hello');
    }function A(){}
}

function test(){}

class Foo2
{
    const CONSTANT = 'constant value';

    use Foo\Bar; // expected in the return value
}

PHP
            ,
        ];
    }

    public function testGetClassyElementsWithNullableProperties(): void
    {
        $source = <<<'PHP'
<?php
class Foo
{
    public int $prop0;
    protected ?array $prop1;
    private string $prop2 = 1;
    var ? Foo\Bar $prop3 = array(1,2,3);
}

PHP;
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        static::assertSame(
            [
                11 => [
                    'classIndex' => 1,
                    'token' => $tokens[11],
                    'type' => 'property',
                ],
                19 => [
                    'classIndex' => 1,
                    'token' => $tokens[19],
                    'type' => 'property',
                ],
                26 => [
                    'classIndex' => 1,
                    'token' => $tokens[26],
                    'type' => 'property',
                ],
                41 => [
                    'classIndex' => 1,
                    'token' => $tokens[41],
                    'type' => 'property',
                ],
            ],
            $elements
        );
    }

    public function testGetClassyElementsWithAnonymousClass(): void
    {
        $source = <<<'PHP'
<?php
class A {
    public $A;

    private function B()
    {
        return new class(){
            protected $level1;
            private function XYZ() {
                return new class(){private $level2 = 1;};
            }
        };
    }

    private function C() {
    }
}

function B() {} // do not count this
PHP;
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        static::assertSame(
            [
                9 => [
                    'classIndex' => 1,
                    'token' => $tokens[9],
                    'type' => 'property', // $A
                ],
                14 => [
                    'classIndex' => 1,
                    'token' => $tokens[14],
                    'type' => 'method', // B
                ],
                33 => [
                    'classIndex' => 26,
                    'token' => $tokens[33],
                    'type' => 'property', // $level1
                ],
                38 => [
                    'classIndex' => 26,
                    'token' => $tokens[38],
                    'type' => 'method', // XYZ
                ],
                56 => [
                    'classIndex' => 50,
                    'token' => $tokens[56],
                    'type' => 'property', // $level2
                ],
                74 => [
                    'classIndex' => 1,
                    'token' => $tokens[74],
                    'type' => 'method', // C
                ],
            ],
            $elements
        );
    }

    public function testGetClassyElementsWithMultipleAnonymousClass(): void
    {
        $source = <<<'PHP'
<?php class A0
{
    public function AA0()
    {
        return new class
        {
            public function BB0()
            {
            }
        };
    }

    public function otherFunction0()
    {
    }
}

class A1
{
    public function AA1()
    {
        return new class
        {
            public function BB1()
            {
                return new class
                {
                    public function CC1()
                    {
                        return new class
                        {
                            public function DD1()
                            {
                                return new class{};
                            }

                            public function DD2()
                            {
                                return new class{};
                            }
                        };
                    }
                };
            }

            public function BB2()
            {
                return new class
                {
                    public function CC2()
                    {
                        return new class
                        {
                            public function DD2()
                            {
                                return new class{};
                            }
                        };
                    }
                };
            }
        };
    }

    public function otherFunction1()
    {
    }
}
PHP;
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        static::assertSame(
            [
                9 => [
                    'classIndex' => 1,
                    'token' => $tokens[9],
                    'type' => 'method',
                ],
                27 => [
                    'classIndex' => 21,
                    'token' => $tokens[27],
                    'type' => 'method',
                ],
                44 => [
                    'classIndex' => 1,
                    'token' => $tokens[44],
                    'type' => 'method',
                ],
                64 => [
                    'classIndex' => 56,
                    'token' => $tokens[64],
                    'type' => 'method',
                ],
                82 => [
                    'classIndex' => 76,
                    'token' => $tokens[82],
                    'type' => 'method',
                ],
                100 => [
                    'classIndex' => 94,
                    'token' => $tokens[100],
                    'type' => 'method',
                ],
                118 => [
                    'classIndex' => 112,
                    'token' => $tokens[118],
                    'type' => 'method',
                ],
                139 => [
                    'classIndex' => 112,
                    'token' => $tokens[139],
                    'type' => 'method',
                ],
                170 => [
                    'classIndex' => 76,
                    'token' => $tokens[170],
                    'type' => 'method',
                ],
                188 => [
                    'classIndex' => 182,
                    'token' => $tokens[188],
                    'type' => 'method',
                ],
                206 => [
                    'classIndex' => 200,
                    'token' => $tokens[206],
                    'type' => 'method',
                ],
                242 => [
                    'classIndex' => 56,
                    'token' => $tokens[242],
                    'type' => 'method',
                ],
            ],
            $elements
        );
    }

    public function testGetClassyElements74(): void
    {
        $source = <<<'PHP'
<?php
class Foo
{
    public int $bar = 3;

    protected ?string $baz;

    private ?string $bazNull = null;

    public static iterable $staticProp;

    public float $x, $y;

    var bool $flag1;

    var ?bool $flag2;
}

PHP;
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();
        $expected = [];

        foreach ([11, 23, 31, 44, 51, 54, 61, 69] as $index) {
            $expected[$index] = [
                'classIndex' => 1,
                'token' => $tokens[$index],
                'type' => 'property',
            ];
        }

        static::assertSame($expected, $elements);
    }

    /**
     * @param array<int, array{classIndex: int, type: string}> $expected
     *
     * @dataProvider provideGetClassyElements81Cases
     *
     * @requires PHP 8.1
     */
    public function testGetClassyElements81(array $expected, string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        array_walk(
            $expected,
            static function (array &$element, $index) use ($tokens): void {
                $element['token'] = $tokens[$index];
                ksort($element);
            }
        );

        static::assertSame($expected, $elements);
    }

    public function provideGetClassyElements81Cases(): iterable
    {
        yield [
            [
                11 => [
                    'classIndex' => 1,
                    'type' => 'property', // $prop1
                ],
                20 => [
                    'classIndex' => 1,
                    'type' => 'property', // $prop2
                ],
                29 => [
                    'classIndex' => 1,
                    'type' => 'property', // $prop13
                ],
            ],
            '<?php
class Foo
{
    readonly string $prop1;
    readonly public string $prop2;
    public readonly string $prop3;
}
            ',
        ];

        yield 'final const' => [
            [
                11 => [
                    'classIndex' => 1,
                    'type' => 'const', // A
                ],
                24 => [
                    'classIndex' => 1,
                    'type' => 'const', // B
                ],
            ],
            '<?php
class Foo
{
    final public const A = "1";
    public final const B = "2";
}
            ',
        ];

        yield 'enum final const' => [
            [
                11 => [
                    'classIndex' => 1,
                    'type' => 'const', // A
                ],
                24 => [
                    'classIndex' => 1,
                    'type' => 'const', // B
                ],
            ],
            '<?php
enum Foo
{
    final public const A = "1";
    public final const B = "2";
}
            ',
        ];

        yield 'enum case' => [
            [
                12 => [
                    'classIndex' => 1,
                    'type' => 'const', // Spades
                ],
                21 => [
                    'classIndex' => 1,
                    'type' => 'case', // Hearts
                ],
                32 => [
                    'classIndex' => 1,
                    'type' => 'method', // function tests
                ],
                81 => [
                    'classIndex' => 75,
                    'type' => 'method', // function bar123
                ],
                135 => [
                    'classIndex' => 127,
                    'type' => 'method', // function bar7
                ],
            ],
            '<?php
enum Foo: string
{
    private const Spades = 123;

    case Hearts = "H";

    private function test($foo) {
        switch ($foo) {
            case 1:
            // case 2
            case 3:
                echo 123;
            break;
        }

        return new class {
            public function bar123($foo) {
                switch ($foo) {
                    case 1:
                    // case 2
                    case 3:
                        echo 123;
                };
            }
        };
    }
}

class Bar {
    public function bar7($foo) {
        switch ($foo) {
            case 1:
            // case 2
            case 3:
                echo 123;
        };
    }
}
',
        ];

        yield 'enum' => [
            [
                10 => [
                    'classIndex' => 1,
                    'type' => 'case',
                ],
                19 => [
                    'classIndex' => 1,
                    'type' => 'case',
                ],
                28 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
            ],
            '<?php
enum Foo: string
{
    case Bar = "bar";
    case Baz = "baz";
    function qux() {
        switch (true) {
            case "x": break;
        }
    }
}
            ',
        ];
    }

    /**
     * @param array<int, array{classIndex: int, type: string}> $expected
     *
     * @dataProvider provideGetClassyElements82Cases
     *
     * @requires PHP 8.2
     */
    public function testGetClassyElements82(array $expected, string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        array_walk(
            $expected,
            static function (array &$element, $index) use ($tokens): void {
                $element['token'] = $tokens[$index];
                ksort($element);
            },
        );

        static::assertSame($expected, $elements);
    }

    public function provideGetClassyElements82Cases(): iterable
    {
        yield 'constant in trait' => [
            [
                7 => [
                    'classIndex' => 1,
                    'type' => 'const',
                ],
                18 => [
                    'classIndex' => 1,
                    'type' => 'const',
                ],
            ],
            <<<'PHP'
                <?php
                trait Foo
                {
                    const BAR = 0;
                    final const BAZ = 1;
                }
                PHP,
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsAnonymousClassCases
     */
    public function testIsAnonymousClass(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isAnonymousClass($index));
        }
    }

    public function provideIsAnonymousClassCases(): iterable
    {
        yield [
            [1 => false],
            '<?php class foo {}',
        ];

        yield [
            [7 => true],
            '<?php $foo = new class() {};',
        ];

        yield [
            [7 => true],
            '<?php $foo = new class() extends Foo implements Bar, Baz {};',
        ];

        yield [
            [1 => false, 19 => true],
            '<?php class Foo { function bar() { return new class() {}; } }',
        ];

        yield [
            [7 => true, 11 => true],
            '<?php $a = new class(new class($d->a) implements B{}) extends C{};',
        ];

        yield [
            [1 => false],
            '<?php interface foo {}',
        ];

        if (\PHP_VERSION_ID >= 80000) {
            yield [
                [11 => true],
                '<?php $object = new #[ExampleAttribute] class(){};',
            ];

            yield [
                [27 => true],
                '<?php $object = new #[A] #[B] #[C]#[D]/* */ /** */#[E]class(){};',
            ];
        }

        if (\PHP_VERSION_ID >= 80100) {
            yield [
                [1 => false],
                '<?php enum foo {}',
            ];
        }
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsLambdaCases
     */
    public function testIsLambda(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isLambda) {
            static::assertSame($isLambda, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambdaCases(): array
    {
        return [
            [
                [1 => false],
                '<?php function foo () {};',
            ],
            [
                [1 => false],
                '<?php function /** foo */ foo () {};',
            ],
            [
                [5 => true],
                '<?php $foo = function () {};',
            ],
            [
                [5 => true],
                '<?php $foo = function /** foo */ () {};',
            ],
            [
                [7 => true],
                '<?php
preg_replace_callback(
    "/(^|[a-z])/",
    function (array $matches) {
        return "a";
    },
    $string
);',
            ],
            [
                [5 => true],
                '<?php $foo = function &() {};',
            ],
            [
                [6 => true],
                '<?php
                    $a = function (): array {
                        return [];
                    };',
            ],
            [
                [2 => false],
                '<?php
                    function foo (): array {
                        return [];
                    };',
            ],
            [
                [6 => true],
                '<?php
                    $a = function (): void {
                        return [];
                    };',
            ],
            [
                [2 => false],
                '<?php
                    function foo (): void {
                        return [];
                    };',
            ],
            [
                [6 => true],
                '<?php
                    $a = function (): ?int {
                        return [];
                    };',
            ],
            [
                [6 => true],
                '<?php
                    $a = function (): int {
                        return [];
                    };',
            ],
            [
                [2 => false],
                '<?php
                    function foo (): ?int {
                        return [];
                    };',
            ],
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsLambda74Cases
     */
    public function testIsLambda74(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambda74Cases(): iterable
    {
        yield [
            [5 => true],
            '<?php $fn = fn() => [];',
        ];

        yield [
            [5 => true],
            '<?php $fn = fn () => [];',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsLambda80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsLambda80(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambda80Cases(): array
    {
        return [
            [
                [6 => true],
                '<?php
                    $a = function (): ?static {
                        return [];
                    };',
            ],
            [
                [6 => true],
                '<?php
                    $a = function (): static {
                        return [];
                    };',
            ],
            [
                [14 => true],
                '<?php
$c = 4; //
$a = function(
    $a,
    $b,
) use (
    $c,
) {
    echo $a + $b + $c;
};


$a(1,2);',
            ],
        ];
    }

    public function testIsLambdaInvalid(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No T_FUNCTION or T_FN at given index 0, got "T_OPEN_TAG".');

        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode('<?php '));
        $tokensAnalyzer->isLambda(0);
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsConstantInvocationCases
     */
    public function testIsConstantInvocation(array $expected, string $source): void
    {
        $this->doIsConstantInvocationTest($expected, $source);
    }

    public function provideIsConstantInvocationCases(): array
    {
        return [
            [
                [3 => true],
                '<?php echo FOO;',
            ],
            [
                [4 => true],
                '<?php echo \FOO;',
            ],
            [
                [3 => false, 5 => false, 7 => true],
                '<?php echo Foo\Bar\BAR;',
            ],
            [
                [3 => true, 7 => true, 11 => true],
                '<?php echo FOO ? BAR : BAZ;',
            ],
            'Bitwise & and bitwise |' => [
                [3 => true, 7 => true, 11 => true],
                '<?php echo FOO & BAR | BAZ;',
            ],
            'Bitwise &' => [
                [3 => true],
                '<?php echo FOO & $bar;',
            ],
            [
                [5 => true],
                '<?php echo $foo[BAR];',
            ],
            [
                [3 => true, 5 => true],
                '<?php echo FOO[BAR];',
            ],
            [
                [1 => false, 3 => true, 6 => false, 8 => true],
                '<?php func(FOO, Bar\BAZ);',
            ],
            [
                [4 => true, 8 => true],
                '<?php if (FOO && BAR) {}',
            ],
            [
                [3 => true, 7 => false, 9 => false, 11 => true],
                '<?php return FOO * X\Y\BAR;',
            ],
            [
                [3 => false, 11 => true, 16 => true, 20 => true],
                '<?php function x() { yield FOO; yield FOO => BAR; }',
            ],
            [
                [11 => true],
                '<?php switch ($a) { case FOO: break; }',
            ],
            [
                [3 => false],
                '<?php namespace FOO;',
            ],
            [
                [3 => false],
                '<?php use FOO;',
            ],
            [
                [5 => false, 7 => false, 9 => false],
                '<?php use function FOO\BAR\BAZ;',
            ],
            [
                [3 => false, 8 => false],
                '<?php namespace X; const FOO = 1;',
            ],
            [
                [3 => false],
                '<?php class FOO {}',
            ],
            [
                [3 => false],
                '<?php interface FOO {}',
            ],
            [
                [3 => false],
                '<?php trait FOO {}',
            ],
            [
                [3 => false, 7 => false],
                '<?php class x extends FOO {}',
            ],
            [
                [3 => false, 7 => false],
                '<?php class x implements FOO {}',
            ],
            [
                [3 => false, 7 => false, 10 => false, 13 => false],
                '<?php class x implements FOO, BAR, BAZ {}',
            ],
            [
                [3 => false, 9 => false],
                '<?php class x { const FOO = 1; }',
            ],
            [
                [3 => false, 9 => false],
                '<?php class x { use FOO; }',
            ],
            [
                [3 => false, 9 => false, 12 => false, 16 => false, 18 => false, 22 => false],
                '<?php class x { use FOO, BAR { FOO::BAZ insteadof BAR; } }',
            ],
            [
                [3 => false, 6 => false, 11 => false, 17 => false],
                '<?php function x (FOO $foo, BAR &$bar, BAZ ...$baz) {}',
            ],
            [
                [1 => false],
                '<?php FOO();',
            ],
            [
                [1 => false, 3 => false],
                '<?php FOO::x();',
            ],
            [
                [1 => false, 3 => false],
                '<?php x::FOO();',
            ],
            [
                [5 => false],
                '<?php $foo instanceof FOO;',
            ],
            [
                [9 => false],
                '<?php try {} catch (FOO $e) {}',
            ],
            [
                [4 => false],
                '<?php "$foo[BAR]";',
            ],
            [
                [5 => true],
                '<?php "{$foo[BAR]}";',
            ],
            [
                [1 => false, 6 => false],
                '<?php FOO: goto FOO;',
            ],
            [
                [1 => false, 3 => true, 7 => true],
                '<?php foo(E_USER_DEPRECATED | E_DEPRECATED);',
            ],
            [
                [3 => false, 7 => false, 10 => false, 13 => false],
                '<?php interface Foo extends Bar, Baz, Qux {}',
            ],
            [
                [3 => false, 5 => false, 8 => false, 10 => false, 13 => false, 15 => false],
                '<?php use Foo\Bar, Foo\Baz, Foo\Qux;',
            ],
            [
                [3 => false, 8 => false],
                '<?php function x(): FOO {}',
            ],
            [
                [3 => false, 5 => false, 8 => false, 11 => false, 15 => false, 18 => false],
                '<?php use X\Y\{FOO, BAR as BAR2, BAZ};',
            ],
            [
                [6 => false, 16 => false, 21 => false],
                '<?php

abstract class Baz
{
    abstract public function test(): Foo;
}
',
            ],
            [
                [3 => false, 6 => false],
                '<?php function x(?FOO $foo) {}',
            ],
            [
                [3 => false, 9 => false],
                '<?php function x(): ?FOO {}',
            ],
            [
                [9 => false, 11 => false, 13 => false],
                '<?php try {} catch (FOO|BAR|BAZ $e) {}',
            ],
            [
                [3 => false, 11 => false, 16 => false],
                '<?php interface Foo { public function bar(): Baz; }',
            ],
            [
                [3 => false, 11 => false, 17 => false],
                '<?php interface Foo { public function bar(): \Baz; }',
            ],
            [
                [3 => false, 11 => false, 17 => false],
                '<?php interface Foo { public function bar(): ?Baz; }',
            ],
            [
                [3 => false, 11 => false, 18 => false],
                '<?php interface Foo { public function bar(): ?\Baz; }',
            ],
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsConstantInvocationPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsConstantInvocationPhp80(array $expected, string $source): void
    {
        $this->doIsConstantInvocationTest($expected, $source);
    }

    public function provideIsConstantInvocationPhp80Cases(): iterable
    {
        yield 'abstract method return alternation' => [
            [6 => false, 16 => false, 21 => false, 23 => false],
            '<?php

abstract class Baz
{
    abstract public function test(): Foo|Bar;
}
',
        ];

        yield 'function return alternation' => [
            [3 => false, 8 => false, 10 => false],
            '<?php function test(): Foo|Bar {}',
        ];

        yield 'nullsafe operator' => [
            [3 => false, 5 => false],
            '<?php $a?->b?->c;',
        ];

        yield 'non-capturing catch' => [
            [9 => false],
            '<?php try {} catch (Exception) {}',
        ];

        yield 'non-capturing catch 2' => [
            [10 => false],
            '<?php try {} catch (\Exception) {}',
        ];

        yield 'non-capturing multiple catch' => [
            [9 => false, 13 => false],
            '<?php try {} catch (Foo | Bar) {}',
        ];

        yield 'attribute 1' => [
            [2 => false, 5 => false, 10 => false],
            '<?php #[Foo, Bar] function foo() {}',
        ];

        yield 'attribute 2' => [
            [2 => false, 7 => false, 14 => false],
            '<?php #[Foo(), Bar()] function foo() {}',
        ];

        yield [
            [2 => false, 9 => false],
            '<?php #[Foo()] function foo() {}',
        ];

        yield [
            [3 => false, 10 => false],
            '<?php #[\Foo()] function foo() {}',
        ];

        yield [
            [2 => false, 4 => false, 11 => false],
            '<?php #[A\Foo()] function foo() {}',
        ];

        yield [
            [3 => false, 5 => false, 12 => false],
            '<?php #[\A\Foo()] function foo() {}',
        ];

        yield 'multiple type catch with variable' => [
            [5 => false, 15 => false, 18 => false],
            '<?php try { foo(); } catch(\InvalidArgumentException|\LogicException $e) {}',
        ];

        yield 'multiple type catch without variable 1' => [
            [5 => false, 15 => false, 18 => false],
            '<?php try { foo(); } catch(\InvalidArgumentException|\LogicException) {}',
        ];

        yield 'multiple type catch without variable 2' => [
            [5 => false, 15 => false, 17 => false, 19 => false, 21 => false, 24 => false, 27 => false],
            '<?php try { foo(); } catch(\D|Z|A\B|\InvalidArgumentException|\LogicException) {}',
        ];

        yield 'multiple type catch without variable 3' => [
            [5 => false, 14 => false, 16 => false, 19 => false, 22 => false],
            '<?php try { foo(); } catch(A\B|\InvalidArgumentException|\LogicException) {}',
        ];

        yield 'attribute before' => [
            [4 => false, 6 => false, 8 => false, 13 => false, 17 => false, 23 => false, 25 => false],
            '<?php

use Psr\Log\LoggerInterface;
function f( #[Target(\'xxx\')] LoggerInterface|null $logger) {}
',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsConstantInvocationPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testIsConstantInvocationPhp81(array $expected, string $source): void
    {
        $this->doIsConstantInvocationTest($expected, $source);
    }

    public function provideIsConstantInvocationPhp81Cases(): iterable
    {
        yield [
            [5 => false, 15 => false],
            '<?php
abstract class Baz
{
    final public const Y = "i";
}
',
        ];

        yield [
            [4 => false, 12 => false, 23 => false],
            '<?php

class Test {
    public function __construct(
        public $prop = new Foo,
    ) {}
}
',
        ];

        yield 'intersection' => [
            [3 => false, 9 => false, 11 => false],
            '<?php function foo(): \Foo&Bar {}',
        ];

        yield 'abstract method return intersection' => [
            [6 => false, 16 => false, 21 => false, 23 => false, 25 => false, 27 => false, 29 => false],
            '<?php

abstract class Baz
{
    abstract public function foo(): Foo&Bar1&Bar2&Bar3&Bar4;
}
',
        ];
    }

    public function testIsConstantInvocationInvalid(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No T_STRING at given index 0, got "T_OPEN_TAG".');

        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode('<?php '));
        $tokensAnalyzer->isConstantInvocation(0);
    }

    /**
     * @requires PHP 8.0
     */
    public function testIsConstantInvocationForNullSafeObjectOperator(): void
    {
        $tokens = Tokens::fromCode('<?php $a?->b?->c;');
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            static::assertFalse($tokensAnalyzer->isConstantInvocation($index));
        }
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsUnarySuccessorOperatorCases
     */
    public function testIsUnarySuccessorOperator(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isUnary) {
            static::assertSame($isUnary, $tokensAnalyzer->isUnarySuccessorOperator($index));

            if ($isUnary) {
                static::assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
                static::assertFalse($tokensAnalyzer->isBinaryOperator($index));
            }
        }
    }

    public function provideIsUnarySuccessorOperatorCases(): array
    {
        return [
            [
                [2 => true],
                '<?php $a++;',
            ],
            [
                [2 => true],
                '<?php $a--;',
            ],
            [
                [3 => true],
                '<?php $a ++;',
            ],
            [
                [2 => true, 4 => false],
                '<?php $a++ + 1;',
            ],
            [
                [5 => true],
                '<?php ${"a"}++;',
            ],
            [
                [4 => true],
                '<?php $foo->bar++;',
            ],
            [
                [6 => true],
                '<?php $foo->{"bar"}++;',
            ],
            'array access' => [
                [5 => true],
                '<?php $a["foo"]++;',
            ],
            'array curly access' => [
                [5 => true],
                '<?php $a{"foo"}++;',
            ],
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsUnaryPredecessorOperatorCases
     */
    public function testIsUnaryPredecessorOperator(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isUnary) {
            static::assertSame($isUnary, $tokensAnalyzer->isUnaryPredecessorOperator($index));

            if ($isUnary) {
                static::assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                static::assertFalse($tokensAnalyzer->isBinaryOperator($index));
            }
        }
    }

    public function provideIsUnaryPredecessorOperatorCases(): array
    {
        return [
            [
                [1 => true],
                '<?php ++$a;',
            ],
            [
                [1 => true],
                '<?php --$a;',
            ],
            [
                [1 => true],
                '<?php -- $a;',
            ],
            [
                [3 => false, 5 => true],
                '<?php $a + ++$b;',
            ],
            [
                [1 => true, 2 => true],
                '<?php !!$a;',
            ],
            [
                [5 => true],
                '<?php $a = &$b;',
            ],
            [
                [3 => true],
                '<?php function &foo() {}',
            ],
            [
                [1 => true],
                '<?php @foo();',
            ],
            [
                [3 => true, 8 => true],
                '<?php foo(+ $a, -$b);',
            ],
            [
                [5 => true, 11 => true, 17 => true],
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
            ],
            [
                [8 => true],
                '<?php function foo($a, ...$b) {}',
            ],
            [
                [5 => true, 6 => true],
                '<?php function foo(&...$b) {}',
            ],
            [
                [7 => true],
                '<?php function foo(array ...$b) {}',
            ],
            [
                [7 => true],
                '<?php $foo = function(...$a) {};',
            ],
            [
                [10 => true],
                '<?php $foo = function($a, ...$b) {};',
            ],
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsBinaryOperatorCases
     */
    public function testIsBinaryOperator(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isBinary) {
            static::assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));

            if ($isBinary) {
                static::assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                static::assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperatorCases(): iterable
    {
        yield from [
            [
                [8 => true],
                '<?php echo $a[1] + 1;',
            ],
            [
                [8 => true],
                '<?php echo $a{1} + 1;',
            ],
            [
                [3 => true],
                '<?php $a .= $b; ?>',
            ],
            [
                [3 => true],
                '<?php $a . \'a\' ?>',
            ],
            [
                [3 => true],
                '<?php $a &+ $b;',
            ],
            [
                [3 => true],
                '<?php $a && $b;',
            ],
            [
                [3 => true],
                '<?php $a & $b;',
            ],
            [
                [4 => true],
                '<?php [] + [];',
            ],
            [
                [3 => true],
                '<?php $a + $b;',
            ],
            [
                [3 => true],
                '<?php 1 + $b;',
            ],
            [
                [3 => true],
                '<?php 0.2 + $b;',
            ],
            [
                [6 => true],
                '<?php $a[1] + $b;',
            ],
            [
                [3 => true],
                '<?php FOO + $b;',
            ],
            [
                [5 => true],
                '<?php foo() + $b;',
            ],
            [
                [6 => true],
                '<?php ${"foo"} + $b;',
            ],
            [
                [2 => true],
                '<?php $a+$b;',
            ],
            [
                [5 => true],
                '<?php $a /* foo */  +  /* bar */  $b;',
            ],
            [
                [3 => true],
                '<?php $a =
$b;',
            ],

            [
                [3 => true],
                '<?php $a
= $b;',
            ],
            [
                [3 => true, 9 => true, 12 => false],
                '<?php $a = array("b" => "c", );',
            ],
            [
                [3 => true, 5 => false],
                '<?php $a * -$b;',
            ],
            [
                [3 => true, 5 => false, 8 => true, 10 => false],
                '<?php $a = -2 / +5;',
            ],
            [
                [3 => true, 5 => false],
                '<?php $a = &$b;',
            ],
            [
                [2 => false, 4 => true],
                '<?php $a++ + $b;',
            ],
            [
                [7 => true],
                '<?php $a = FOO & $bar;',
            ],
            [
                [3 => true],
                '<?php __LINE__ - 1;',
            ],
            [
                [5 => true],
                '<?php `echo 1` + 1;',
            ],
            [
                [3 => true],
                '<?php $a ** $b;',
            ],
            [
                [3 => true],
                '<?php $a **= $b;',
            ],
            [
                [9 => false],
                '<?php $a = "{$value}-{$theSwitch}";',
            ],
            [
                [3 => false],
                '<?=$path?>-<?=$id?>',
            ],
        ];

        $operators = [
            '+', '-', '*', '/', '%', '<', '>', '|', '^', '&=', '&&', '||', '.=', '/=', '==', '>=', '===', '!=',
            '<>', '!==', '<=', 'and', 'or', 'xor', '-=', '%=', '*=', '|=', '+=', '<<', '<<=', '>>', '>>=', '^',
        ];

        foreach ($operators as $operator) {
            yield [
                [3 => true],
                '<?php $a '.$operator.' $b;',
            ];
        }

        yield [
            [3 => true],
            '<?php $a <=> $b;',
        ];

        yield [
            [3 => true],
            '<?php $a ?? $b;',
        ];
    }

    /**
     * @dataProvider provideIsArrayCases
     */
    public function testIsArray(string $source, int $tokenIndex, bool $isMultiLineArray = false): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        static::assertTrue($tokensAnalyzer->isArray($tokenIndex), 'Expected to be an array.');
        static::assertSame($isMultiLineArray, $tokensAnalyzer->isArrayMultiLine($tokenIndex), sprintf('Expected %sto be a multiline array', $isMultiLineArray ? '' : 'not '));
    }

    public function provideIsArrayCases(): array
    {
        return [
            [
                '<?php
                    array("a" => 1);
                ',
                2,
            ],
            [
                '<?php
                    ["a" => 2];
                ',
                2, false,
            ],
            [
                '<?php
                    array(
                        "a" => 3
                    );
                ',
                2, true,
            ],
            [
                '<?php
                    [
                        "a" => 4
                    ];
                ',
                2, true,
            ],
            [
                '<?php
                    array(
                        "a" => array(5, 6, 7),
8 => new \Exception(\'Hello\')
                    );
                ',
                2, true,
            ],
            [
                // mix short array syntax
                '<?php
                    array(
                        "a" => [9, 10, 11],
12 => new \Exception(\'Hello\')
                    );
                ',
                2, true,
            ],
            // Windows/Max EOL testing
            [
                "<?php\r\narray('a' => 13);\r\n",
                1,
            ],
            [
                "<?php\r\n   array(\r\n       'a' => 14,\r\n       'b' =>  15\r\n   );\r\n",
                2, true,
            ],
        ];
    }

    /**
     * @param list<int> $tokenIndexes
     *
     * @dataProvider provideIsArray71Cases
     */
    public function testIsArray71(string $source, array $tokenIndexes): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokens as $index => $token) {
            $expect = \in_array($index, $tokenIndexes, true);

            static::assertSame(
                $expect,
                $tokensAnalyzer->isArray($index),
                sprintf('Expected %sarray, got @ %d "%s".', $expect ? '' : 'no ', $index, var_export($token, true))
            );
        }
    }

    public function provideIsArray71Cases(): array
    {
        return [
            [
                '<?php
                    [$a] = $z;
                    ["a" => $a, "b" => $b] = $array;
                    $c = [$d, $e] = $array[$a];
                    [[$a, $b], [$c, $d]] = $d;
                    $array = []; $d = array();
                ',
                [76, 84],
            ],
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsBinaryOperator71Cases
     */
    public function testIsBinaryOperator71(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isBinary) {
            static::assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));

            if ($isBinary) {
                static::assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                static::assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperator71Cases(): iterable
    {
        yield [
            [11 => false],
            '<?php try {} catch (A | B $e) {}',
        ];

        yield [
            [3 => true],
            '<?php $a ??= $b;',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsBinaryOperator80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsBinaryOperator80(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isBinary) {
            static::assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));

            if ($isBinary) {
                static::assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                static::assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperator80Cases(): iterable
    {
        yield [
            [6 => false],
            '<?php function foo(array|string $x) {}',
        ];

        yield [
            [6 => false],
            '<?php function foo(string|array $x) {}',
        ];

        yield [
            [6 => false],
            '<?php function foo(int|callable $x) {}',
        ];

        yield [
            [6 => false],
            '<?php function foo(callable|int $x) {}',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsBinaryOperator81Cases
     *
     * @requires PHP 8.1
     */
    public function testIsBinaryOperator81(array $expected, string $source): void
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isBinary) {
            static::assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));

            if ($isBinary) {
                static::assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                static::assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperator81Cases(): iterable
    {
        yield 'type intersection' => [
            [6 => false],
            '<?php function foo(array&string $x) {}',
        ];
    }

    /**
     * @dataProvider provideArrayExceptionsCases
     */
    public function testIsNotArray(string $source, int $tokenIndex): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        static::assertFalse($tokensAnalyzer->isArray($tokenIndex));
    }

    /**
     * @dataProvider provideArrayExceptionsCases
     */
    public function testIsMultiLineArrayException(string $source, int $tokenIndex): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $tokensAnalyzer->isArrayMultiLine($tokenIndex);
    }

    public function provideArrayExceptionsCases(): array
    {
        return [
            ['<?php $a;', 1],
            ["<?php\n \$a = (0+1); // [0,1]", 4],
            ['<?php $text = "foo $bbb[0] bar";', 8],
            ['<?php $text = "foo ${aaa[123]} bar";', 9],
        ];
    }

    public function testIsBlockMultilineException(): void
    {
        $this->expectException(\LogicException::class);

        $tokens = Tokens::fromCode('<?php foo(1, 2, 3);');
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $tokensAnalyzer->isBlockMultiline($tokens, 1);
    }

    /**
     * @dataProvider provideIsBlockMultilineCases
     */
    public function testIsBlockMultiline(bool $isBlockMultiline, string $source, int $tokenIndex): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        static::assertSame($isBlockMultiline, $tokensAnalyzer->isBlockMultiline($tokens, $tokenIndex));
    }

    public static function provideIsBlockMultilineCases(): iterable
    {
        yield [
            false,
            '<?php foo(1, 2, 3);',
            2,
        ];

        yield [
            true,
            '<?php foo(1,
                2,
                3
            );',
            2,
        ];

        yield [
            false,
            '<?php foo(1, "Multi
                string", 2, 3);',
            2,
        ];

        yield [
            false,
            '<?php foo(1, havingNestedBlockThatIsMultilineDoesNotMakeTheMainBlockMultiline(
                    "a",
                    "b"
                ), 2, 3);',
            2,
        ];
    }

    /**
     * @param array{visibility: ?int, static: bool, abstract: bool, final: bool} $expected
     *
     * @dataProvider provideGetFunctionPropertiesCases
     */
    public function testGetFunctionProperties(string $source, int $index, array $expected): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $attributes = $tokensAnalyzer->getMethodAttributes($index);

        static::assertSame($expected, $attributes);
    }

    public function provideGetFunctionPropertiesCases(): array
    {
        $defaultAttributes = [
            'visibility' => null,
            'static' => false,
            'abstract' => false,
            'final' => false,
        ];

        $template = '
<?php
class TestClass {
    %s function a() {
        //
    }
}
';
        $cases = [];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PRIVATE;
        $cases[] = [sprintf($template, 'private'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $cases[] = [sprintf($template, 'public'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PROTECTED;
        $cases[] = [sprintf($template, 'protected'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = null;
        $attributes['static'] = true;
        $cases[] = [sprintf($template, 'static'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $attributes['static'] = true;
        $attributes['final'] = true;
        $cases[] = [sprintf($template, 'final public static'), 14, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = null;
        $attributes['abstract'] = true;
        $cases[] = [sprintf($template, 'abstract'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $attributes['abstract'] = true;
        $cases[] = [sprintf($template, 'abstract public'), 12, $attributes];

        $attributes = $defaultAttributes;
        $cases[] = [sprintf($template, ''), 8, $attributes];

        return $cases;
    }

    public function testIsWhilePartOfDoWhile(): void
    {
        $source =
<<<'SRC'
<?php
// `not do`
while(false) {
}
while (false);
while (false)?>
<?php

if(false){
}while(false);

if(false){
}while(false)?><?php
while(false){}while(false){}

while ($i <= 10):
    echo $i;
    $i++;
endwhile;

?>
<?php while(false): ?>

<?php endwhile ?>

<?php
// `do`
do{
} while(false);

do{
} while(false)?>
<?php
if (false){}do{}while(false);

// `not do`, `do`
if(false){}while(false){}do{}while(false);
SRC;

        $expected = [
            3 => false,
            12 => false,
            19 => false,
            34 => false,
            47 => false,
            53 => false,
            59 => false,
            66 => false,
            91 => false,
            112 => true,
            123 => true,
            139 => true,
            153 => false,
            162 => true,
        ];

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_WHILE)) {
                continue;
            }

            static::assertSame(
                $expected[$index],
                $tokensAnalyzer->isWhilePartOfDoWhile($index),
                sprintf('Expected token at index "%d" to be detected as %sa "do-while"-loop.', $index, true === $expected[$index] ? '' : 'not ')
            );
        }
    }

    /**
     * @param array<int, list<int>>|list<int> $expected
     *
     * @dataProvider provideGetImportUseIndexesCases
     */
    public function testGetImportUseIndexes(array $expected, string $input, bool $perNamespace = false): void
    {
        $tokens = Tokens::fromCode($input);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        static::assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function provideGetImportUseIndexesCases(): array
    {
        return [
            [
                [1, 8],
                '<?php use E\F?><?php use A\B;',
            ],
            [
                [[1], [14], [29]],
                '<?php
use T\A;
namespace A { use D\C; }
namespace b { use D\C; }
',
                true,
            ],
            [
                [[1, 8]],
                '<?php use D\B; use A\C?>',
                true,
            ],
            [
                [1, 8],
                '<?php use D\B; use A\C?>',
            ],
            [
                [7, 22],
                '<?php
namespace A { use D\C; }
namespace b { use D\C; }
',
            ],
            [
                [3, 10, 34, 45, 54, 59, 77, 95],
                <<<'EOF'
use Zoo\Bar;
use Foo\Bar;
use Foo\Zar\Baz;

<?php

use Foo\Bar;
use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar as FooBaz;
 use Foo\Bir as FBB;
use Foo\Zar\Baz;
use SomeClass;
   use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;
use Zoo\Bar;

$a = new someclass();

use Zoo\Tar;

class AnnotatedClass
{
}
EOF
                ,
            ],
            [
                [1, 22, 41],
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
                ',
            ],
            [
                [[1, 22, 41]],
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
                ',
                true,
            ],
            [
                [1, 23, 43],
                '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA, ConstB, ConstC,};
                ',
            ],
            [
                [[1, 23, 43]],
                '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA, ConstB, ConstC,};
                ',
                true,
            ],
        ];
    }

    public function testGetClassyElementsWithMultipleNestedAnonymousClass(): void
    {
        $source = '<?php
class MyTestWithAnonymousClass extends TestCase
{
    public function setUp()
    {
        $provider = new class(function () {}) {};
    }

    public function testSomethingWithMoney(
        Money $amount
    ) {
        $a = new class(function () {
    new class(function () {
        new class(function () {})
        {
            const A=1;
        };
    })
    {
        const B=1;

        public function foo() {
            $c = new class() {const AA=3;};
            $d = new class {const AB=3;};
        }
    };
})
{
    const C=1;
};
    }
}';
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        static::assertSame([
            13 => [
                'classIndex' => 1,
                'token' => $tokens[13],
                'type' => 'method', // setUp
            ],
            46 => [
                'classIndex' => 1,
                'token' => $tokens[46],
                'type' => 'method', // testSomethingWithMoney
            ],
            100 => [
                'classIndex' => 87,
                'token' => $tokens[100], // const A
                'type' => 'const',
            ],
            115 => [
                'classIndex' => 65,
                'token' => $tokens[115], // const B
                'type' => 'const',
            ],
            124 => [
                'classIndex' => 65, // $a
                'token' => $tokens[124],
                'type' => 'method', // foo
            ],
            143 => [
                'classIndex' => 138,
                'token' => $tokens[143], // const AA
                'type' => 'const',
            ],
            161 => [
                'classIndex' => 158,
                'token' => $tokens[161], // const AB
                'type' => 'const',
            ],
        ], $elements);
    }

    /**
     * @dataProvider provideIsSuperGlobalCases
     */
    public function testIsSuperGlobal(bool $expected, string $source, int $index): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        static::assertSame($expected, $tokensAnalyzer->isSuperGlobal($index));
    }

    public function provideIsSuperGlobalCases(): array
    {
        $superNames = [
            '$_COOKIE',
            '$_ENV',
            '$_FILES',
            '$_GET',
            '$_POST',
            '$_REQUEST',
            '$_SERVER',
            '$_SESSION',
            '$GLOBALS',
        ];

        $cases = [];

        foreach ($superNames as $superName) {
            $cases[] = [
                true,
                sprintf('<?php echo %s[0];', $superName),
                3,
            ];
        }

        $notGlobalCodeCases = [
            '<?php echo 1; $a = static function($b) use ($a) { $a->$b(); }; // $_SERVER',
            '<?php class Foo{}?> <?php $_A = 1; /* $_SESSION */',
        ];

        foreach ($notGlobalCodeCases as $notGlobalCodeCase) {
            $tokensCount = \count(Tokens::fromCode($notGlobalCodeCase));

            for ($i = 0; $i < $tokensCount; ++$i) {
                $cases[] = [
                    false,
                    $notGlobalCodeCase,
                    $i,
                ];
            }
        }

        return $cases;
    }

    /**
     * @param array<int, bool> $expected
     */
    private function doIsConstantInvocationTest(array $expected, string $source): void
    {
        $tokens = Tokens::fromCode($source);

        static::assertCount(
            $tokens->countTokenKind(T_STRING),
            $expected,
            'All T_STRING tokens must be tested'
        );

        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($expected as $index => $expectedValue) {
            static::assertSame(
                $expectedValue,
                $tokensAnalyzer->isConstantInvocation($index),
                sprintf('Token at index '.$index.' should match the expected value (%s).', $expectedValue ? 'true' : 'false')
            );
        }
    }
}
