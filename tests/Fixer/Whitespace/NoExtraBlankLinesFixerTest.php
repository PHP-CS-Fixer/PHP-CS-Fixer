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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer
 */
final class NoExtraBlankLinesFixerTest extends AbstractFixerTestCase
{
    private $template = <<<'EOF'
<?php
use \DateTime;

use \stdClass;

use \InvalidArgumentException;

class Test {

    public function testThrow($a)
    {
        if ($a) {
            throw new InvalidArgumentException('test'); // test

        }
        $date = new DateTime();
        $class = new stdClass();
        $class = (string) $class;
        $e = new InvalidArgumentException($class.$date->format('Y'));
        throw $e;

    }



    public function testBreak($a)
    {
        switch($a) {
            case 1:
                echo $a;
                break;

            case 2:
                echo 'test';
                break;
        }
    }

    protected static function testContinueAndReturn($a, $b)
    {
        while($a < 100) {
            if ($b < time()) {

                continue;

            }

            return $b;

        }

        return $a;

    }

    private function test(){

        // comment
    }

    private function test123(){
        // comment
    }
}
EOF;

    /**
     * @group legacy
     * @expectedDeprecation Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.
     */
    public function testLegacyConfigNull()
    {
        $this->fixer->configure(null);

        $this->doTest($this->removeLinesFromString($this->template, [23, 24]), $this->template);
    }

    /**
     * @param int[]         $lineNumberRemoved Line numbers expected to be removed after fixing
     * @param null|string[] $config
     *
     * @group legacy
     * @dataProvider provideWithConfigCases
     * @expectedDeprecation Passing "tokens" at the root of the configuration is deprecated and will not be supported in 3.0, use "tokens" => array(...) option instead.
     */
    public function testLegacyWithConfig(array $lineNumberRemoved, array $config)
    {
        $this->fixer->configure($config);

        $this->doTest($this->removeLinesFromString($this->template, $lineNumberRemoved), $this->template);
    }

    /**
     * @param int[]         $lineNumberRemoved Line numbers expected to be removed after fixing
     * @param null|string[] $config
     *
     * @dataProvider provideWithConfigCases
     */
    public function testWithConfig(array $lineNumberRemoved, array $config)
    {
        $this->fixer->configure(['tokens' => $config]);

        $this->doTest($this->removeLinesFromString($this->template, $lineNumberRemoved), $this->template);
    }

    public function provideWithConfigCases()
    {
        $tests = [
            [
                [9, 14, 21, 43, 45, 49, 53, 57],
                ['curly_brace_block'],
            ],
            [
                [3, 5],
                ['use'],
            ],
            [
                [23, 24],
                ['extra'],
            ],
            [
                [49, 53],
                ['return'],
            ],
            [
                [45],
                ['continue'],
            ],
            [
                [32],
                ['break'],
            ],
            [
                [14, 21],
                ['throw'],
            ],
        ];

        $all = [[], []];
        foreach ($tests as $test) {
            $all[0] = array_merge($test[0], $all[0]);
            $all[1] = array_merge($test[1], $all[1]);
        }
        $tests[] = $all;

        return $tests;
    }

    public function testFix()
    {
        $expected = <<<'EOF'
<?php
$a = new Bar();

$a = new FooBaz();
EOF;

        $input = <<<'EOF'
<?php
$a = new Bar();


$a = new FooBaz();
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithManyEmptyLines()
    {
        $expected = <<<'EOF'
<?php
$a = new Bar();

$a = new FooBaz();
EOF;

        $input = <<<'EOF'
<?php
$a = new Bar();






$a = new FooBaz();
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithHeredoc()
    {
        $expected = '
<?php
$b = <<<TEXT
Foo TEXT
Bar


FooFoo
TEXT;
';

        $this->doTest($expected);
    }

    public function testFixWithNowdoc()
    {
        $expected = '
<?php
$b = <<<\'TEXT\'
Foo TEXT;
Bar1}


FooFoo
TEXT;
';

        $this->doTest($expected);
    }

    public function testFixWithEncapsulatedNowdoc()
    {
        $expected = '
<?php
$b = <<<\'TEXT\'
Foo TEXT
Bar

<<<\'TEMPLATE\'
BarFooBar TEMPLATE


TEMPLATE;


FooFoo
TEXT;
';

        $this->doTest($expected);
    }

    public function testFixWithMultilineString()
    {
        $expected = <<<'EOF'
<?php
$a = 'Foo


Bar';
EOF;

        $this->doTest($expected);
    }

    public function testFixWithTrickyMultilineStrings()
    {
        $expected = <<<'EOF'
<?php
$a = 'Foo';

$b = 'Bar


Here\'s an escaped quote '

.

'


FooFoo';
EOF;

        $input = <<<'EOF'
<?php
$a = 'Foo';


$b = 'Bar


Here\'s an escaped quote '


.


'


FooFoo';
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithCommentWithQuote()
    {
        $expected = <<<'EOF'
<?php
$a = 'foo';

// my comment's must have a quote
$b = 'foobar';

$c = 'bar';
EOF;

        $input = <<<'EOF'
<?php
$a = 'foo';


// my comment's must have a quote
$b = 'foobar';


$c = 'bar';
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithTrailingInlineBlock()
    {
        $expected = "
<?php
    echo 'hello';
?>

\$a = 0;



//a

<?php

\$a = 0;

\$b = 1;

//a
?>



";
        $this->doTest($expected);
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideCommentCases
     */
    public function testFixWithComments($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideCommentCases()
    {
        return [
            [
                <<<'EOF'
<?php
//class Test
$a; //

$b;
/***/

$c;
//

$d;
EOF
                ,
                <<<'EOF'
<?php
//class Test
$a; //




$b;
/***/



$c;
//



$d;
EOF
            ],
            [
                "<?php\n//a\n\n\$a =1;",
                "<?php\n//a\n\n\n\n\$a =1;",
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideLineBreakCases
     */
    public function testFixWithLineBreaks($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideLineBreakCases()
    {
        $input = '<?php //


$a = 1;


$b = 1;
';
        $expected = '<?php //

$a = 1;

$b = 1;
';

        return [
            [
                "<?php\r\n//a\r\n\r\n\$a =1;",
                "<?php\r\n//a\r\n\r\n\r\n\r\n\$a =1;",
            ],
            [
                $expected,
                $input,
            ],
            [
                str_replace("\n", "\r\n", $expected),
                str_replace("\n", "\r\n", $input),
            ],
            [
                str_replace("\n", "\r", $input),
            ],
            [
                str_replace("\n", "\r", $expected),
            ],
        ];
    }

    public function testWrongConfig()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[no_extra_blank_lines\] Invalid configuration: The option "tokens" .*\.$/');

        $this->fixer->configure(['tokens' => ['__TEST__']]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideBetweenUseCases
     */
    public function testBetweenUse($expected, $input = null)
    {
        $this->fixer->configure(['tokens' => ['use']]);
        $this->doTest($expected, $input);
    }

    public function provideBetweenUseCases()
    {
        return [
            ['<?php use A\B;'],
            ['<?php use A\B?>'],
            ['<?php use A\B;use A\D; return 1;'],
            [
                '<?php
                    use A\B;
                    use A\C;',
                '<?php
                    use A\B;

                    use A\C;',
            ],
            [
                '<?php use A\E;use A\Z;
                    use C;
                return 1;
                ',
                '<?php use A\E;use A\Z;

                    use C;
                return 1;
                ',
            ],
            [
                '<?php
                class Test {
                    use A;

                    use B;
                }',
            ],
            [
                '<?php
                    $example = function () use ($message) { var_dump($message); };

                    $example = function () use ($message) { var_dump($message); };
                ',
            ],
        ];
    }

    public function testRemoveLinesBetweenUseStatements()
    {
        $expected = <<<'EOF'
<?php

use Zxy\Qux;
use Zoo\Bar as Bar2;
use Foo\Bar as Bar1;
use Foo\Zar\Baz;

$c = 1;

use Foo\Quxx as Quxx1;
use Foo\Zar\Quxx;

$a = new Bar1();
$a = new Bar2();
$a = new Baz();
$a = new Qux();
EOF
        ;

        $input = <<<'EOF'
<?php

use Zxy\Qux;

use Zoo\Bar as Bar2;

use Foo\Bar as Bar1;
use Foo\Zar\Baz;

$c = 1;

use Foo\Quxx as Quxx1;

use Foo\Zar\Quxx;

$a = new Bar1();
$a = new Bar2();
$a = new Baz();
$a = new Qux();
EOF
        ;

        $this->fixer->configure(['tokens' => ['use']]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideRemoveLinesBetweenUseStatements70Cases
     * @requires PHP 7.0
     */
    public function testRemoveLinesBetweenUseStatements70($expected, $input = null)
    {
        $this->fixer->configure(['tokens' => ['use']]);
        $this->doTest($expected, $input);
    }

    public function provideRemoveLinesBetweenUseStatements70Cases()
    {
        return [
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};

use function some\a\{fn_a, fn_b, fn_c};

use const some\a\{ConstA, ConstB, ConstC};
',
            ],
        ];
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideWithoutUsesCases
     */
    public function testWithoutUses($expected)
    {
        $this->fixer->configure(['tokens' => ['use']]);
        $this->doTest($expected);
    }

    public function provideWithoutUsesCases()
    {
        return [
            [
                '<?php

$c = 1;

$a = new Baz();
$a = new Qux();',
            ],
            [
                '<?php use A\B;',
            ],
            [
                '<?php use A\B?>',
            ],
            [
                '<?php use A\B;?>',
            ],
        ];
    }

    public function testRemoveBetweenUseTraits()
    {
        $this->fixer->configure(['tokens' => ['use_trait']]);
        $this->doTest(
            '<?php
            namespace T\A;
            use V;


            use W;

            class Test {
                use A;
                use B;

                private function test($b) {

                    $a = function() use ($b) { echo $b;};

                    $b = function() use ($b) { echo $b;};

                }
            }',
            '<?php
            namespace T\A;
            use V;


            use W;

            class Test {
                use A;

                use B;

                private function test($b) {

                    $a = function() use ($b) { echo $b;};

                    $b = function() use ($b) { echo $b;};

                }
            }'
        );
    }

    /**
     * @group legacy
     * @expectedDeprecation Token "useTrait" is deprecated and will be removed in 3.0, use "use_trait" instead.
     */
    public function testRemoveBetweenUseTraitsDeprecatedToken()
    {
        $this->fixer->configure(['tokens' => ['useTrait']]);
        $this->doTest(
            '<?php
            namespace T\A;
            use V;


            use W;

            class Test {
                use A;
                use B;

                private function test($b) {

                    $a = function() use ($b) { echo $b;};

                    $b = function() use ($b) { echo $b;};

                }
            }',
            '<?php
            namespace T\A;
            use V;


            use W;

            class Test {
                use A;

                use B;

                private function test($b) {

                    $a = function() use ($b) { echo $b;};

                    $b = function() use ($b) { echo $b;};

                }
            }'
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideOneAndInLineCases
     */
    public function testOneOrInLineCases($expected, $input = null)
    {
        $this->fixer->configure(['tokens' => [
            'break',
            'continue',
            'return',
            'throw',
            'curly_brace_block',
            'square_brace_block',
            'parenthesis_brace_block',
        ]]);

        $this->doTest($expected, $input);
    }

    public function provideOneAndInLineCases()
    {
        return [
            [
                "<?php\n\n\$a = function() use (\$b) { while(3<1)break; \$c = \$b[1]; while(\$b<1)continue; if (true) throw \$e; return 1; };\n\n",
            ],
            [
                "<?php throw new \\Exception('do not import');\n",
                "<?php throw new \\Exception('do not import');\n\n",
            ],
            [
                "<?php\n\n\$a = \$b{0};\n\n",
            ],
            [
                "<?php\n\n\$a->{'Test'};\nfunction test(){}\n",
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideOneAndInLine70Cases
     * @requires PHP 7.0
     */
    public function testOneOrInLine70Cases($expected, $input = null)
    {
        $this->fixer->configure(['tokens' => [
            'break',
            'continue',
            'return',
            'throw',
            'curly_brace_block',
            'square_brace_block',
            'parenthesis_brace_block',
        ]]);

        $this->doTest($expected, $input);
    }

    public function provideOneAndInLine70Cases()
    {
        return [
            [
                "<?php\n\n\$a = new class { public function a () { while(4<1)break; while(3<1)continue; if (true) throw \$e; return 1; }};\n\n",
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideBraceCases
     */
    public function testBraces(array $config = null, $expected, $input = null)
    {
        $this->fixer->configure(['tokens' => $config]);
        $this->doTest($expected, $input);
    }

    public function provideBraceCases()
    {
        return [
            [
                ['curly_brace_block'],
                "<?php function test()\n\n{}\n\necho 789;",
            ],
            [
                ['curly_brace_block'],
                "<?php switch(\$a){\ncase 1:echo 789;}",
                "<?php switch(\$a){\n   \ncase 1:echo 789;}",
            ],
            [
                ['parenthesis_brace_block'],
                '<?php
is_int(
1);
function test(
$a,
$b,
$c
)
{


}',
                '<?php
is_int(

1);
function test(

$a,
$b,
$c


)
{


}',
            ],
            [
                ['parenthesis_brace_block'],
                "<?php array(\n1,\n2,\n3,\n);",
                "<?php array(\n  \n1,\n2,\n3,\n\n\n);",
            ],
            [
                ['parenthesis_brace_block'],
                '<?php
    function a()
    {
        $b->d(e(
        ));

        foreach ($a as $x) {
        }
    }',
            ],
            [
                ['return'],
                '<?php
class Foo
{
    public function bar() {return 1;}

    public function baz() {return 2;
    }
}',
                '<?php
class Foo
{
    public function bar() {return 1;}

    public function baz() {return 2;

    }
}',
            ],
            [
                ['square_brace_block'],
                "<?php \$c = \$b[0];\n\n\n\$a = [\n   1,\n2];\necho 1;\n\$b = [];\n\n\n//a\n",
                "<?php \$c = \$b[0];\n\n\n\$a = [\n\n   1,\n2];\necho 1;\n\$b = [];\n\n\n//a\n",
            ],
        ];
    }

    /**
     * @param array       $config
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(array $config, $expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                [],
                "<?php\r\nuse AAA;\r\n\r\nuse BBB;\r\n\r\n",
                "<?php\r\nuse AAA;\r\n\r\n\r\n\r\nuse BBB;\r\n\r\n",
            ],
            [
                ['tokens' => ['parenthesis_brace_block']],
                "<?php is_int(\r\n1);",
                "<?php is_int(\r\n\r\n\r\n\r\n1);",
            ],
            [
                ['tokens' => ['square_brace_block']],
                "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n   1,\r\n2];\r\necho 1;\r\n\$b = [];\r\n\r\n\r\n//a\r\n",
                "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n\r\n   1,\r\n2];\r\necho 1;\r\n\$b = [];\r\n\r\n\r\n//a\r\n",
            ],
            [
                ['tokens' => ['square_brace_block']],
                "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n\t1,\r\n2];",
                "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n\r\n\t1,\r\n2];",
            ],
        ];
    }

    /**
     * @param array       $config
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideSwitchCases
     */
    public function testInSwitchStatement(array $config, $expected, $input = null)
    {
        $this->fixer->configure(['tokens' => $config]);
        $this->doTest($expected, $input);
    }

    public function provideSwitchCases()
    {
        return [
            [
                [
                    'break',
                    'continue',
                    'extra',
                    'return',
                    'throw',
                ],
                '<?php
                    /** a  */
                    switch ($a) {
                        case 1:
                            break;
                        case 2:
                            continue;
                        case 3:
                            return 1;
                        case 4:
                            throw $e;
                        case 5:
                            throw new \Exception();
                        case Token::STRING_TYPE:
                            echo 123;

                            return new ConstantNode($token->getValue());
                        case 7:
                            return new ConstantNode($token->getValue());
                        case 8:
                            return 8;
                        default:
                            echo 1;
                    }',
                '<?php
                    /** a  */
                    switch ($a) {
                        case 1:
                            break;

                        case 2:
                            continue;

                        case 3:
                            return 1;

                        case 4:
                            throw $e;

                        case 5:
                            throw new \Exception();

                        case Token::STRING_TYPE:
                            echo 123;

                            return new ConstantNode($token->getValue());

                        case 7:
                            return new ConstantNode($token->getValue());
        '.'
                        case 8:
                            return 8;
                        '.'
                        default:
                            echo 1;
                    }',
            ],
            [
                [
                    'switch',
                    'case',
                    'default',
                ],
                '<?php
                    switch($a) {
                        case 0:
                        case 1:
                        default:
                            return 1;
                    }',
                '<?php
                    switch($a) {

                        case 0:

                        case 1:

                        default:

                            return 1;
                    }',
            ],
            [
                [
                    'switch',
                    'case',
                    'default',
                ],
                '<?php
                    switch($a) { case 2: echo 3;
                    default: return 1;}


                    // above stays empty',
                '<?php
                    switch($a) { case 2: echo 3;

                    default: return 1;}


                    // above stays empty',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix72Cases
     * @requires PHP 7.2
     */
    public function testFix72($expected, $input)
    {
        $this->fixer->configure(['tokens' => ['use']]);
        $this->doTest($expected, $input);
    }

    public function provideFix72Cases()
    {
        return [
            [
                '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA,ConstB,ConstC
,
};
use const some\Z\{ConstA,ConstB,ConstC,};
',
                '<?php
use some\a\{ClassA, ClassB, ClassC as C,};


use function some\a\{fn_a, fn_b, fn_c,};

use const some\a\{ConstA,ConstB,ConstC
,
};
  '.'
use const some\Z\{ConstA,ConstB,ConstC,};
',
            ],
        ];
    }

    private function removeLinesFromString($input, array $lineNumbers)
    {
        sort($lineNumbers);
        $lines = explode("\n", $input);
        foreach ($lineNumbers as $lineNumber) {
            --$lineNumber;

            unset($lines[$lineNumber]);
        }

        return implode("\n", $lines);
    }
}
