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
 * @phpstan-import-type _ClassyElementType from \PhpCsFixer\Tokenizer\TokensAnalyzer
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
            static function (array &$element, int $index) use ($tokens): void {
                $element['token'] = $tokens[$index];
                ksort($element);
            }
        );

        $tokensAnalyzer = new TokensAnalyzer($tokens);

        self::assertSame(
            $expectedElements,
            $tokensAnalyzer->getClassyElements()
        );
    }

    /**
     * @return iterable<array{array<int, array{classIndex: int, type: string}>, string}>
     */
    public static function provideGetClassyElementsCases(): iterable
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

                PHP,
        ];

        yield [
            [
                11 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                23 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                31 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                44 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                51 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                54 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                61 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
                69 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
            ],
            <<<'PHP'
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

                PHP,
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
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

    /**
     * @param array<int, array{classIndex: int, type: string}> $expected
     *
     * @dataProvider provideGetClassyElements80Cases
     *
     * @requires PHP >= 8.0
     */
    public function testGetClassyElements80(array $expected, string $source): void
    {
        $this->testGetClassyElements($expected, $source);
    }

    /**
     * @return iterable<string, array{array<int, array{classIndex: int, type: string}>, string}>
     */
    public static function provideGetClassyElements80Cases(): iterable
    {
        yield 'promoted properties' => [
            [
                9 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                18 => [
                    'classIndex' => 1,
                    'type' => 'promoted_property',
                ],
                26 => [
                    'classIndex' => 1,
                    'type' => 'promoted_property',
                ],
                37 => [
                    'classIndex' => 1,
                    'type' => 'promoted_property',
                ],
            ],
            <<<'PHP'
                <?php class Foo {
                    public function __construct(
                        public bool $b,
                        protected ?int $i,
                        private bool|int|string $x,
                    ) {}
                }
                PHP,
        ];
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
        $this->testGetClassyElements($expected, $source);
    }

    /**
     * @return iterable<array{array<int, array{classIndex: int, type: string}>, string}>
     */
    public static function provideGetClassyElements81Cases(): iterable
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

        yield 'readonly promoted property' => [
            [
                9 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                19 => [
                    'classIndex' => 1,
                    'type' => 'promoted_property',
                ],
            ],
            <<<'PHP'
                <?php class Foo {
                    public function __construct(public readonly bool $b) {}
                }
                PHP,
        ];

        yield 'promoted property without visibility' => [
            [
                9 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                17 => [
                    'classIndex' => 1,
                    'type' => 'promoted_property',
                ],
            ],
            <<<'PHP'
                <?php class Foo {
                    public function __construct(readonly bool $b) {}
                }
                PHP,
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
        $this->testGetClassyElements($expected, $source);
    }

    /**
     * @return iterable<string, array{array<int, array{classIndex: int, type: string}>, string}>
     */
    public static function provideGetClassyElements82Cases(): iterable
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

        yield 'readonly class' => [
            [
                11 => [
                    'classIndex' => 3,
                    'type' => 'method',
                ],
                22 => [
                    'classIndex' => 3,
                    'type' => 'method',
                ],
            ],
            <<<'PHP'
                <?php readonly class Foo {
                    public function __construct() {}
                    public function process(object $event): void {}
                }
                PHP,
        ];
    }

    /**
     * @param array<int, array{classIndex: int, type: string}> $expected
     *
     * @dataProvider provideGetClassyElements84Cases
     *
     * @requires PHP >= 8.4
     */
    public function testGetClassyElements84(array $expected, string $source): void
    {
        $this->testGetClassyElements($expected, $source);
    }

    /**
     * @return iterable<string, array{array<int, array{classIndex: int, type: _ClassyElementType}>, string}>
     */
    public static function provideGetClassyElements84Cases(): iterable
    {
        yield 'property hooks' => [
            [
                11 => [
                    'classIndex' => 1,
                    'type' => 'property',
                ],
            ],
            <<<'PHP'
                <?php
                class Foo
                {
                    protected int $bar {
                        set => $this->bar = $value;
                    }
                }
                PHP,
        ];
    }

    /**
     * @param array<int, array{classIndex: int, type: _ClassyElementType}> $expected
     *
     * @dataProvider provideGetClassyElements85Cases
     *
     * @requires PHP 8.5
     */
    public function testGetClassyElements85(array $expected, string $source): void
    {
        $this->testGetClassyElements($expected, $source);
    }

    /**
     * @return iterable<string, array{array<int, array{classIndex: int, type: _ClassyElementType}>, string}>
     */
    public static function provideGetClassyElements85Cases(): iterable
    {
        yield 'final promoted property' => [
            [
                9 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                19 => [
                    'classIndex' => 1,
                    'type' => 'promoted_property',
                ],
            ],
            <<<'PHP'
                <?php class Foo {
                    public function __construct(public final bool $b) {}
                }
                PHP,
        ];

        yield 'promoted property without visibility' => [
            [
                9 => [
                    'classIndex' => 1,
                    'type' => 'method',
                ],
                17 => [
                    'classIndex' => 1,
                    'type' => 'promoted_property',
                ],
            ],
            <<<'PHP'
                <?php class Foo {
                    public function __construct(final bool $b) {}
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
            self::assertSame($expectedValue, $tokensAnalyzer->isAnonymousClass($index));
        }
    }

    /**
     * @return iterable<int, array{array<int, bool>, string}>
     */
    public static function provideIsAnonymousClassCases(): iterable
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
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsAnonymousClass80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsAnonymousClass80(array $expected, string $source): void
    {
        $this->testIsAnonymousClass($expected, $source);
    }

    /**
     * @return iterable<int, array{array<int, bool>, string}>
     */
    public static function provideIsAnonymousClass80Cases(): iterable
    {
        yield [
            [11 => true],
            '<?php $object = new #[ExampleAttribute] class(){};',
        ];

        yield [
            [27 => true],
            '<?php $object = new #[A] #[B] #[C]#[D]/* */ /** */#[E]class(){};',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsAnonymousClass81Cases
     *
     * @requires PHP 8.1
     */
    public function testIsAnonymousClass81(array $expected, string $source): void
    {
        $this->testIsAnonymousClass($expected, $source);
    }

    /**
     * @return iterable<int, array{array<int, bool>, string}>
     */
    public static function provideIsAnonymousClass81Cases(): iterable
    {
        yield [
            [1 => false],
            '<?php enum foo {}',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsAnonymousClass83Cases
     *
     * @requires PHP 8.3
     */
    public function testIsAnonymousClass83(array $expected, string $source): void
    {
        $this->testIsAnonymousClass($expected, $source);
    }

    /**
     * @return iterable<string, array{array<int, bool>, string}>
     */
    public static function provideIsAnonymousClass83Cases(): iterable
    {
        yield 'simple readonly anonymous class' => [
            [9 => true],
            '<?php $instance = new readonly class {};',
        ];

        yield 'readonly anonymous class with attribute' => [
            [13 => true],
            '<?php $instance = new #[Foo] readonly class {};',
        ];

        yield 'readonly anonymous class with multiple attributes' => [
            [17 => true],
            '<?php $instance = new #[Foo] #[BAR] readonly class {};',
        ];
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
            self::assertSame($isLambda, $tokensAnalyzer->isLambda($index));
        }
    }

    /**
     * @return iterable<int, array{array<int, bool>, string}>
     */
    public static function provideIsLambdaCases(): iterable
    {
        yield [
            [1 => false],
            '<?php function foo () {};',
        ];

        yield [
            [1 => false],
            '<?php function /** foo */ foo () {};',
        ];

        yield [
            [5 => true],
            '<?php $foo = function () {};',
        ];

        yield [
            [5 => true],
            '<?php $foo = function /** foo */ () {};',
        ];

        yield [
            [7 => true],
            '<?php
preg_replace_callback(
    "/(^|[a-z])/",
    function (array $matches) {
        return "a";
    },
    $string
);',
        ];

        yield [
            [5 => true],
            '<?php $foo = function &() {};',
        ];

        yield [
            [6 => true],
            '<?php
                    $a = function (): array {
                        return [];
                    };',
        ];

        yield [
            [2 => false],
            '<?php
                    function foo (): array {
                        return [];
                    };',
        ];

        yield [
            [6 => true],
            '<?php
                    $a = function (): void {
                        return [];
                    };',
        ];

        yield [
            [2 => false],
            '<?php
                    function foo (): void {
                        return [];
                    };',
        ];

        yield [
            [6 => true],
            '<?php
                    $a = function (): ?int {
                        return [];
                    };',
        ];

        yield [
            [6 => true],
            '<?php
                    $a = function (): int {
                        return [];
                    };',
        ];

        yield [
            [2 => false],
            '<?php
                    function foo (): ?int {
                        return [];
                    };',
        ];

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
        $this->testIsLambda($expected, $source);
    }

    /**
     * @return iterable<int, array{array<int, bool>, string}>
     */
    public static function provideIsLambda80Cases(): iterable
    {
        yield [
            [6 => true],
            '<?php
                    $a = function (): ?static {
                        return [];
                    };',
        ];

        yield [
            [6 => true],
            '<?php
                    $a = function (): static {
                        return [];
                    };',
        ];

        yield [
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

    /**
     * @return iterable<array{array<int, bool>, string}>
     */
    public static function provideIsConstantInvocationCases(): iterable
    {
        yield [
            [3 => true],
            '<?php echo FOO;',
        ];

        yield [
            [4 => true],
            '<?php echo \FOO;',
        ];

        yield [
            [3 => false, 5 => false, 7 => true],
            '<?php echo Foo\Bar\BAR;',
        ];

        yield [
            [3 => true, 7 => true, 11 => true],
            '<?php echo FOO ? BAR : BAZ;',
        ];

        yield 'Bitwise & and bitwise |' => [
            [3 => true, 7 => true, 11 => true],
            '<?php echo FOO & BAR | BAZ;',
        ];

        yield 'Bitwise &' => [
            [3 => true],
            '<?php echo FOO & $bar;',
        ];

        yield [
            [5 => true],
            '<?php echo $foo[BAR];',
        ];

        yield [
            [3 => true, 5 => true],
            '<?php echo FOO[BAR];',
        ];

        yield [
            [1 => false, 3 => true, 6 => false, 8 => true],
            '<?php func(FOO, Bar\BAZ);',
        ];

        yield [
            [4 => true, 8 => true],
            '<?php if (FOO && BAR) {}',
        ];

        yield [
            [3 => true, 7 => false, 9 => false, 11 => true],
            '<?php return FOO * X\Y\BAR;',
        ];

        yield [
            [3 => false, 11 => true, 16 => true, 20 => true],
            '<?php function x() { yield FOO; yield FOO => BAR; }',
        ];

        yield [
            [11 => true],
            '<?php switch ($a) { case FOO: break; }',
        ];

        yield [
            [3 => false],
            '<?php namespace FOO;',
        ];

        yield [
            [3 => false],
            '<?php use FOO;',
        ];

        yield [
            [5 => false, 7 => false, 9 => false],
            '<?php use function FOO\BAR\BAZ;',
        ];

        yield [
            [3 => false, 8 => false],
            '<?php namespace X; const FOO = 1;',
        ];

        yield [
            [3 => false],
            '<?php class FOO {}',
        ];

        yield [
            [3 => false],
            '<?php interface FOO {}',
        ];

        yield [
            [3 => false],
            '<?php trait FOO {}',
        ];

        yield [
            [3 => false, 7 => false],
            '<?php class x extends FOO {}',
        ];

        yield [
            [3 => false, 7 => false],
            '<?php class x implements FOO {}',
        ];

        yield [
            [3 => false, 7 => false, 10 => false, 13 => false],
            '<?php class x implements FOO, BAR, BAZ {}',
        ];

        yield [
            [3 => false, 9 => false],
            '<?php class x { const FOO = 1; }',
        ];

        yield [
            [3 => false, 9 => false],
            '<?php class x { use FOO; }',
        ];

        yield [
            [3 => false, 9 => false, 12 => false, 16 => false, 18 => false, 22 => false],
            '<?php class x { use FOO, BAR { FOO::BAZ insteadof BAR; } }',
        ];

        yield [
            [3 => false, 6 => false, 11 => false, 17 => false],
            '<?php function x (FOO $foo, BAR &$bar, BAZ ...$baz) {}',
        ];

        yield [
            [1 => false],
            '<?php FOO();',
        ];

        yield [
            [1 => false, 3 => false],
            '<?php FOO::x();',
        ];

        yield [
            [1 => false, 3 => false],
            '<?php x::FOO();',
        ];

        yield [
            [5 => false],
            '<?php $foo instanceof FOO;',
        ];

        yield [
            [9 => false],
            '<?php try {} catch (FOO $e) {}',
        ];

        yield [
            [4 => false],
            '<?php "$foo[BAR]";',
        ];

        yield [
            [5 => true],
            '<?php "{$foo[BAR]}";',
        ];

        yield [
            [1 => false, 6 => false],
            '<?php FOO: goto FOO;',
        ];

        yield [
            [1 => false, 3 => true, 7 => true],
            '<?php foo(E_USER_DEPRECATED | E_DEPRECATED);',
        ];

        yield [
            [3 => false, 7 => false, 10 => false, 13 => false],
            '<?php interface Foo extends Bar, Baz, Qux {}',
        ];

        yield [
            [3 => false, 5 => false, 8 => false, 10 => false, 13 => false, 15 => false],
            '<?php use Foo\Bar, Foo\Baz, Foo\Qux;',
        ];

        yield [
            [3 => false, 8 => false],
            '<?php function x(): FOO {}',
        ];

        yield [
            [3 => false, 5 => false, 8 => false, 11 => false, 15 => false, 18 => false],
            '<?php use X\Y\{FOO, BAR as BAR2, BAZ};',
        ];

        yield [
            [6 => false, 16 => false, 21 => false],
            '<?php

abstract class Baz
{
    abstract public function test(): Foo;
}
',
        ];

        yield [
            [3 => false, 6 => false],
            '<?php function x(?FOO $foo) {}',
        ];

        yield [
            [3 => false, 9 => false],
            '<?php function x(): ?FOO {}',
        ];

        yield [
            [9 => false, 11 => false, 13 => false],
            '<?php try {} catch (FOO|BAR|BAZ $e) {}',
        ];

        yield [
            [3 => false, 11 => false, 16 => false],
            '<?php interface Foo { public function bar(): Baz; }',
        ];

        yield [
            [3 => false, 11 => false, 17 => false],
            '<?php interface Foo { public function bar(): \Baz; }',
        ];

        yield [
            [3 => false, 11 => false, 17 => false],
            '<?php interface Foo { public function bar(): ?Baz; }',
        ];

        yield [
            [3 => false, 11 => false, 18 => false],
            '<?php interface Foo { public function bar(): ?\Baz; }',
        ];

        yield [
            [3 => true],
            '<?php foreach(FOO as $foo) {}',
        ];

        yield [
            [
                3 => false,
                5 => false,
                9 => false,
            ],
            '<?php use Foo\Bir as FBB;',
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

    /**
     * @return iterable<array{array<int, bool>, string}>
     */
    public static function provideIsConstantInvocationPhp80Cases(): iterable
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

    /**
     * @return iterable<array{array<int, bool>, string}>
     */
    public static function provideIsConstantInvocationPhp81Cases(): iterable
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

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsConstantInvocationPhp82Cases
     *
     * @requires PHP 8.2
     */
    public function testIsConstantInvocationPhp82(array $expected, string $source): void
    {
        $this->doIsConstantInvocationTest($expected, $source);
    }

    /**
     * @return iterable<int, array{array<int, bool>, string}>
     */
    public static function provideIsConstantInvocationPhp82Cases(): iterable
    {
        yield [
            [3 => false, 11 => false, 13 => false, 17 => false, 20 => false, 23 => false, 25 => false, 28 => false, 31 => false, 33 => false, 35 => false, 39 => false, 42 => false, 44 => false],
            '<?php class Foo { public (\A&B)|(C&\D)|E\F|\G|(A&H\I)|(A&\J\K) $var; }',
        ];

        yield [
            [3 => false, 8 => false, 10 => false, 14 => false, 17 => false, 20 => false, 22 => false, 25 => false, 28 => false, 30 => false, 32 => false, 36 => false, 39 => false, 41 => false],
            '<?php function foo ((\A&B)|(C&\D)|E\F|\G|(A&H\I)|(A&\J\K) $var) {}',
        ];

        yield [
            [3 => false, 6 => false, 12 => false],
            '<?php enum someEnum: int
                {
                    case E_ERROR = 123;
                }',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsConstantInvocationPhp83Cases
     *
     * @requires PHP 8.3
     */
    public function testIsConstantInvocationPhp83(array $expected, string $source): void
    {
        $this->doIsConstantInvocationTest($expected, $source);
    }

    /**
     * @return iterable<int, array{array<int, bool>, string}>
     */
    public static function provideIsConstantInvocationPhp83Cases(): iterable
    {
        yield [
            [3 => false, 11 => false, 13 => false, 17 => true],
            '<?php class Foo { public const string FOO = BAR; }',
        ];

        yield [
            [3 => false, 11 => false, 13 => false, 17 => true],
            '<?php class Foo { public const int FOO = BAR; }',
        ];

        yield [
            [3 => false, 11 => false, 13 => false, 17 => true],
            '<?php class Foo { public const bool FOO = BAR; }',
        ];

        yield [
            [3 => false, 13 => false, 18 => true],
            '<?php class Foo { public const array FOO = [BAR]; }',
        ];

        yield [
            [3 => false, 11 => false, 13 => false, 17 => true],
            '<?php class Foo { public const A FOO = BAR; }',
        ];

        yield [
            [3 => false, 11 => false, 13 => false, 15 => false, 19 => true],
            '<?php class Foo { public const A|B FOO = BAR; }',
        ];

        yield [
            [3 => false, 11 => false, 13 => false, 15 => false, 19 => true],
            '<?php class Foo { public const A&B FOO = BAR; }',
        ];

        yield [
            [3 => false, 12 => false, 14 => false, 17 => false, 19 => false, 23 => true],
            '<?php class Foo { public const (A&B)|C FOO = BAR; }',
        ];

        yield [
            [3 => false, 12 => false, 14 => false, 18 => false, 20 => false, 23 => false, 27 => true],
            '<?php class Foo { public const (A&B)|(C&D) FOO = BAR; }',
        ];

        yield [
            [3 => false, 12 => false, 15 => false, 17 => false, 21 => false, 23 => false, 25 => false, 28 => false, 32 => true],
            '<?php class Foo { public const (A&\B\C)|(D\E&F) FOO = BAR; }',
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

            self::assertFalse($tokensAnalyzer->isConstantInvocation($index));
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
            self::assertSame($isUnary, $tokensAnalyzer->isUnarySuccessorOperator($index));

            if ($isUnary) {
                self::assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
                self::assertFalse($tokensAnalyzer->isBinaryOperator($index));
            }
        }
    }

    /**
     * @return iterable<array{array<int, bool>, string}>
     */
    public static function provideIsUnarySuccessorOperatorCases(): iterable
    {
        yield [
            [2 => true],
            '<?php $a++;',
        ];

        yield [
            [2 => true],
            '<?php $a--;',
        ];

        yield [
            [3 => true],
            '<?php $a ++;',
        ];

        yield [
            [2 => true, 4 => false],
            '<?php $a++ + 1;',
        ];

        yield [
            [5 => true],
            '<?php ${"a"}++;',
        ];

        yield [
            [4 => true],
            '<?php $foo->bar++;',
        ];

        yield [
            [6 => true],
            '<?php $foo->{"bar"}++;',
        ];

        yield 'array access' => [
            [5 => true],
            '<?php $a["foo"]++;',
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsUnarySuccessorOperatorPre84Cases
     *
     * @requires PHP <8.4
     */
    public function testIsUnarySuccessorOperatorPre84(array $expected, string $source): void
    {
        $this->testIsUnarySuccessorOperator($expected, $source);
    }

    /**
     * @return iterable<string, array{array<int, bool>, string}>
     */
    public static function provideIsUnarySuccessorOperatorPre84Cases(): iterable
    {
        yield 'array curly access' => [
            [5 => true],
            '<?php $a{"foo"}++;',
        ];
    }

    /**
     * @param list<int> $expected
     *
     * @dataProvider provideIsUnaryPredecessorOperatorCases
     */
    public function testIsUnaryPredecessorOperator(array $expected, string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($tokens as $index => $token) {
            $expect = \in_array($index, $expected, true);

            self::assertSame(
                $expect,
                $tokensAnalyzer->isUnaryPredecessorOperator($index),
                \sprintf('Expected %sunary predecessor operator, got @ %d "%s".', $expect ? '' : 'no ', $index, var_export($token, true))
            );

            if ($expect) {
                self::assertFalse(
                    $tokensAnalyzer->isUnarySuccessorOperator($index),
                    \sprintf('Expected no unary successor operator, got @ %d "%s".', $index, var_export($token, true))
                );
                self::assertFalse(
                    $tokensAnalyzer->isBinaryOperator($index),
                    \sprintf('Expected no binary operator, got @ %d "%s".', $index, var_export($token, true))
                );
            }
        }
    }

    /**
     * @return iterable<int, array{list<int>, string}>
     */
    public static function provideIsUnaryPredecessorOperatorCases(): iterable
    {
        yield [
            [1],
            '<?php ++$a;',
        ];

        yield [
            [1],
            '<?php --$a;',
        ];

        yield [
            [1],
            '<?php -- $a;',
        ];

        yield [
            [5],
            '<?php $a + ++$b;',
        ];

        yield [
            [1, 2],
            '<?php !!$a;',
        ];

        yield [
            [5],
            '<?php $a = &$b;',
        ];

        yield [
            [3],
            '<?php function &foo() {}',
        ];

        yield [
            [1],
            '<?php @foo();',
        ];

        yield [
            [3, 8],
            '<?php foo(+ $a, -$b);',
        ];

        yield [
            [5, 11, 17],
            '<?php function foo(&$a, array &$b, Bar &$c) {}',
        ];

        yield [
            [8],
            '<?php function foo($a, ...$b) {}',
        ];

        yield [
            [5, 6],
            '<?php function foo(&...$b) {}',
        ];

        yield [
            [7],
            '<?php function foo(array ...$b) {}',
        ];

        yield [
            [7],
            '<?php $foo = function(...$a) {};',
        ];

        yield [
            [10],
            '<?php $foo = function($a, ...$b) {};',
        ];

        yield [
            [9],
            '<?php $foo = function(int &$x) {};',
        ];

        yield [
            [9],
            '<?php $foo = fn(int &$x) => null;',
        ];

        yield [
            [],
            '<?php fn() => A_CONSTANT & $object->property;',
        ];
    }

    /**
     * @param list<int> $expected
     *
     * @dataProvider provideIsBinaryOperatorCases
     */
    public function testIsBinaryOperator(array $expected, string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($tokens as $index => $token) {
            $expect = \in_array($index, $expected, true);

            self::assertSame(
                $expect,
                $tokensAnalyzer->isBinaryOperator($index),
                \sprintf('Expected %sbinary operator, got @ %d "%s".', $expect ? '' : 'no ', $index, var_export($token, true))
            );

            if ($expect) {
                self::assertFalse(
                    $tokensAnalyzer->isUnarySuccessorOperator($index),
                    \sprintf('Expected no unary successor operator, got @ %d "%s".', $index, var_export($token, true))
                );
                self::assertFalse(
                    $tokensAnalyzer->isUnaryPredecessorOperator($index),
                    \sprintf('Expected no unary predecessor operator, got @ %d "%s".', $index, var_export($token, true))
                );
            }
        }
    }

    /**
     * @return iterable<int, array{array<int, bool|int>, string}>
     */
    public static function provideIsBinaryOperatorCases(): iterable
    {
        yield [
            [8],
            '<?php echo $a[1] + 1;',
        ];

        yield [
            [3],
            '<?php $a .= $b; ?>',
        ];

        yield [
            [3],
            '<?php $a . \'a\' ?>',
        ];

        yield [
            [3],
            '<?php $a &+ $b;',
        ];

        yield [
            [4],
            '<?php [] + [];',
        ];

        yield [
            [3],
            '<?php 1 + $b;',
        ];

        yield [
            [3],
            '<?php 0.2 + $b;',
        ];

        yield [
            [6],
            '<?php $a[1] + $b;',
        ];

        yield [
            [3],
            '<?php FOO + $b;',
        ];

        yield [
            [5],
            '<?php foo() + $b;',
        ];

        yield [
            [6],
            '<?php ${"foo"} + $b;',
        ];

        yield [
            [2],
            '<?php $a+$b;',
        ];

        yield [
            [5],
            '<?php $a /* foo */  +  /* bar */  $b;',
        ];

        yield [
            [3],
            '<?php $a =
$b;',
        ];

        yield [
            [3],
            '<?php $a
= $b;',
        ];

        yield [
            [3, 9],
            '<?php $a = array("b" => "c", );',
        ];

        yield [
            [3],
            '<?php $a * -$b;',
        ];

        yield [
            [3, 8],
            '<?php $a = -2 / +5;',
        ];

        yield [
            [3, 5 => false],
            '<?php $a = &$b;',
        ];

        yield [
            [4],
            '<?php $a++ + $b;',
        ];

        yield [
            [3, 7],
            '<?php $a = FOO & $bar;',
        ];

        yield [
            [3],
            '<?php __LINE__ - 1;',
        ];

        yield [
            [5],
            '<?php `echo 1` + 1;',
        ];

        yield [
            [3],
            '<?php $a ** $b;',
        ];

        yield [
            [3],
            '<?php $a **= $b;',
        ];

        yield [
            [3],
            '<?php $a = "{$value}-{$theSwitch}";',
        ];

        yield [
            [],
            '<?=$path?>-<?=$id?>',
        ];

        $operators = [
            '+', '-', '*', '/', '%', '<', '>', '|', '^', '&=', '&&', '||', '.=', '/=', '==', '>=', '===', '!=',
            '<>', '!==', '<=', 'and', 'or', 'xor', '-=', '%=', '*=', '|=', '+=', '<<', '<<=', '>>', '>>=',
        ];

        foreach ($operators as $operator) {
            yield [
                [3],
                '<?php $a '.$operator.' $b;',
            ];
        }

        yield [
            [3],
            '<?php $a <=> $b;',
        ];

        yield [
            [3],
            '<?php $a ?? $b;',
        ];

        yield [
            [],
            '<?php try {} catch (A | B $e) {}',
        ];

        yield [
            [5, 11],
            '<?php fn() => $object->property & A_CONSTANT;',
        ];
    }

    /**
     * @dataProvider provideIsArrayCases
     *
     * @param array<int,bool> $tokenIndices in the form: index => isArrayMultiLine
     */
    public function testIsArray(string $source, array $tokenIndices): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokens as $index => $token) {
            $isArray = \array_key_exists($index, $tokenIndices);

            self::assertSame(
                $isArray,
                $tokensAnalyzer->isArray($index),
                \sprintf('Expected %sarray, got @ %d "%s".', $isArray ? '' : 'no ', $index, var_export($token, true))
            );
            if (\array_key_exists($index, $tokenIndices)) {
                self::assertSame(
                    $tokenIndices[$index],
                    $tokensAnalyzer->isArrayMultiLine($index),
                    \sprintf('Expected %sto be a multiline array', $tokenIndices[$index] ? '' : 'not ')
                );
            }
        }
    }

    /**
     * @return iterable<int, array{string, array<int, bool>}>
     */
    public static function provideIsArrayCases(): iterable
    {
        yield [
            '<?php
                    array("a" => 1);
                ',
            [2 => false],
        ];

        yield [
            '<?php
                    ["a" => 2];
                ',
            [2 => false],
        ];

        yield [
            '<?php
                    array(
                        "a" => 3
                    );
                ',
            [2 => true],
        ];

        yield [
            '<?php
                    [
                        "a" => 4
                    ];
                ',
            [2 => true],
        ];

        yield [
            '<?php
                    array(
                        "a" => array(5, 6, 7),
8 => new \Exception(\'Hello\')
                    );
                ',
            [2 => true, 9 => false],
        ];

        yield [
            // mix short array syntax
            '<?php
                    array(
                        "a" => [9, 10, 11],
12 => new \Exception(\'Hello\')
                    );
                ',
            [2 => true, 9 => false],
        ];

        // Windows/Max EOL testing
        yield [
            "<?php\r\narray('a' => 13);\r\n",
            [1 => false],
        ];

        yield [
            "<?php\r\n   array(\r\n       'a' => 14,\r\n       'b' =>  15\r\n   );\r\n",
            [2 => true],
        ];

        yield [
            '<?php
                    [$a] = $z;
                    ["a" => $a, "b" => $b] = $array;
                    $c = [$d, $e] = $array[$a];
                    [[$a, $b], [$c, $d]] = $d;
                    $array = []; $d = array();
                ',
            [76 => false, 84 => false],
        ];
    }

    /**
     * @param list<int> $expected
     *
     * @dataProvider provideIsBinaryOperator80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsBinaryOperator80(array $expected, string $source): void
    {
        $this->testIsBinaryOperator($expected, $source);
    }

    /**
     * @return iterable<int, array{list<int>, string}>
     */
    public static function provideIsBinaryOperator80Cases(): iterable
    {
        yield [
            [],
            '<?php function foo(array|string $x) {}',
        ];

        yield [
            [],
            '<?php function foo(string|array $x) {}',
        ];

        yield [
            [],
            '<?php function foo(int|callable $x) {}',
        ];

        yield [
            [],
            '<?php function foo(callable|int $x) {}',
        ];

        yield [
            [],
            '<?php function foo(bool|int &$x) {}',
        ];
    }

    /**
     * @param list<int> $expected
     *
     * @dataProvider provideIsBinaryOperator81Cases
     *
     * @requires PHP 8.1
     */
    public function testIsBinaryOperator81(array $expected, string $source): void
    {
        $this->testIsBinaryOperator($expected, $source);
    }

    /**
     * @return iterable<string, array{list<int>, string}>
     */
    public static function provideIsBinaryOperator81Cases(): iterable
    {
        yield 'type intersection' => [
            [],
            '<?php function foo(array&string $x) {}',
        ];
    }

    /**
     * @param list<int> $expected
     *
     * @dataProvider provideIsBinaryOperator82Cases
     *
     * @requires PHP 8.2
     */
    public function testIsBinaryOperator82(array $expected, string $source): void
    {
        $this->testIsBinaryOperator($expected, $source);
    }

    /**
     * @return iterable<array{list<int>, string}>
     */
    public static function provideIsBinaryOperator82Cases(): iterable
    {
        yield [
            [],
            '<?php class Dnf { public static I|(P&S11) $f2;}',
        ];

        yield [
            [],
            '<?php function Foo((A&B)|I $x): (X&Z)|(p\f\G&Y\Z)|z { return foo();}',
        ];

        $particularEndOfFile = 'A|(B&C); }';

        yield \sprintf('block "%s" at the end of file that is a type', $particularEndOfFile) => [
            [],
            '<?php abstract class A { abstract function foo(): '.$particularEndOfFile,
        ];

        yield \sprintf('block "%s" at the end of file that is not a type', $particularEndOfFile) => [
            [12, 15],
            '<?php function foo() { return '.$particularEndOfFile,
        ];
    }

    /**
     * @param list<int> $expected
     *
     * @dataProvider provideIsBinaryOperatorPre84Cases
     *
     * @requires PHP <8.4
     */
    public function testIsBinaryOperatorPre84(array $expected, string $source): void
    {
        $this->testIsBinaryOperator($expected, $source);
    }

    /**
     * @return iterable<int, array{list<int>, string}>
     */
    public static function provideIsBinaryOperatorPre84Cases(): iterable
    {
        yield [
            [8],
            '<?php echo $a{1} + 1;',
        ];
    }

    /**
     * @dataProvider provideArrayExceptionsCases
     */
    public function testIsNotArray(string $source, int $tokenIndex): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        self::assertFalse($tokensAnalyzer->isArray($tokenIndex));
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

    /**
     * @return iterable<int, array{string, int}>
     */
    public static function provideArrayExceptionsCases(): iterable
    {
        yield ['<?php $a;', 1];

        yield ["<?php\n \$a = (0+1); // [0,1]", 4];

        yield ['<?php $text = "foo $bbb[0] bar";', 8];

        yield ['<?php $text = "foo ${aaa[123]} bar";', 9];
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

        self::assertSame($isBlockMultiline, $tokensAnalyzer->isBlockMultiline($tokens, $tokenIndex));
    }

    /**
     * @return iterable<int, array{bool, string, int}>
     */
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

        self::assertSame($expected, $attributes);
    }

    /**
     * @return iterable<int, array{string, int, array{visibility: ?int, static: bool, abstract: bool, final: bool}}>
     */
    public static function provideGetFunctionPropertiesCases(): iterable
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
        $cases[] = [\sprintf($template, 'private'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $cases[] = [\sprintf($template, 'public'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PROTECTED;
        $cases[] = [\sprintf($template, 'protected'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = null;
        $attributes['static'] = true;
        $cases[] = [\sprintf($template, 'static'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $attributes['static'] = true;
        $attributes['final'] = true;
        $cases[] = [\sprintf($template, 'final public static'), 14, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = null;
        $attributes['abstract'] = true;
        $cases[] = [\sprintf($template, 'abstract'), 10, $attributes];

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $attributes['abstract'] = true;
        $cases[] = [\sprintf($template, 'abstract public'), 12, $attributes];

        $attributes = $defaultAttributes;
        $cases[] = [\sprintf($template, ''), 8, $attributes];

        return $cases;
    }

    public function testIsWhilePartOfDoWhile(): void
    {
        $source = <<<'SRC'
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

            $isExpected = $expected[$index] ?? null;

            self::assertSame(
                $isExpected,
                $tokensAnalyzer->isWhilePartOfDoWhile($index),
                \sprintf('Expected token at index "%d" to be detected as %sa "do-while"-loop.', $index, true === $isExpected ? '' : 'not ')
            );
        }
    }

    /**
     * @dataProvider provideIsEnumCaseCases
     *
     * @param array<int, bool> $expected
     *
     * @requires PHP 8.1
     */
    public function testIsEnumCase(string $source, array $expected): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CASE)) {
                try {
                    $tokensAnalyzer->isEnumCase($index);
                    self::fail('TokensAnalyzer::isEnumCase() did not throw LogicException.');
                } catch (\Throwable $e) {
                    self::assertInstanceOf(\LogicException::class, $e);
                    self::assertMatchesRegularExpression('/^No T_CASE given at index \d+, got \S+ instead\.$/', $e->getMessage());
                }

                continue;
            }

            \assert(\array_key_exists($index, $expected));
            self::assertSame($expected[$index], $tokensAnalyzer->isEnumCase($index));
        }
    }

    /**
     * @return iterable<string, array{string, array<int, bool>}>
     */
    public static function provideIsEnumCaseCases(): iterable
    {
        yield 'switch only' => [
            '<?php
function bar(string $a): string
{
    switch ($a) {
        case \'one\':
            return $a;
        case \'two\':
        default:
            return strtoupper($a);
    }
}
',
            [
                23 => false,
                33 => false,
            ],
        ];

        yield 'pure enum' => [
            '<?php
enum Foo
{
    case One;
    case Two;
}
',
            [
                7 => true,
                12 => true,
            ],
        ];

        yield 'pure enum with switch' => [
            '<?php
enum Foo
{
    case One;
    case Two;

    public static function getLowerName(self $instance): string
    {
        switch ($instance->name) {
            case \'One\':
            case \'Two\':
                return strtolower($instance->name);
        }
    }
}
',
            [
                7 => true,
                12 => true,
                45 => false,
                50 => false,
            ],
        ];

        yield 'backed enum' => [
            '<?php
enum Suit: string
{
    case Hearts = \'hearts\';
    case Spades = \'spades\';
    case Clubs = \'clubs\';
    case Diamonds = \'diamonds\';
}
',
            [
                10 => true,
                19 => true,
                28 => true,
                37 => true,
            ],
        ];

        yield 'backed enum with switch' => [
            '<?php
enum Suit: string
{
    case Hearts = \'hearts\';
    case Spades = \'spades\';
    case Clubs = \'clubs\';
    case Diamonds = \'diamonds\';

    public static function getUppercasedValue(self $instance): string
    {
        switch ($instance->value) {
            case \'hearts\':
            case \'spades\':
                return strtoupper($instance->value);
            default:
                return $instance->value;
        }
    }
}
',
            [
                10 => true,
                19 => true,
                28 => true,
                37 => true,
                74 => false,
                79 => false,
            ],
        ];
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

        self::assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    /**
     * @return iterable<int, array{0: array<int, list<int>>|list<int>, 1: string, 2?: bool}>
     */
    public static function provideGetImportUseIndexesCases(): iterable
    {
        yield [
            [1, 8],
            '<?php use E\F?><?php use A\B;',
        ];

        yield [
            [[1], [14], [29]],
            '<?php
use T\A;
namespace A { use D\C; }
namespace b { use D\C; }
',
            true,
        ];

        yield [
            [[1, 8]],
            '<?php use D\B; use A\C?>',
            true,
        ];

        yield [
            [1, 8],
            '<?php use D\B; use A\C?>',
        ];

        yield [
            [7, 22],
            '<?php
namespace A { use D\C; }
namespace b { use D\C; }
',
        ];

        yield [
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
                EOF,
        ];

        yield [
            [1, 22, 41],
            '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
                ',
        ];

        yield [
            [[1, 22, 41]],
            '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
                ',
            true,
        ];

        yield [
            [1, 23, 43],
            '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA, ConstB, ConstC,};
                ',
        ];

        yield [
            [[1, 23, 43]],
            '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA, ConstB, ConstC,};
                ',
            true,
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

        self::assertSame([
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

        self::assertSame($expected, $tokensAnalyzer->isSuperGlobal($index));
    }

    /**
     * @return iterable<int, array{bool, string, int}>
     */
    public static function provideIsSuperGlobalCases(): iterable
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
                \sprintf('<?php echo %s[0];', $superName),
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
     * @dataProvider provideGetClassyModifiersCases
     *
     * @param array<string, null|int> $expectedModifiers
     */
    public function testGetClassyModifiers(array $expectedModifiers, int $index, string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        self::assertSame($expectedModifiers, $tokensAnalyzer->getClassyModifiers($index));
    }

    /**
     * @return iterable<string, array{array<string, null|int>, int, string}>
     */
    public static function provideGetClassyModifiersCases(): iterable
    {
        yield 'final' => [
            ['final' => 1, 'abstract' => null, 'readonly' => null],
            3,
            '<?php final class Foo {}',
        ];

        yield 'abstract' => [
            ['final' => null, 'abstract' => 3, 'readonly' => null],
            5,
            '<?php /* comment */ abstract class Foo {}',
        ];
    }

    /**
     * @requires PHP 8.2
     *
     * @dataProvider provideGetClassyModifiersOnPhp82Cases
     *
     * @param array<string, null|int> $expectedModifiers
     */
    public function testGetClassyModifiersOnPhp82(array $expectedModifiers, int $index, string $source): void
    {
        $this->testGetClassyModifiers($expectedModifiers, $index, $source);
    }

    /**
     * @return iterable<string, array{array<string, null|int>, int, string}>
     */
    public static function provideGetClassyModifiersOnPhp82Cases(): iterable
    {
        yield 'readonly' => [
            ['final' => null, 'abstract' => null, 'readonly' => 1],
            3,
            '<?php readonly class Foo {}',
        ];

        yield 'readonly final' => [
            ['final' => 3, 'abstract' => null, 'readonly' => 1],
            5,
            '<?php readonly final class Foo {}',
        ];

        yield 'readonly final reversed' => [
            ['final' => 1, 'abstract' => null, 'readonly' => 3],
            5,
            '<?php final readonly class Foo {}',
        ];

        yield 'readonly final reversed + comment' => [
            ['final' => 1, 'abstract' => null, 'readonly' => 5],
            7,
            '<?php final /* comment */ readonly class Foo {}',
        ];

        yield 'readonly abstract' => [
            ['final' => null, 'abstract' => 3, 'readonly' => 1],
            5,
            '<?php readonly abstract class Foo {}',
        ];

        yield 'readonly abstract reversed' => [
            ['final' => null, 'abstract' => 1, 'readonly' => 3],
            5,
            '<?php abstract readonly class Foo {}',
        ];

        yield 'readonly abstract reversed + comment' => [
            ['final' => null, 'abstract' => 1, 'readonly' => 5],
            7,
            '<?php abstract /* comment */ readonly class Foo {}',
        ];
    }

    public function testGetClassyModifiersForNonClassyThrowsAnException(): void
    {
        $tokens = Tokens::fromCode('<?php echo 1;');
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $this->expectException(\InvalidArgumentException::class);

        $tokensAnalyzer->getClassyModifiers(1);
    }

    /**
     * @dataProvider provideGetLastTokenIndexOfArrowFunctionCases
     *
     * @param array<int, int> $expectations
     */
    public function testGetLastTokenIndexOfArrowFunction(array $expectations, string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $indices = [];

        foreach ($expectations as $index => $expectedEndIndex) {
            $indices[$index] = $tokensAnalyzer->getLastTokenIndexOfArrowFunction($index);
        }

        self::assertSame($expectations, $indices);
    }

    /**
     * @return iterable<string, array{array<int, int>, string}>
     */
    public static function provideGetLastTokenIndexOfArrowFunctionCases(): iterable
    {
        yield 'simple cases' => [
            [
                2 => 11,
                16 => 25,
                28 => 39,
                46 => 61,
            ],
            '<?php
                fn(array $x) => $x;

                static fn(): int => $x;

                fn($x = 42) => $x;
                $eq = fn ($x, $y) => $x == $y;
            ',
        ];

        yield 'references, splat and arrow cases' => [
            [
                2 => 10,
                13 => 21,
                24 => 35,
                42 => 51,
                65 => 77,
            ],
            '<?php
                fn(&$x) => $x;
                fn&($x) => $x;
                fn($x, ...$rest) => $rest;

                $fn = fn(&$x) => $x++;
                $y = &$fn($x);
                fn($x, &...$rest) => 1;
            ',
        ];

        yield 'different endings' => [
            [
                9 => 21,
                31 => 43,
            ],
            '<?php
                $results = array_map(
                    fn ($item) => $item * 2,
                    $list
                );

                return fn ($y) => $x * $y ?>
            ',
        ];

        yield 'nested arrow function' => [
            [
                1 => 26,
                14 => 25,
            ],
            '<?php fn(array $x, $z) => (fn(int $z):bool => $z);',
        ];

        yield 'arrow function as argument' => [
            [
                5 => 14,
            ],
            '<?php return foo(fn(array $x) => $x);',
        ];

        yield 'arrow function as collection item' => [
            [
                9 => 18,
                26 => 35,
                46 => 55,
                62 => 69,
            ],
            '<?php return [
                [1, fn(array $x) => $x1, 1],
                [fn(array $x) => $x2, 1],
                [1, fn(array $x) => $x3],
                ([(fn($x4) => $x5)]),
            ];',
        ];

        yield 'nested inside anonymous class' => [
            [
                1 => 46,
                33 => 41,
            ],
            '<?php fn($x) => $a = new class($x) { public function foo() { return fn(&$x) => $x; } };',
        ];

        yield 'array destructuring' => [
            [
                4 => 13,
            ],
            '<?php return [fn(array $x) => $x1] = $x;',
        ];

        yield 'array_map() callback with different token blocks' => [
            [
                9 => 28,
            ],
            '<?php
                $a = array_map(
                    fn (array $item) => $item[\'callback\']($item[\'value\']),
                    [/* items */]
                );
            ',
        ];

        yield 'arrow function returning array' => [
            [
                5 => 21,
            ],
            '<?php $z = fn ($a) => [0, 1, $a];',
        ];
    }

    public function testCannotGetLastTokenIndexOfArrowFunctionForNonFnToken(): void
    {
        $tokens = Tokens::fromCode('<?php echo 1;');
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $this->expectException(\InvalidArgumentException::class);

        $tokensAnalyzer->getLastTokenIndexOfArrowFunction(1);
    }

    /**
     * @param array<int, bool> $expected
     */
    private function doIsConstantInvocationTest(array $expected, string $source): void
    {
        $tokens = Tokens::fromCode($source);

        self::assertCount(
            $tokens->countTokenKind(T_STRING),
            $expected,
            'All T_STRING tokens must be tested'
        );

        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($expected as $index => $expectedValue) {
            self::assertSame(
                $expectedValue,
                $tokensAnalyzer->isConstantInvocation($index),
                \sprintf('Token at index '.$index.' should match the expected value (%s).', $expectedValue ? 'true' : 'false')
            );
        }
    }
}
