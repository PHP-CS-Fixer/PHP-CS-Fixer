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

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PHPUnit\Framework\TestCase;

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
            [
                9 => [
                    'token' => $tokens[9],
                    'type' => 'property',
                ],
                14 => [
                    'token' => $tokens[14],
                    'type' => 'property',
                ],
                19 => [
                    'token' => $tokens[19],
                    'type' => 'property',
                ],
                28 => [
                    'token' => $tokens[28],
                    'type' => 'property',
                ],
                42 => [
                    'token' => $tokens[42],
                    'type' => 'const',
                ],
                53 => [
                    'token' => $tokens[53],
                    'type' => 'method',
                ],
                83 => [
                    'token' => $tokens[83],
                    'type' => 'method',
                ],
                140 => [
                    'token' => $tokens[140],
                    'type' => 'method',
                ],
                164 => [
                    'token' => $tokens[164],
                    'type' => 'const',
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
            [
                9 => [
                    'token' => $tokens[9],
                    'type' => 'property',
                ],
                14 => [
                    'token' => $tokens[14],
                    'type' => 'method',
                ],
                33 => [
                    'token' => $tokens[33],
                    'type' => 'property',
                ],
                38 => [
                    'token' => $tokens[38],
                    'type' => 'method',
                ],
                56 => [
                    'token' => $tokens[56],
                    'type' => 'property',
                ],
                74 => [
                    'token' => $tokens[74],
                    'type' => 'method',
                ],
            ],
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
            $this->assertSame($isLambda, $tokensAnalyzer->isLambda($index));
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
     * @dataProvider provideIsLambdaCases70
     * @requires PHP 7.0
     */
    public function testIsLambda70($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            $this->assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambdaCases70()
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
     * @dataProvider provideIsLambdaCases71
     * @requires PHP 7.1
     */
    public function testIsLambda71($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expectedValue) {
            $this->assertSame($expectedValue, $tokensAnalyzer->isLambda($index));
        }
    }

    public function provideIsLambdaCases71()
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
     * @dataProvider provideIsUnarySuccessorOperator
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

    public function provideIsUnarySuccessorOperator()
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
     * @dataProvider provideIsUnaryPredecessorOperator
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

    public function provideIsUnaryPredecessorOperator()
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
     * @dataProvider provideIsBinaryOperator
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

    public function provideIsBinaryOperator()
    {
        $cases = [
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
     * @dataProvider provideIsBinaryOperator70
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

    public function provideIsBinaryOperator70()
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
        $this->assertTrue($tokensAnalyzer->isArray($tokenIndex), 'Expected to be an array.');
        $this->assertSame($isMultiLineArray, $tokensAnalyzer->isArrayMultiLine($tokenIndex), sprintf('Expected %sto be a multiline array', $isMultiLineArray ? '' : 'not '));
    }

    public function provideIsArrayCases()
    {
        $cases = [
            [
                '<?php
                    array("a" => 1);
                ',
                2,
            ],
            [
                // short array PHP 5.4 single line
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
                // short array PHP 5.4 multi line
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
        return [
            [
                '<?php
                    [$a] = $z;
                    ["a" => $a, "b" => $b] = $array;
                    $c = [$d, $e] = $array[$a];
                    [[$a, $b], [$c, $d]] = $d;
                ',
                [51, 59],
            ],
        ];
    }

    /**
     * @param string $source
     * @param int    $tokenIndex
     *
     * @dataProvider provideArrayExceptions
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
     * @dataProvider provideArrayExceptions
     */
    public function testIsMultiLineArrayException($source, $tokenIndex)
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $tokensAnalyzer->isArrayMultiLine($tokenIndex);
    }

    public function provideArrayExceptions()
    {
        $cases = [
            ['<?php $a;', 1],
            ["<?php\n \$a = (0+1); // [0,1]", 4],
            ['<?php $text = "foo $bbb[0] bar";', 8],
            ['<?php $text = "foo ${aaa[123]} bar";', 9],
        ];

        return $cases;
    }

    /**
     * @param string $source
     * @param int    $index
     * @param array  $expected
     *
     * @dataProvider provideGetFunctionProperties
     */
    public function testGetFunctionProperties($source, $index, array $expected)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $attributes = $tokensAnalyzer->getMethodAttributes($index);
        $this->assertSame($expected, $attributes);
    }

    public function provideGetFunctionProperties()
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
     * @dataProvider getImportUseIndexesCases
     */
    public function testGetImportUseIndexes(array $expected, $input, $perNamespace = false)
    {
        $tokens = Tokens::fromCode($input);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function getImportUseIndexesCases()
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
     * @dataProvider getImportUseIndexesCasesPHP70
     * @requires PHP 7.0
     */
    public function testGetImportUseIndexesPHP70(array $expected, $input, $perNamespace = false)
    {
        $tokens = Tokens::fromCode($input);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->assertSame($expected, $tokensAnalyzer->getImportUseIndexes($perNamespace));
    }

    public function getImportUseIndexesCasesPHP70()
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
}
