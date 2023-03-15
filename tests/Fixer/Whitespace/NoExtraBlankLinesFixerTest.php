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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer
 */
final class NoExtraBlankLinesFixerTest extends AbstractFixerTestCase
{
    private string $template = <<<'EOF'
<?php
use \DateTime;

use \stdClass;

use \InvalidArgumentException;

class Test {

    public function testThrow($a)
    {
        if ($a) {
            throw new InvalidArgumentException('test.'); // test

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
     * @param list<int>    $lineNumberRemoved Line numbers expected to be removed after fixing
     * @param list<string> $config
     *
     * @dataProvider provideWithConfigCases
     */
    public function testWithConfig(array $lineNumberRemoved, array $config): void
    {
        $this->fixer->configure(['tokens' => $config]);

        $this->doTest($this->removeLinesFromString($this->template, $lineNumberRemoved), $this->template);
    }

    public static function provideWithConfigCases(): iterable
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

        yield from $tests;

        $all = [[], []];

        foreach ($tests as $test) {
            $all[0] = array_merge($test[0], $all[0]);
            $all[1] = array_merge($test[1], $all[1]);
        }

        yield $all;
    }

    public function testFix(): void
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

    public function testFixWithManyEmptyLines(): void
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

    public function testFixWithHeredoc(): void
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

    public function testFixWithNowdoc(): void
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

    public function testFixWithEncapsulatedNowdoc(): void
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

    public function testFixWithMultilineString(): void
    {
        $expected = <<<'EOF'
<?php
$a = 'Foo


Bar';
EOF;

        $this->doTest($expected);
    }

    public function testFixWithTrickyMultilineStrings(): void
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

    public function testFixWithCommentWithQuote(): void
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

    public function testFixWithTrailingInlineBlock(): void
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
     * @dataProvider provideCommentCases
     */
    public function testFixWithComments(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideCommentCases(): array
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
     * @dataProvider provideLineBreakCases
     */
    public function testFixWithLineBreaks(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideLineBreakCases(): iterable
    {
        $input = '<?php //


$a = 1;


$b = 1;
';
        $expected = '<?php //

$a = 1;

$b = 1;
';

        yield [
            "<?php\r\n//a\r\n\r\n\$a =1;",
            "<?php\r\n//a\r\n\r\n\r\n\r\n\$a =1;",
        ];

        yield [
            $expected,
            $input,
        ];

        yield [
            str_replace("\n", "\r\n", $expected),
            str_replace("\n", "\r\n", $input),
        ];

        yield [
            str_replace("\n", "\r", $input),
        ];

        yield [
            str_replace("\n", "\r", $expected),
        ];
    }

    public function testWrongConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[no_extra_blank_lines\] Invalid configuration: The option "tokens" .*\.$/');

        $this->fixer->configure(['tokens' => ['__TEST__']]);
    }

    /**
     * @dataProvider provideBetweenUseCases
     */
    public function testBetweenUse(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['tokens' => ['use']]);

        $this->doTest($expected, $input);
    }

    public static function provideBetweenUseCases(): iterable
    {
        yield from [
            ['<?php use A\B;'],
            ['<?php use A\B?>'],
            ['<?php use A\B;use A\D; return 1;'],
            ["<?php use A\\B?>\n\n<?php use D\\E\\F?>"],
            ['<?php use Y\B;use A\D; return 1;'],
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

        yield [
            '<?php
use function A; use function B;

echo 1;',
        ];

        yield [
            '<?php
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA,ConstB,ConstC
,
};
use const some\Z\{ConstX,ConstY,ConstZ,};
',
            '<?php
use some\a\{ClassA, ClassB, ClassC as C,};


use function some\a\{fn_a, fn_b, fn_c,};

use const some\a\{ConstA,ConstB,ConstC
,
};
  '.'
use const some\Z\{ConstX,ConstY,ConstZ,};
',
        ];
    }

    /**
     * @dataProvider provideRemoveLinesBetweenUseStatementsCases
     */
    public function testRemoveLinesBetweenUseStatements(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['tokens' => ['use']]);

        $this->doTest($expected, $input);
    }

    public static function provideRemoveLinesBetweenUseStatementsCases(): array
    {
        return [
            [
                <<<'EOF'
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
                ,

                <<<'EOF'
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
                ,
            ],
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
     * @dataProvider provideWithoutUsesCases
     */
    public function testWithoutUses(string $expected): void
    {
        $this->fixer->configure(['tokens' => ['use']]);

        $this->doTest($expected);
    }

    public static function provideWithoutUsesCases(): iterable
    {
        yield [
            '<?php

$c = 1;

$a = new Baz();
$a = new Qux();',
        ];

        yield [
            '<?php use A\B;',
        ];

        yield [
            '<?php use A\B?>',
        ];

        yield [
            '<?php use A\B;?>',
        ];
    }

    /**
     * @dataProvider provideRemoveBetweenUseTraitsCases
     *
     * @group legacy
     */
    public function testRemoveBetweenUseTraits(string $expected, string $input): void
    {
        $this->expectDeprecation('Option "tokens: use_trait" used in `no_extra_blank_lines` rule is deprecated, use the rule `class_attributes_separation` with `elements: trait_import` instead.');

        $this->fixer->configure(['tokens' => ['use_trait']]);

        $this->doTest($expected, $input);
    }

    public static function provideRemoveBetweenUseTraitsCases(): iterable
    {
        yield [
            '<?php
class Foo
{
    use Z; // 123
    use Bar;/* */use Baz;

    public function baz() {}

    use Bar1; use Baz1;

    public function baz1() {}
}
',
            '<?php
class Foo
{
    use Z; // 123

    use Bar;/* */use Baz;

    public function baz() {}

    use Bar1; use Baz1;

    public function baz1() {}
}
',
        ];

        yield [
            '<?php
class Foo
{
    use Bar;use Baz;
    use Bar1;use Baz1;

    public function baz() {}
}
',
            '<?php
class Foo
{
    use Bar;use Baz;

    use Bar1;use Baz1;

    public function baz() {}
}
',
        ];

        yield [
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
            }',
        ];
    }

    /**
     * @dataProvider provideOneAndInLineCases
     */
    public function testOneOrInLineCases(string $expected, ?string $input = null): void
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

    public static function provideOneAndInLineCases(): iterable
    {
        yield [
            "<?php\n\n\$a = function() use (\$b) { while(3<1)break; \$c = \$b[1]; while(\$b<1)continue; if (true) throw \$e; return 1; };\n\n",
        ];

        yield [
            "<?php throw new \\Exception('do not import.');\n",
            "<?php throw new \\Exception('do not import.');\n\n",
        ];

        yield [
            "<?php\n\n\$a = \$b[0];\n\n",
        ];

        yield [
            "<?php\n\n\$a->{'Test'};\nfunction test(){}\n",
        ];

        yield [
            "<?php\n\n\$a = new class { public function a () { while(4<1)break; while(3<1)continue; if (true) throw \$e; return 1; }};\n\n",
        ];

        if (\PHP_VERSION_ID < 8_00_00) {
            yield [
                "<?php\n\n\$a = \$b{0};\n\n",
            ];
        }
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideBraceCases
     */
    public function testBraces(array $config, string $expected, ?string $input = null): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideBraceCases(): array
    {
        return [
            [
                ['tokens' => ['curly_brace_block']],
                "<?php function test()\n\n{}\n\necho 789;",
            ],
            [
                ['tokens' => ['curly_brace_block']],
                "<?php switch(\$a){\ncase 1:echo 789;}",
                "<?php switch(\$a){\n   \ncase 1:echo 789;}",
            ],
            [
                ['tokens' => ['parenthesis_brace_block']],
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
                ['tokens' => ['parenthesis_brace_block']],
                "<?php array(\n1,\n2,\n3,\n);",
                "<?php array(\n  \n1,\n2,\n3,\n\n\n);",
            ],
            [
                ['tokens' => ['parenthesis_brace_block']],
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
                ['tokens' => ['return']],
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
                ['tokens' => ['square_brace_block']],
                "<?php \$c = \$b[0];\n\n\n\$a = [\n   1,\n2];\necho 1;\n\$b = [];\n\n\n//a\n",
                "<?php \$c = \$b[0];\n\n\n\$a = [\n\n   1,\n2];\necho 1;\n\$b = [];\n\n\n//a\n",
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(array $config, string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield [
            [],
            "<?php\r\nuse AAA;\r\n\r\nuse BBB;\r\n\r\n",
            "<?php\r\nuse AAA;\r\n\r\n\r\n\r\nuse BBB;\r\n\r\n",
        ];

        yield [
            ['tokens' => ['parenthesis_brace_block']],
            "<?php is_int(\r\n1);",
            "<?php is_int(\r\n\r\n\r\n\r\n1);",
        ];

        yield [
            ['tokens' => ['square_brace_block']],
            "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n   1,\r\n2];\r\necho 1;\r\n\$b = [];\r\n\r\n\r\n//a\r\n",
            "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n\r\n   1,\r\n2];\r\necho 1;\r\n\$b = [];\r\n\r\n\r\n//a\r\n",
        ];

        yield [
            ['tokens' => ['square_brace_block']],
            "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n\t1,\r\n2];",
            "<?php \$c = \$b[0];\r\n\r\n\r\n\$a = [\r\n\r\n\t1,\r\n2];",
        ];
    }

    /**
     * @param list<string> $config
     *
     * @dataProvider provideSwitchCases
     */
    public function testInSwitchStatement(array $config, string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['tokens' => $config]);

        $this->doTest($expected, $input);
    }

    public static function provideSwitchCases(): array
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

    public function testRemovingEmptyLinesAfterOpenTag(): void
    {
        $this->doTest(
            '<?php

class Foo {}',
            '<?php


class Foo {}'
        );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(array $config, string $expected, string $input = null): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            ['tokens' => ['throw']],
            '<?php
                $a = $bar ?? throw new \Exception();

                $a = $bar ?? throw new \Exception();

                $a = $bar ?? throw new \Exception();
            ',
        ];

        yield [
            ['tokens' => ['throw']],
            '<?php
                $a = $bar ?? throw new \Exception();

                // Now, we are going to use it!
                var_dump($a);
            ',
        ];

        yield [
            ['tokens' => ['attribute']],
            '<?php
#[Attr]
#[AttrFoo1]
#[AttrFoo2]
function foo(){}
            ',
            '<?php
#[Attr]



#[AttrFoo1]


#[AttrFoo2]

function foo(){}
            ',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input = null): void
    {
        $this->fixer->configure(['tokens' => ['case']]);

        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
enum test
{
    case Baz;

    public function foo() {
        switch (bar()) {
            case 1: echo 1; break;
            case 2: echo 2; break;
        }
    }

    case Bad;
}
',
            '<?php
enum test
{
    case Baz;

    public function foo() {
        switch (bar()) {
            case 1: echo 1; break;


            case 2: echo 2; break;
        }
    }

    case Bad;
}
',
        ];

        $expectedTemplate = '<?php
enum Foo
{
    case CASE_1;

    %s
}';

        $enumAttributes = [
            'case CASE_2;',
            'const CONST_1 = self::CASE_1;',
            'private const CONST_1 = self::CASE_1;',
            'public function bar(): void {}',
            'protected function bar(): void {}',
            'private function bar(): void {}',
            'static function bar(): void {}',
            'final function bar(): void {}',
        ];

        foreach ($enumAttributes as $enumAttribute) {
            yield [
                sprintf($expectedTemplate, $enumAttribute),
            ];
        }
    }

    /**
     * @param list<int> $lineNumbers
     */
    private function removeLinesFromString(string $input, array $lineNumbers): string
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
