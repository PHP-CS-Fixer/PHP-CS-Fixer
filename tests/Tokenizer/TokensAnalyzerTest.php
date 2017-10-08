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

        $this->assertSame(
            array(
                9 => array(
                    'token' => $tokens[9],
                    'type' => 'property',
                ),
                14 => array(
                    'token' => $tokens[14],
                    'type' => 'property',
                ),
                19 => array(
                    'token' => $tokens[19],
                    'type' => 'property',
                ),
                28 => array(
                    'token' => $tokens[28],
                    'type' => 'property',
                ),
                42 => array(
                    'token' => $tokens[42],
                    'type' => 'const',
                ),
                53 => array(
                    'token' => $tokens[53],
                    'type' => 'method',
                ),
                83 => array(
                    'token' => $tokens[83],
                    'type' => 'method',
                ),
                140 => array(
                    'token' => $tokens[140],
                    'type' => 'method',
                ),
                164 => array(
                    'token' => $tokens[164],
                    'type' => 'const',
                ),
            ),
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
            private function A() {
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

        $this->assertSame(
            array(
                9 => array(
                    'token' => $tokens[9],
                    'type' => 'property',
                ),
                14 => array(
                    'token' => $tokens[14],
                    'type' => 'method',
                ),
                33 => array(
                    'token' => $tokens[33],
                    'type' => 'property',
                ),
                38 => array(
                    'token' => $tokens[38],
                    'type' => 'method',
                ),
                56 => array(
                    'token' => $tokens[56],
                    'type' => 'property',
                ),
                74 => array(
                    'token' => $tokens[74],
                    'type' => 'method',
                ),
            ),
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

        $this->assertSame(
            array(
                9 => array(
                    'token' => $tokens[9],
                    'type' => 'method',
                    // 'classIndex' => 1, @TODO on master
                ),
                27 => array(
                    'token' => $tokens[27],
                    'type' => 'method',
                    //'classIndex' => 21, @TODO on master
                ),
                44 => array(
                    'token' => $tokens[44],
                    'type' => 'method',
                    // 'classIndex' => 1, @TODO on master
                ),
                64 => array(
                    'token' => $tokens[64],
                    'type' => 'method',
                    // 'classIndex' => 56, @TODO on master
                ),
                82 => array(
                    'token' => $tokens[82],
                    'type' => 'method',
                    // 'classIndex' => 76, @TODO on master
                ),
                100 => array(
                    'token' => $tokens[100],
                    'type' => 'method',
                    // 'classIndex' => 94, @TODO on master
                ),
                118 => array(
                    'token' => $tokens[118],
                    'type' => 'method',
                    // 'classIndex' => 112, @TODO on master
                ),
                139 => array(
                    'token' => $tokens[139],
                    'type' => 'method',
                    // 'classIndex' => 112, @TODO on master
                ),
                170 => array(
                    'token' => $tokens[170],
                    'type' => 'method',
                    // 'classIndex' => 76, @TODO on master
                ),
                188 => array(
                    'token' => $tokens[188],
                    'type' => 'method',
                    // 'classIndex' => 182, @TODO on master
                ),
                206 => array(
                    'token' => $tokens[206],
                    'type' => 'method',
                    // 'classIndex' => 200, @TODO on master
                ),
                242 => array(
                    'token' => $tokens[242],
                    'type' => 'method',
                    // 'classIndex' => 56, @TODO on master
                ),
            ),
            $elements
        );
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
            $this->assertSame($expectedValue, $tokensAnalyzer->isAnonymousClass($index));
        }
    }

    public function provideIsAnonymousClassCases()
    {
        return array(
            array(
                '<?php class foo {}',
                array(1 => false),
            ),
            array(
                '<?php $foo = new class() {};',
                array(7 => true),
            ),
            array(
                '<?php $foo = new class() extends Foo implements Bar, Baz {};',
                array(7 => true),
            ),
            array(
                '<?php class Foo { function bar() { return new class() {}; } }',
                array(1 => false, 19 => true),
            ),
            array(
                '<?php $a = new class(new class($d->a) implements B{}) extends C{};',
                array(7 => true, 11 => true),
            ),
        );
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
            $this->assertSame($isLambda, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambdaCases()
    {
        return array(
            array(
                '<?php function foo () {};',
                array(1 => false),
            ),
            array(
                '<?php function /** foo */ foo () {};',
                array(1 => false),
            ),
            array(
                '<?php $foo = function () {};',
                array(5 => true),
            ),
            array(
                '<?php $foo = function /** foo */ () {};',
                array(5 => true),
            ),
            array(
                '<?php
preg_replace_callback(
    "/(^|[a-z])/",
    function (array $matches) {
        return "a";
    },
    $string
);',
                array(7 => true),
            ),
            array(
                '<?php $foo = function &() {};',
                array(5 => true),
            ),
        );
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
            $this->assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambda70Cases()
    {
        return array(
            array(
                '<?php
                    $a = function (): array {
                        return [];
                    };',
                array(6 => true),
            ),
            array(
                '<?php
                    function foo (): array {
                        return [];
                    };',
                array(2 => false),
            ),
        );
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
            $this->assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambda71Cases()
    {
        return array(
            array(
                '<?php
                    $a = function (): void {
                        return [];
                    };',
                array(6 => true),
            ),
            array(
                '<?php
                    function foo (): void {
                        return [];
                    };',
                array(2 => false),
            ),
            array(
                '<?php
                    $a = function (): ?int {
                        return [];
                    };',
                array(6 => true),
            ),
            array(
                '<?php
                    function foo (): ?int {
                        return [];
                    };',
                array(2 => false),
            ),
        );
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
            $this->assertSame($isUnary, $tokensAnalyzer->isUnarySuccessorOperator($index));
            if ($isUnary) {
                $this->assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
                $this->assertFalse($tokensAnalyzer->isBinaryOperator($index));
            }
        }
    }

    public function provideIsUnarySuccessorOperatorCases()
    {
        return array(
            array(
                '<?php $a++;',
                array(2 => true),
            ),
            array(
                '<?php $a--;',
                array(2 => true),
            ),
            array(
                '<?php $a ++;',
                array(3 => true),
            ),
            array(
                '<?php $a++ + 1;',
                array(2 => true, 4 => false),
            ),
            array(
                '<?php ${"a"}++;',
                array(5 => true),
            ),
            array(
                '<?php $foo->bar++;',
                array(4 => true),
            ),
            array(
                '<?php $foo->{"bar"}++;',
                array(6 => true),
            ),
            array(
                '<?php $a["foo"]++;',
                array(5 => true),
            ),
        );
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
            $this->assertSame($isUnary, $tokensAnalyzer->isUnaryPredecessorOperator($index));
            if ($isUnary) {
                $this->assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                $this->assertFalse($tokensAnalyzer->isBinaryOperator($index));
            }
        }
    }

    public function provideIsUnaryPredecessorOperatorCases()
    {
        return array(
            array(
                '<?php ++$a;',
                array(1 => true),
            ),
            array(
                '<?php --$a;',
                array(1 => true),
            ),
            array(
                '<?php -- $a;',
                array(1 => true),
            ),
            array(
                '<?php $a + ++$b;',
                array(3 => false, 5 => true),
            ),
            array(
                '<?php !!$a;',
                array(1 => true, 2 => true),
            ),
            array(
                '<?php $a = &$b;',
                array(5 => true),
            ),
            array(
                '<?php function &foo() {}',
                array(3 => true),
            ),
            array(
                '<?php @foo();',
                array(1 => true),
            ),
            array(
                '<?php foo(+ $a, -$b);',
                array(3 => true, 8 => true),
            ),
            array(
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
                array(5 => true, 11 => true, 17 => true),
            ),
        );
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsUnaryPredecessorOperator56Cases
     * @requires PHP 5.6
     */
    public function testIsUnaryPredecessorOperator56($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isUnary) {
            $this->assertSame($isUnary, $tokensAnalyzer->isUnaryPredecessorOperator($index));
            if ($isUnary) {
                $this->assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                $this->assertFalse($tokensAnalyzer->isBinaryOperator($index));
            }
        }
    }

    public function provideIsUnaryPredecessorOperator56Cases()
    {
        return array(
            array(
                '<?php function foo($a, ...$b) {}',
                array(8 => true),
            ),
            array(
                '<?php function foo(&...$b) {}',
                array(5 => true, 6 => true),
            ),
            array(
                '<?php function foo(array ...$b) {}',
                array(7 => true),
            ),
            array(
                '<?php $foo = function(...$a) {};',
                array(7 => true),
            ),
            array(
                '<?php $foo = function($a, ...$b) {};',
                array(10 => true),
            ),
        );
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
            $this->assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));
            if ($isBinary) {
                $this->assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                $this->assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperatorCases()
    {
        $cases = array(
            array(
                '<?php [] + [];',
                array(4 => true),
            ),
            array(
                '<?php $a + $b;',
                array(3 => true),
            ),
            array(
                '<?php 1 + $b;',
                array(3 => true),
            ),
            array(
                '<?php 0.2 + $b;',
                array(3 => true),
            ),
            array(
                '<?php $a[1] + $b;',
                array(6 => true),
            ),
            array(
                '<?php FOO + $b;',
                array(3 => true),
            ),
            array(
                '<?php foo() + $b;',
                array(5 => true),
            ),
            array(
                '<?php ${"foo"} + $b;',
                array(6 => true),
            ),
            array(
                '<?php $a+$b;',
                array(2 => true),
            ),
            array(
                '<?php $a /* foo */  +  /* bar */  $b;',
                array(5 => true),
            ),
            array(
                '<?php $a =
$b;',
                array(3 => true),
            ),

            array(
                '<?php $a
= $b;',
                array(3 => true),
            ),
            array(
                '<?php $a = array("b" => "c", );',
                array(3 => true, 9 => true, 12 => false),
            ),
            array(
                '<?php $a * -$b;',
                array(3 => true, 5 => false),
            ),
            array(
                '<?php $a = -2 / +5;',
                array(3 => true, 5 => false, 8 => true, 10 => false),
            ),
            array(
                '<?php $a = &$b;',
                array(3 => true, 5 => false),
            ),
            array(
                '<?php $a++ + $b;',
                array(2 => false, 4 => true),
            ),
            array(
                '<?php $a = FOO & $bar;',
                array(7 => true),
            ),
            array(
                '<?php __LINE__ - 1;',
                array(3 => true),
            ),
            array(
                '<?php `echo 1` + 1;',
                array(5 => true),
            ),
        );

        $operators = array(
            '+', '-', '*', '/', '%', '<', '>', '|', '^', '&=', '&&', '||', '.=', '/=', '==', '>=', '===', '!=',
            '<>', '!==', '<=', 'and', 'or', 'xor', '-=', '%=', '*=', '|=', '+=', '<<', '<<=', '>>', '>>=', '^',
        );
        foreach ($operators as $operator) {
            $cases[] = array(
                '<?php $a '.$operator.' $b;',
                array(3 => true),
            );
        }

        return $cases;
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsBinaryOperator56Cases
     * @requires PHP 5.6
     */
    public function testIsBinaryOperator56($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $isBinary) {
            $this->assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));
            if ($isBinary) {
                $this->assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                $this->assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperator56Cases()
    {
        return array(
            array(
                '<?php $a ** $b;',
                array(3 => true),
            ),
            array(
                '<?php $a **= $b;',
                array(3 => true),
            ),
        );
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
            $this->assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));
            if ($isBinary) {
                $this->assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                $this->assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperator70Cases()
    {
        return array(
            array(
                '<?php $a <=> $b;',
                array(3 => true),
            ),
            array(
                '<?php $a ?? $b;',
                array(3 => true),
            ),
        );
    }

    /**
     * @param string $source
     * @param int    $tokenIndex
     * @param bool   $isMultiLineArray
     *
     * @dataProvider provideIsArrayCases
     * @requires PHP 5.4
     */
    public function testIsArray($source, $tokenIndex, $isMultiLineArray = false)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->assertTrue($tokensAnalyzer->isArray($tokenIndex), 'Expected to be an array.');
        $this->assertSame($isMultiLineArray, $tokensAnalyzer->isArrayMultiLine($tokenIndex), sprintf('Expected %sto be a multiline array', $isMultiLineArray ? '' : 'not '));
    }

    public function provideIsArrayCases()
    {
        $cases = array(
            array(
                '<?php
                    array("a" => 1);
                ',
                2,
            ),
            array(
                // short array PHP 5.4 single line
                '<?php
                    ["a" => 2];
                ',
                2, false,
            ),
            array(
                '<?php
                    array(
                        "a" => 3
                    );
                ',
                2, true,
            ),
            array(
                // short array PHP 5.4 multi line
                '<?php
                    [
                        "a" => 4
                    ];
                ',
                2, true,
            ),
            array(
                '<?php
                    array(
                        "a" => array(5, 6, 7),
8 => new \Exception(\'Ellow\')
                    );
                ',
                2, true,
            ),
            array(
                // mix short array syntax
                '<?php
                    array(
                        "a" => [9, 10, 11],
12 => new \Exception(\'Ellow\')
                    );
                ',
                2, true,
            ),
            // Windows/Max EOL testing
            array(
                "<?php\r\narray('a' => 13);\r\n",
                1,
            ),
            array(
                "<?php\r\n   array(\r\n       'a' => 14,\r\n       'b' =>  15\r\n   );\r\n",
                2, true,
            ),
        );

        return $cases;
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
            $expect = in_array($index, $tokenIndexes, true);
            $this->assertSame(
                $expect,
                $tokensAnalyzer->isArray($index),
                sprintf('Expected %sarray, got @ %d "%s".', $expect ? '' : 'no ', $index, var_export($token, true))
            );
        }
    }

    public function provideIsArray71Cases()
    {
        return array(
            array(
                '<?php
                    [$a] = $z;
                    ["a" => $a, "b" => $b] = $array;
                    $c = [$d, $e] = $array[$a];
                    [[$a, $b], [$c, $d]] = $d;
                ',
                array(51, 59),
            ),
        );
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
            $this->assertSame($isBinary, $tokensAnalyzer->isBinaryOperator($index));
            if ($isBinary) {
                $this->assertFalse($tokensAnalyzer->isUnarySuccessorOperator($index));
                $this->assertFalse($tokensAnalyzer->isUnaryPredecessorOperator($index));
            }
        }
    }

    public function provideIsBinaryOperator71Cases()
    {
        return array(
            array(
                '<?php try {} catch (A | B $e) {}',
                array(11 => true),
            ),
        );
    }

    /**
     * @param string $source
     * @param int    $tokenIndex
     *
     * @dataProvider provideArrayExceptionCases
     */
    public function testIsNotArray($source, $tokenIndex)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->assertFalse($tokensAnalyzer->isArray($tokenIndex));
    }

    /**
     * @param string $source
     * @param int    $tokenIndex
     *
     * @dataProvider provideArrayExceptionCases
     */
    public function testIsMultiLineArrayException($source, $tokenIndex)
    {
        $this->setExpectedException('InvalidArgumentException');

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $tokensAnalyzer->isArrayMultiLine($tokenIndex);
    }

    public function provideArrayExceptionCases()
    {
        $cases = array(
            array('<?php $a;', 1),
            array("<?php\n \$a = (0+1); // [0,1]", 4),
            array('<?php $text = "foo $bbb[0] bar";', 8),
            array('<?php $text = "foo ${aaa[123]} bar";', 9),
        );

        return $cases;
    }

    /**
     * @param string $source
     * @param int    $index
     * @param array  $expected
     *
     * @dataProvider provideGetMethodAttributesCases
     */
    public function testGetMethodAttributes($source, $index, array $expected)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $attributes = $tokensAnalyzer->getMethodAttributes($index);
        $this->assertSame($expected, $attributes);
    }

    public function provideGetMethodAttributesCases()
    {
        $defaultAttributes = array(
            'visibility' => null,
            'static' => false,
            'abstract' => false,
            'final' => false,
        );

        $template = '
<?php
class TestClass {
    %s function a() {
        //
    }
}
';
        $cases = array();

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PRIVATE;
        $cases[] = array(sprintf($template, 'private'), 10, $attributes);

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $cases[] = array(sprintf($template, 'public'), 10, $attributes);

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PROTECTED;
        $cases[] = array(sprintf($template, 'protected'), 10, $attributes);

        $attributes = $defaultAttributes;
        $attributes['visibility'] = null;
        $attributes['static'] = true;
        $cases[] = array(sprintf($template, 'static'), 10, $attributes);

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $attributes['static'] = true;
        $attributes['final'] = true;
        $cases[] = array(sprintf($template, 'final public static'), 14, $attributes);

        $attributes = $defaultAttributes;
        $attributes['visibility'] = null;
        $attributes['abstract'] = true;
        $cases[] = array(sprintf($template, 'abstract'), 10, $attributes);

        $attributes = $defaultAttributes;
        $attributes['visibility'] = T_PUBLIC;
        $attributes['abstract'] = true;
        $cases[] = array(sprintf($template, 'abstract public'), 12, $attributes);

        $attributes = $defaultAttributes;
        $cases[] = array(sprintf($template, ''), 8, $attributes);

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

        $expected = array(
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
        );

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_WHILE)) {
                continue;
            }

            $this->assertSame(
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
        $this->assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function provideGetImportUseIndexesCases()
    {
        return array(
            array(
                array(1, 8),
                '<?php use E\F?><?php use A\B;',
            ),
            array(
                array(array(1), array(14), array(29)),
                '<?php
use T\A;
namespace A { use D\C; }
namespace b { use D\C; }
',
                true,
            ),
            array(
                array(array(1, 8)),
                '<?php use D\B; use A\C?>',
                true,
            ),
            array(
                array(1, 8),
                '<?php use D\B; use A\C?>',
            ),
            array(
                array(7, 22),
                '<?php
namespace A { use D\C; }
namespace b { use D\C; }
',
            ),
            array(
                array(3, 10, 34, 45, 54, 59, 77, 95),
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
            ),
        );
    }

    /**
     * @param string $input
     * @param bool   $perNamespace
     *
     * @dataProvider provideGetImportUseIndexes70Cases
     * @requires PHP 7.0
     */
    public function testGetImportUseIndexes70(array $expected, $input, $perNamespace = false)
    {
        $tokens = Tokens::fromCode($input);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function provideGetImportUseIndexes70Cases()
    {
        return array(
            array(
                array(1, 22, 41),
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
                ',
            ),
            array(
                array(array(1, 22, 41)),
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
                ',
                true,
            ),
        );
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
        $this->assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function provideGetImportUseIndexesPHP72Cases()
    {
        return array(
            array(
                array(1, 23, 43),
                '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA, ConstB, ConstC,};
                ',
            ),
            array(
                array(array(1, 23, 43)),
                '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA, ConstB, ConstC,};
                ',
                true,
            ),
        );
    }
}
