<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer;

use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Max Voloshin <voloshin.dp@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class TokensAnalyzerTest extends \PHPUnit_Framework_TestCase
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
    }
}
PHP;

        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = array_values($tokensAnalyzer->getClassyElements());

        $this->assertCount(6, $elements);
        $this->assertSame('property', $elements[0]['type']);
        $this->assertSame('property', $elements[1]['type']);
        $this->assertSame('property', $elements[2]['type']);
        $this->assertSame('property', $elements[3]['type']);
        $this->assertSame('method', $elements[4]['type']);
        $this->assertSame('method', $elements[5]['type']);
    }

    /**
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
                '<?php function foo () {}',
                array(1 => false),
            ),
            array(
                '<?php function /** foo */ foo () {}',
                array(1 => false),
            ),
            array(
                '<?php $foo = function () {}',
                array(5 => true),
            ),
            array(
                '<?php $foo = function /** foo */ () {}',
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
                '<?php $foo = function &() {}',
                array(5 => true),
            ),
        );
    }

    /**
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
        return array(
            array(
                '<?php $a++;',
                array(2 => true),
            ),
            array(
                '<?php $a--',
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
                '<?php ${"a"}++',
                array(5 => true),
            ),
            array(
                '<?php $foo->bar++',
                array(4 => true),
            ),
            array(
                '<?php $foo->{"bar"}++',
                array(6 => true),
            ),
            array(
                '<?php $a["foo"]++',
                array(5 => true),
            ),
        );
    }

    /**
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
        return array(
            array(
                '<?php ++$a;',
                array(1 => true),
            ),
            array(
                '<?php --$a',
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
     * @dataProvider provideIsUnaryPredecessorOperator56
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

    public function provideIsUnaryPredecessorOperator56()
    {
        return array(
            array(
                '<?php function foo($a, ...$b);',
                array(8 => true),
            ),
            array(
                '<?php function foo(&...$b);',
                array(5 => true, 6 => true),
            ),
            array(
                '<?php function foo(array ...$b);',
                array(7 => true),
            ),
            array(
                '<?php foo(...$a);',
                array(3 => true),
            ),
            array(
                '<?php foo($a, ...$b);',
                array(6 => true),
            ),
        );
    }

    /**
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
     * @dataProvider provideIsBinaryOperator56
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

    public function provideIsBinaryOperator56()
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
     * @dataProvider provideIsArray
     * @requires PHP 5.4
     */
    public function testIsArray($source, $tokenIndex, $isMultilineArray = false)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->assertTrue($tokensAnalyzer->isArray($tokenIndex), 'Expected to be an array.');
        $this->assertSame($isMultilineArray, $tokensAnalyzer->isArrayMultiLine($tokenIndex), sprintf('Expected %sto be a multiline array', $isMultilineArray ? '' : 'not '));
    }

    public function provideIsArray()
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
     * @dataProvider provideArrayExceptions
     */
    public function testIsNotArray($source, $tokenIndex)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->assertFalse($tokensAnalyzer->isArray($tokenIndex));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider provideArrayExceptions
     */
    public function testIsMultiLineArrayException($source, $tokenIndex)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $tokensAnalyzer->isArrayMultiLine($tokenIndex);
    }

    public function provideArrayExceptions()
    {
        $cases = array(
            array('<?php $a;', 1),
            array("<?php\n \$a = (0+1); // [0,1]", 4),
        );

        return $cases;
    }

    /**
     * @dataProvider provideGetFunctionProperties
     */
    public function testGetFunctionProperties($source, $index, $expected)
    {
        $tokens = Tokens::fromCode($source);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $attributes = $tokensAnalyzer->getMethodAttributes($index);
        $this->assertSame($expected, $attributes);
    }

    public function provideGetFunctionProperties()
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
}
