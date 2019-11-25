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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Max Voloshin <voloshin.dp@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\TokensAnalyzer
 */
final class TokensAnalyzerTest extends TestCase
{
    public function testGetClassyElements()
    {
        $source = <<<'PHP'
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
}

PHP;

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        static::assertSame(
            [
                9 => [
                    'token' => $tokens[9],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
                14 => [
                    'token' => $tokens[14],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
                19 => [
                    'token' => $tokens[19],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
                28 => [
                    'token' => $tokens[28],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
                42 => [
                    'token' => $tokens[42],
                    'type' => 'const',
                    'classIndex' => 1,
                ],
                53 => [
                    'token' => $tokens[53],
                    'type' => 'method',
                    'classIndex' => 1,
                ],
                83 => [
                    'token' => $tokens[83],
                    'type' => 'method',
                    'classIndex' => 1,
                ],
                140 => [
                    'token' => $tokens[140],
                    'type' => 'method',
                    'classIndex' => 1,
                ],
                164 => [
                    'token' => $tokens[164],
                    'type' => 'const',
                    'classIndex' => 158,
                ],
            ],
            $elements
        );
    }

    /**
     * @requires PHP 7.4
     */
    public function testGetClassyElementsWithNullableProperties()
    {
        $source = <<<'PHP'
<?php
class Foo
{
    public int $prop0;
    protected ?int $prop1;
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
                    'token' => $tokens[11],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
                19 => [
                    'token' => $tokens[19],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
                26 => [
                    'token' => $tokens[26],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
                41 => [
                    'token' => $tokens[41],
                    'type' => 'property',
                    'classIndex' => 1,
                ],
            ],
            $elements
        );
    }

    public function testGetClassyElementsWithAnonymousClass()
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
                    'token' => $tokens[9],
                    'type' => 'property', // $A
                    'classIndex' => 1,
                ],
                14 => [
                    'token' => $tokens[14],
                    'type' => 'method', // B
                    'classIndex' => 1,
                ],
                33 => [
                    'token' => $tokens[33],
                    'type' => 'property', // $level1
                    'classIndex' => 26,
                ],
                38 => [
                    'token' => $tokens[38],
                    'type' => 'method', // XYZ
                    'classIndex' => 26,
                ],
                56 => [
                    'token' => $tokens[56],
                    'type' => 'property', // $level2
                    'classIndex' => 50,
                ],
                74 => [
                    'token' => $tokens[74],
                    'type' => 'method', // C
                    'classIndex' => 1,
                ],
            ],
            $elements
        );
    }

    public function testGetClassyElementsWithMultipleAnonymousClass()
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
                    'token' => $tokens[9],
                    'type' => 'method',
                    'classIndex' => 1,
                ],
                27 => [
                    'token' => $tokens[27],
                    'type' => 'method',
                    'classIndex' => 21,
                ],
                44 => [
                    'token' => $tokens[44],
                    'type' => 'method',
                    'classIndex' => 1,
                ],
                64 => [
                    'token' => $tokens[64],
                    'type' => 'method',
                    'classIndex' => 56,
                ],
                82 => [
                    'token' => $tokens[82],
                    'type' => 'method',
                    'classIndex' => 76,
                ],
                100 => [
                    'token' => $tokens[100],
                    'type' => 'method',
                    'classIndex' => 94,
                ],
                118 => [
                    'token' => $tokens[118],
                    'type' => 'method',
                    'classIndex' => 112,
                ],
                139 => [
                    'token' => $tokens[139],
                    'type' => 'method',
                    'classIndex' => 112,
                ],
                170 => [
                    'token' => $tokens[170],
                    'type' => 'method',
                    'classIndex' => 76,
                ],
                188 => [
                    'token' => $tokens[188],
                    'type' => 'method',
                    'classIndex' => 182,
                ],
                206 => [
                    'token' => $tokens[206],
                    'type' => 'method',
                    'classIndex' => 200,
                ],
                242 => [
                    'token' => $tokens[242],
                    'type' => 'method',
                    'classIndex' => 56,
                ],
            ],
            $elements
        );
    }

    /**
     * @requires PHP 7.4
     */
    public function testGetClassyElements74()
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
                'token' => $tokens[$index],
                'type' => 'property',
                'classIndex' => 1,
            ];
        }

        static::assertSame($expected, $elements);
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsAnonymousClassCases
     */
    public function testIsAnonymousClass($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isAnonymousClass($index));
        }
    }

    public function provideIsAnonymousClassCases()
    {
        return [
            [
                '<?php class foo {}',
                [1 => false],
            ],
            [
                '<?php $foo = new class() {};',
                [7 => true],
            ],
            [
                '<?php $foo = new class() extends Foo implements Bar, Baz {};',
                [7 => true],
            ],
            [
                '<?php class Foo { function bar() { return new class() {}; } }',
                [1 => false, 19 => true],
            ],
            [
                '<?php $a = new class(new class($d->a) implements B{}) extends C{};',
                [7 => true, 11 => true],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsLambdaCases
     */
    public function testIsLambda($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isLambda) {
            static::assertSame($isLambda, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambdaCases()
    {
        return [
            [
                '<?php function foo () {};',
                [1 => false],
            ],
            [
                '<?php function /** foo */ foo () {};',
                [1 => false],
            ],
            [
                '<?php $foo = function () {};',
                [5 => true],
            ],
            [
                '<?php $foo = function /** foo */ () {};',
                [5 => true],
            ],
            [
                '<?php
preg_replace_callback(
    "/(^|[a-z])/",
    function (array $matches) {
        return "a";
    },
    $string
);',
                [7 => true],
            ],
            [
                '<?php $foo = function &() {};',
                [5 => true],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsLambda70Cases
     * @requires PHP 7.0
     */
    public function testIsLambda70($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambda70Cases()
    {
        return [
            [
                '<?php
                    $a = function (): array {
                        return [];
                    };',
                [6 => true],
            ],
            [
                '<?php
                    function foo (): array {
                        return [];
                    };',
                [2 => false],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsLambda74Cases
     * @requires PHP 7.4
     */
    public function testIsLambda74($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambda74Cases()
    {
        return [
            [
                '<?php $fn = fn() => [];',
                [5 => true],
            ],
            [
                '<?php $fn = fn () => [];',
                [5 => true],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsLambda71Cases
     * @requires PHP 7.1
     */
    public function testIsLambda71($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambda71Cases()
    {
        return [
            [
                '<?php
                    $a = function (): void {
                        return [];
                    };',
                [6 => true],
            ],
            [
                '<?php
                    function foo (): void {
                        return [];
                    };',
                [2 => false],
            ],
            [
                '<?php
                    $a = function (): ?int {
                        return [];
                    };',
                [6 => true],
            ],
            [
                '<?php
                    function foo (): ?int {
                        return [];
                    };',
                [2 => false],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsConstantInvocationCases
     */
    public function testIsConstantInvocation($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isConstantInvocation($index), 'Token at index '.$index.' should match the expected value.');
        }
    }

    public function provideIsConstantInvocationCases()
    {
        return [
            [
                '<?php echo FOO;',
                [3 => true],
            ],
            [
                '<?php echo \FOO;',
                [4 => true],
            ],
            [
                '<?php echo Foo\Bar\BAR;',
                [3 => false, 5 => false, 7 => true],
            ],
            [
                '<?php echo FOO ? BAR : BAZ;',
                [3 => true, 7 => true, 11 => true],
            ],
            [
                '<?php echo FOO & BAR | BAZ;',
                [3 => true, 7 => true, 11 => true],
            ],
            [
                '<?php echo FOO & $bar;',
                [3 => true],
            ],
            [
                '<?php echo $foo[BAR];',
                [5 => true],
            ],
            [
                '<?php echo FOO[BAR];',
                [3 => true, 5 => true],
            ],
            [
                '<?php func(FOO, Bar\BAZ);',
                [3 => true, 8 => true],
            ],
            [
                '<?php if (FOO && BAR) {}',
                [4 => true, 8 => true],
            ],
            [
                '<?php return FOO * X\Y\BAR;',
                [3 => true, 11 => true],
            ],
            [
                '<?php function x() { yield FOO; yield FOO => BAR; }',
                [11 => true, 16 => true, 20 => true],
            ],
            [
                '<?php switch ($a) { case FOO: break; }',
                [11 => true],
            ],
            [
                '<?php namespace FOO;',
                [3 => false],
            ],
            [
                '<?php use FOO;',
                [3 => false],
            ],
            [
                '<?php use function FOO\BAR\BAZ;',
                [5 => false, 7 => false, 9 => false],
            ],
            [
                '<?php namespace X; const FOO = 1;',
                [8 => false],
            ],
            [
                '<?php class FOO {}',
                [3 => false],
            ],
            [
                '<?php interface FOO {}',
                [3 => false],
            ],
            [
                '<?php trait FOO {}',
                [3 => false],
            ],
            [
                '<?php class x extends FOO {}',
                [7 => false],
            ],
            [
                '<?php class x implements FOO {}',
                [7 => false],
            ],
            [
                '<?php class x implements FOO, BAR, BAZ {}',
                [7 => false, 10 => false, 13 => false],
            ],
            [
                '<?php class x { const FOO = 1; }',
                [9 => false],
            ],
            [
                '<?php class x { use FOO; }',
                [9 => false],
            ],
            [
                '<?php class x { use FOO, BAR { FOO::BAZ insteadof BAR; } }',
                [9 => false, 12 => false, 16 => false, 18 => false, 22 => false],
            ],
            [
                '<?php function x (FOO $foo, BAR &$bar, BAZ ...$baz) {}',
                [6 => false, 11 => false, 17 => false],
            ],
            [
                '<?php FOO();',
                [1 => false],
            ],
            [
                '<?php FOO::x();',
                [1 => false],
            ],
            [
                '<?php x::FOO();',
                [3 => false],
            ],
            [
                '<?php $foo instanceof FOO;',
                [5 => false],
            ],
            [
                '<?php try {} catch (FOO $e) {}',
                [9 => false],
            ],
            [
                '<?php "$foo[BAR]";',
                [4 => false],
            ],
            [
                '<?php "{$foo[BAR]}";',
                [5 => true],
            ],
            [
                '<?php FOO: goto FOO;',
                [1 => false, 6 => false],
            ],
            [
                '<?php foo(E_USER_DEPRECATED | E_DEPRECATED);',
                [3 => true, 7 => true],
            ],
            [
                '<?php interface Foo extends Bar, Baz, Qux {}',
                [7 => false, 10 => false, 13 => false],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsConstantInvocation70Cases
     * @requires PHP 7.0
     */
    public function testIsConstantInvocation70($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isConstantInvocation($index), 'Token at index '.$index.' should match the expected value.');
        }
    }

    public function provideIsConstantInvocation70Cases()
    {
        return [
            [
                '<?php function x(): FOO {}',
                [8 => false],
            ],
            [
                '<?php use X\Y\{FOO, BAR as BAR2, BAZ};',
                [8 => false, 11 => false, 15 => false, 18 => false],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsConstantInvocation71Cases
     * @requires PHP 7.1
     */
    public function testIsConstantInvocation71($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            static::assertSame($expectedValue, $tokensAnalyzer->isConstantInvocation($index), 'Token at index '.$index.' should match the expected value.');
        }
    }

    public function provideIsConstantInvocation71Cases()
    {
        return [
            [
                '<?php function x(?FOO $foo) {}',
                [6 => false],
            ],
            [
                '<?php function x(): ?FOO {}',
                [9 => false],
            ],
            [
                '<?php try {} catch (FOO|BAR|BAZ $e) {}',
                [9 => false, 11 => false, 13 => false],
            ],
            [
                '<?php interface Foo { public function bar(): Baz; }',
                [16 => false],
            ],
            [
                '<?php interface Foo { public function bar(): ?Baz; }',
                [17 => false],
            ],
            [
                '<?php interface Foo { public function bar(): ?\Baz; }',
                [18 => false],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsUnarySuccessorOperatorCases
     */
    public function testIsUnarySuccessorOperator($source, array $expected)
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

    public function provideIsUnarySuccessorOperatorCases()
    {
        return [
            [
                '<?php $a++;',
                [2 => true],
            ],
            [
                '<?php $a--;',
                [2 => true],
            ],
            [
                '<?php $a ++;',
                [3 => true],
            ],
            [
                '<?php $a++ + 1;',
                [2 => true, 4 => false],
            ],
            [
                '<?php ${"a"}++;',
                [5 => true],
            ],
            [
                '<?php $foo->bar++;',
                [4 => true],
            ],
            [
                '<?php $foo->{"bar"}++;',
                [6 => true],
            ],
            [
                '<?php $a["foo"]++;',
                [5 => true],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsUnaryPredecessorOperatorCases
     */
    public function testIsUnaryPredecessorOperator($source, array $expected)
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

    public function provideIsUnaryPredecessorOperatorCases()
    {
        return [
            [
                '<?php ++$a;',
                [1 => true],
            ],
            [
                '<?php --$a;',
                [1 => true],
            ],
            [
                '<?php -- $a;',
                [1 => true],
            ],
            [
                '<?php $a + ++$b;',
                [3 => false, 5 => true],
            ],
            [
                '<?php !!$a;',
                [1 => true, 2 => true],
            ],
            [
                '<?php $a = &$b;',
                [5 => true],
            ],
            [
                '<?php function &foo() {}',
                [3 => true],
            ],
            [
                '<?php @foo();',
                [1 => true],
            ],
            [
                '<?php foo(+ $a, -$b);',
                [3 => true, 8 => true],
            ],
            [
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
                [5 => true, 11 => true, 17 => true],
            ],
            [
                '<?php function foo($a, ...$b) {}',
                [8 => true],
            ],
            [
                '<?php function foo(&...$b) {}',
                [5 => true, 6 => true],
            ],
            [
                '<?php function foo(array ...$b) {}',
                [7 => true],
            ],
            [
                '<?php $foo = function(...$a) {};',
                [7 => true],
            ],
            [
                '<?php $foo = function($a, ...$b) {};',
                [10 => true],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsBinaryOperatorCases
     */
    public function testIsBinaryOperator($source, array $expected)
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

    public function provideIsBinaryOperatorCases()
    {
        $cases = [
            [
                '<?php $a .= $b; ?>',
                [3 => true],
            ],
            [
                '<?php $a . \'a\' ?>',
                [3 => true],
            ],
            [
                '<?php $a &+ $b;',
                [3 => true],
            ],
            [
                '<?php $a && $b;',
                [3 => true],
            ],
            [
                '<?php $a & $b;',
                [3 => true],
            ],
            [
                '<?php [] + [];',
                [4 => true],
            ],
            [
                '<?php $a + $b;',
                [3 => true],
            ],
            [
                '<?php 1 + $b;',
                [3 => true],
            ],
            [
                '<?php 0.2 + $b;',
                [3 => true],
            ],
            [
                '<?php $a[1] + $b;',
                [6 => true],
            ],
            [
                '<?php FOO + $b;',
                [3 => true],
            ],
            [
                '<?php foo() + $b;',
                [5 => true],
            ],
            [
                '<?php ${"foo"} + $b;',
                [6 => true],
            ],
            [
                '<?php $a+$b;',
                [2 => true],
            ],
            [
                '<?php $a /* foo */  +  /* bar */  $b;',
                [5 => true],
            ],
            [
                '<?php $a =
$b;',
                [3 => true],
            ],

            [
                '<?php $a
= $b;',
                [3 => true],
            ],
            [
                '<?php $a = array("b" => "c", );',
                [3 => true, 9 => true, 12 => false],
            ],
            [
                '<?php $a * -$b;',
                [3 => true, 5 => false],
            ],
            [
                '<?php $a = -2 / +5;',
                [3 => true, 5 => false, 8 => true, 10 => false],
            ],
            [
                '<?php $a = &$b;',
                [3 => true, 5 => false],
            ],
            [
                '<?php $a++ + $b;',
                [2 => false, 4 => true],
            ],
            [
                '<?php $a = FOO & $bar;',
                [7 => true],
            ],
            [
                '<?php __LINE__ - 1;',
                [3 => true],
            ],
            [
                '<?php `echo 1` + 1;',
                [5 => true],
            ],
            [
                '<?php $a ** $b;',
                [3 => true],
            ],
            [
                '<?php $a **= $b;',
                [3 => true],
            ],
        ];

        $operators = [
            '+', '-', '*', '/', '%', '<', '>', '|', '^', '&=', '&&', '||', '.=', '/=', '==', '>=', '===', '!=',
            '<>', '!==', '<=', 'and', 'or', 'xor', '-=', '%=', '*=', '|=', '+=', '<<', '<<=', '>>', '>>=', '^',
        ];
        foreach ($operators as $operator) {
            $cases[] = [
                '<?php $a '.$operator.' $b;',
                [3 => true],
            ];
        }

        return $cases;
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsBinaryOperator70Cases
     * @requires PHP 7.0
     */
    public function testIsBinaryOperator70($source, array $expected)
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

    public function provideIsBinaryOperator70Cases()
    {
        return [
            [
                '<?php $a <=> $b;',
                [3 => true],
            ],
            [
                '<?php $a ?? $b;',
                [3 => true],
            ],
        ];
    }

    /**
     * @param string $source
     * @param int    $tokenIndex
     * @param bool   $isMultiLineArray
     *
     * @dataProvider provideIsArrayCases
     */
    public function testIsArray($source, $tokenIndex, $isMultiLineArray = false)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        static::assertTrue($tokensAnalyzer->isArray($tokenIndex), 'Expected to be an array.');
        static::assertSame($isMultiLineArray, $tokensAnalyzer->isArrayMultiLine($tokenIndex), sprintf('Expected %sto be a multiline array', $isMultiLineArray ? '' : 'not '));
    }

    public function provideIsArrayCases()
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
8 => new \Exception(\'Ellow\')
                    );
                ',
                2, true,
            ],
            [
                // mix short array syntax
                '<?php
                    array(
                        "a" => [9, 10, 11],
12 => new \Exception(\'Ellow\')
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
     * @param string $source
     * @param int[]  $tokenIndexes
     *
     * @dataProvider provideIsArray71Cases
     * @requires PHP 7.1
     */
    public function testIsArray71($source, $tokenIndexes)
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

    public function provideIsArray71Cases()
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
     * @param string $source
     *
     * @dataProvider provideIsBinaryOperator71Cases
     * @requires PHP 7.1
     */
    public function testIsBinaryOperator71($source, array $expected)
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

    public function provideIsBinaryOperator71Cases()
    {
        return [
            [
                '<?php try {} catch (A | B $e) {}',
                [11 => true],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsBinaryOperator74Cases
     * @requires PHP 7.4
     */
    public function testIsBinaryOperator74($source, array $expected)
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

    public function provideIsBinaryOperator74Cases()
    {
        return [
            [
                '<?php $a ??= $b;',
                [3 => true],
            ],
        ];
    }

    /**
     * @param string $source
     * @param int    $tokenIndex
     *
     * @dataProvider provideArrayExceptionsCases
     */
    public function testIsNotArray($source, $tokenIndex)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        static::assertFalse($tokensAnalyzer->isArray($tokenIndex));
    }

    /**
     * @param string $source
     * @param int    $tokenIndex
     *
     * @dataProvider provideArrayExceptionsCases
     */
    public function testIsMultiLineArrayException($source, $tokenIndex)
    {
        $this->expectException(\InvalidArgumentException::class);

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $tokensAnalyzer->isArrayMultiLine($tokenIndex);
    }

    public function provideArrayExceptionsCases()
    {
        return [
            ['<?php $a;', 1],
            ["<?php\n \$a = (0+1); // [0,1]", 4],
            ['<?php $text = "foo $bbb[0] bar";', 8],
            ['<?php $text = "foo ${aaa[123]} bar";', 9],
        ];
    }

    /**
     * @param string $source
     * @param int    $index
     *
     * @dataProvider provideGetFunctionPropertiesCases
     */
    public function testGetFunctionProperties($source, $index, array $expected)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $attributes = $tokensAnalyzer->getMethodAttributes($index);
        static::assertSame($expected, $attributes);
    }

    public function provideGetFunctionPropertiesCases()
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

    public function testIsWhilePartOfDoWhile()
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
     * @param string $input
     * @param bool   $perNamespace
     *
     * @dataProvider provideGetImportUseIndexesCases
     */
    public function testGetImportUseIndexes(array $expected, $input, $perNamespace = false)
    {
        $tokens = Tokens::fromCode($input);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        static::assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function provideGetImportUseIndexesCases()
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
        ];
    }

    /**
     * @param string $input
     * @param bool   $perNamespace
     *
     * @dataProvider provideGetImportUseIndexesPHP70Cases
     * @requires PHP 7.0
     */
    public function testGetImportUseIndexesPHP70(array $expected, $input, $perNamespace = false)
    {
        $tokens = Tokens::fromCode($input);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        static::assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function provideGetImportUseIndexesPHP70Cases()
    {
        return [
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
        ];
    }

    /**
     * @param string $input
     * @param bool   $perNamespace
     *
     * @dataProvider provideGetImportUseIndexesPHP72Cases
     * @requires PHP 7.2
     */
    public function testGetImportUseIndexesPHP72(array $expected, $input, $perNamespace = false)
    {
        $tokens = Tokens::fromCode($input);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        static::assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function provideGetImportUseIndexesPHP72Cases()
    {
        return [
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

    public function testGetClassyElementsWithMultipleNestedAnonymousClass()
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
                'token' => $tokens[13],
                'type' => 'method', // setUp
                'classIndex' => 1,
            ],
            46 => [
                'token' => $tokens[46],
                'type' => 'method', // testSomethingWithMoney
                'classIndex' => 1,
            ],
            100 => [
                'token' => $tokens[100], // const A
                'type' => 'const',
                'classIndex' => 87,
            ],
            115 => [
                'token' => $tokens[115], // const B
                'type' => 'const',
                'classIndex' => 65,
            ],
            124 => [
                'token' => $tokens[124],
                'type' => 'method', // foo
                'classIndex' => 65, // $a
            ],
            143 => [
                'token' => $tokens[143], // const AA
                'type' => 'const',
                'classIndex' => 138,
            ],
            161 => [
                'token' => $tokens[161], // const AB
                'type' => 'const',
                'classIndex' => 158,
            ],
        ], $elements);
    }
}
