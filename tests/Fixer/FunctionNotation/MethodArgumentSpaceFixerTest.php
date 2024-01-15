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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer
 */
final class MethodArgumentSpaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $indent = '    ';
        $lineEnding = "\n";

        if (str_contains($expected, "\t")) {
            $indent = "\t";
        } elseif (preg_match('/\n  \S/', $expected)) {
            $indent = '  ';
        }

        if (str_contains($expected, "\r")) {
            $lineEnding = "\r\n";
        }

        $this->fixer->configure($configuration);
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig(
            $indent,
            $lineEnding
        ));

        $this->doTest($expected, $input);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFixWithDifferentLineEndings(string $expected, ?string $input = null, array $configuration = []): void
    {
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFix(
            str_replace("\n", "\r\n", $expected),
            $input,
            $configuration
        );
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                // space
                EOD.' '.<<<'EOD'

                $var1 = $a->some_method(
                    $var2
                );

                // space
                EOD.' '.<<<'EOD'

                $var2 = some_function(
                    $var2
                );

                    // space
                EOD.'     '.<<<'EOD'

                    $var2a = $z[1](
                        $var2a
                    );
                EOD."\n    ".<<<'EOD'

                    $var3 = function(  $a, $b  ) { };

                EOD,
            <<<'EOD'
                <?php
                // space
                EOD.' '.<<<'EOD'

                $var1 = $a->some_method(
                    $var2);

                // space
                EOD.' '.<<<'EOD'

                $var2 = some_function(
                    $var2);

                    // space
                EOD.'     '.<<<'EOD'

                    $var2a = $z[1](
                        $var2a
                    );
                EOD."\n    ".<<<'EOD'

                    $var3 = function(  $a , $b  ) { };

                EOD,
            [
                'on_multiline' => 'ensure_fully_multiline',
            ],
        ];

        yield 'default' => [
            '<?php xyz("", "", "", "");',
            '<?php xyz("","","","");',
        ];

        yield 'test method arguments' => [
            '<?php function xyz($a=10, $b=20, $c=30) {}',
            '<?php function xyz($a=10,$b=20,$c=30) {}',
        ];

        yield 'test method arguments with multiple spaces' => [
            '<?php function xyz($a=10, $b=20, $c=30) {}',
            '<?php function xyz($a=10,         $b=20 , $c=30) {}',
        ];

        yield 'test method arguments with multiple spaces (kmsac)' => [
            '<?php function xyz($a=10,         $b=20, $c=30) {}',
            '<?php function xyz($a=10,         $b=20 , $c=30) {}',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'test method call (I)' => [
            '<?php xyz($a=10, $b=20, $c=30);',
            '<?php xyz($a=10 ,$b=20,$c=30);',
        ];

        yield 'test method call (II)' => [
            '<?php xyz($a=10, $b=20, $this->foo(), $c=30);',
            '<?php xyz($a=10,$b=20 ,$this->foo() ,$c=30);',
        ];

        yield 'test method call with multiple spaces (I)' => [
            '<?php xyz($a=10, $b=20, $c=30);',
            '<?php xyz($a=10 , $b=20 ,          $c=30);',
        ];

        yield 'test method call with multiple spaces (I) (kmsac)' => [
            '<?php xyz($a=10, $b=20,          $c=30);',
            '<?php xyz($a=10 , $b=20 ,          $c=30);',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'test method call with tab' => [
            '<?php xyz($a=10, $b=20, $c=30);',
            "<?php xyz(\$a=10 , \$b=20 ,\t \$c=30);",
        ];

        yield 'test method call with tab (kmsac)' => [
            "<?php xyz(\$a=10, \$b=20,\t \$c=30);",
            "<?php xyz(\$a=10 , \$b=20 ,\t \$c=30);",
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'test method call with multiple spaces (II)' => [
            '<?php xyz($a=10, $b=20, $this->foo(), $c=30);',
            '<?php xyz($a=10,$b=20 ,         $this->foo() ,$c=30);',
        ];

        yield 'test method call with multiple spaces (II) (kmsac)' => [
            '<?php xyz($a=10, $b=20,         $this->foo(), $c=30);',
            '<?php xyz($a=10,$b=20 ,         $this->foo() ,$c=30);',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'test named class constructor call' => [
            '<?php new Foo($a=10, $b=20, $this->foo(), $c=30);',
            '<?php new Foo($a=10,$b=20 ,$this->foo() ,$c=30);',
        ];

        yield 'test named class constructor call with multiple spaces' => [
            '<?php new Foo($a=10, $b=20, $c=30);',
            '<?php new Foo($a=10 , $b=20 ,          $c=30);',
        ];

        yield 'test named class constructor call with multiple spaces (kmsac)' => [
            '<?php new Foo($a=10, $b=20,          $c=30);',
            '<?php new Foo($a=10 , $b=20 ,          $c=30);',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'test anonymous class constructor call' => [
            '<?php new class ($a=10, $b=20, $this->foo(), $c=30) {};',
            '<?php new class ($a=10,$b=20 ,$this->foo() ,$c=30) {};',
        ];

        yield 'test anonymous class constructor call with multiple spaces' => [
            '<?php new class ($a=10, $b=20, $c=30) extends Foo {};',
            '<?php new class ($a=10 , $b=20 ,          $c=30) extends Foo {};',
        ];

        yield 'test anonymous class constructor call with multiple spaces (kmsac)' => [
            '<?php new class ($a=10, $b=20,          $c=30) {};',
            '<?php new class ($a=10 , $b=20 ,          $c=30) {};',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'test receiving data in list context with omitted values' => [
            '<?php list($a, $b, , , $c) = foo();',
            '<?php list($a, $b,, ,$c) = foo();',
        ];

        yield 'test receiving data in list context with omitted values and multiple spaces' => [
            '<?php list($a, $b, , , $c) = foo();',
            '<?php list($a, $b,,    ,$c) = foo();',
        ];

        yield 'test receiving data in list context with omitted values and multiple spaces (kmsac)' => [
            '<?php list($a, $b, ,    , $c) = foo();',
            '<?php list($a, $b,,    ,$c) = foo();',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'skip array' => [
            '<?php array(10 , 20 ,30); $foo = [ 10,50 , 60 ] ?>',
        ];

        yield 'list call with trailing comma' => [
            '<?php list($path, $mode, ) = foo();',
            '<?php list($path, $mode,) = foo();',
        ];

        yield 'list call with trailing comma multi line' => [
            <<<'EOD'
                <?php
                list(
                    $a,
                    $b,
                ) = foo();

                EOD,
            <<<'EOD'
                <?php
                list(
                    $a   ,
                    $b  ,
                ) = foo();

                EOD,
        ];

        yield 'inline comments with spaces' => [
            '<?php xyz($a=10, /*comment1*/ $b=2000, /*comment2*/ $c=30);',
            '<?php xyz($a=10,    /*comment1*/ $b=2000,/*comment2*/ $c=30);',
        ];

        yield 'inline comments with spaces (kmsac)' => [
            '<?php xyz($a=10,    /*comment1*/ $b=2000, /*comment2*/ $c=30);',
            '<?php xyz($a=10,    /*comment1*/ $b=2000,/*comment2*/ $c=30);',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'multi line testing method call' => [
            <<<'EOD'
                <?php if (1) {
                                xyz(
                                    $a=10,
                                    $b=20,
                                    $c=30
                                );
                                }
                EOD,
            <<<'EOD'
                <?php if (1) {
                                xyz(
                                    $a=10 ,
                                    $b=20,
                                    $c=30
                                );
                                }
                EOD,
        ];

        yield 'multi line anonymous class constructor call' => [
            <<<'EOD'
                <?php if (1) {
                                new class (
                                    $a=10,
                                    $b=20,
                                    $c=30
                                ) {};
                                }
                EOD,
            <<<'EOD'
                <?php if (1) {
                                new class (
                                    $a=10 ,
                                $b=20,$c=30) {};
                                }
                EOD,
        ];

        yield 'skip arrays but replace arg methods' => [
            '<?php fnc(1, array(2, func2(6, 7) ,4), 5);',
            '<?php fnc(1,array(2, func2(6,    7) ,4),    5);',
        ];

        yield 'skip arrays but replace arg methods (kmsac)' => [
            '<?php fnc(1, array(2, func2(6,    7) ,4),    5);',
            '<?php fnc(1,array(2, func2(6,    7) ,4),    5);',
            ['keep_multiple_spaces_after_comma' => true],
        ];

        yield 'ignore commas inside call argument' => [
            '<?php fnc(1, array(2, 3 ,4), 5);',
        ];

        yield 'skip multi line array' => [
            <<<'EOD'
                <?php
                                    array(
                                        10 ,
                                        20,
                                        30
                                    );
                EOD,
        ];

        yield 'skip short array' => [
            <<<'EOD'
                <?php
                    $foo = ["a"=>"apple", "b"=>"bed" ,"c"=>"car"];
                    $bar = ["a" ,"b" ,"c"];
                EOD."\n    ",
        ];

        yield 'don\'t change HEREDOC and NOWDOC' => [
            <<<'EOD'
                <?php if (1) {
                    $this->foo(
                        <<<EOTXTa
                    heredoc
                EOTXTa
                        ,
                        <<<'EOTXTb'
                    nowdoc
                EOTXTb
                        ,
                        'foo'
                    );
                }
                EOD,
        ];

        yield 'with_random_comments on_multiline:ignore' => [
            <<<'EOD'
                <?php xyz#
                 (#
                ""#
                ,#
                $a#
                );
                EOD,
            null,
            ['on_multiline' => 'ignore'],
        ];

        yield 'with_random_comments on_multiline:ensure_single_line' => [
            <<<'EOD'
                <?php xyz#
                 (#
                ""#
                ,#
                $a#
                );
                EOD,
            null,
            ['on_multiline' => 'ensure_single_line'],
        ];

        yield 'with_random_comments on_multiline:ensure_fully_multiline' => [
            <<<'EOD'
                <?php xyz#
                 (#
                ""#
                ,#
                $a#
                 );
                EOD,
            <<<'EOD'
                <?php xyz#
                 (#
                ""#
                ,#
                $a#
                );
                EOD,
            ['on_multiline' => 'ensure_fully_multiline'],
        ];

        yield 'test half-multiline function becomes fully-multiline' => [
            <<<'EXPECTED'
                <?php
                functionCall(
                    'a',
                    'b',
                    'c'
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                functionCall(
                    'a', 'b',
                    'c'
                );
                INPUT,
        ];

        yield 'test wrongly formatted half-multiline function becomes fully-multiline' => [
            <<<'EOD'
                <?php
                f(
                    1,
                    2,
                    3
                );
                EOD,
            <<<'EOD'
                <?php
                f(1,2,
                3);
                EOD,
        ];

        yield 'function calls with here doc cannot be anything but multiline' => [
            <<<'EXPECTED'
                <?php
                str_replace(
                    "\n",
                    PHP_EOL,
                    <<<'TEXT'
                   1) someFile.php

                TEXT
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                str_replace("\n", PHP_EOL, <<<'TEXT'
                   1) someFile.php

                TEXT
                );
                INPUT,
        ];

        yield 'test barely multiline function with blank lines becomes fully-multiline' => [
            <<<'EXPECTED'
                <?php
                functionCall(
                    'a',
                    'b',
                    'c'
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                functionCall('a', 'b',

                    'c');
                INPUT,
        ];

        yield 'test indentation is preserved' => [
            <<<'EXPECTED'
                <?php
                if (true) {
                    functionCall(
                        'a',
                        'b',
                        'c'
                    );
                }
                EXPECTED,
            <<<'INPUT'
                <?php
                if (true) {
                    functionCall(
                        'a', 'b',
                        'c'
                    );
                }
                INPUT,
        ];

        yield 'test multiline array arguments do not trigger multiline' => [
            <<<'EXPECTED'
                <?php
                defraculate(1, array(
                    'a',
                    'b',
                    'c',
                ), 42);
                EXPECTED,
        ];

        yield 'test multiline function arguments do not trigger multiline' => [
            <<<'EXPECTED'
                <?php
                defraculate(1, function () {
                    $a = 42;
                }, 42);
                EXPECTED,
        ];

        yield 'test violation after opening parenthesis' => [
            <<<'EXPECTED'
                <?php
                defraculate(
                    1,
                    2,
                    3
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                defraculate(
                    1, 2, 3);
                INPUT,
        ];

        yield 'test violation after opening parenthesis, indented with two spaces' => [
            <<<'EXPECTED'
                <?php
                defraculate(
                  1,
                  2,
                  3
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                defraculate(
                  1, 2, 3);
                INPUT,
        ];

        yield 'test violation after opening parenthesis, indented with tabs' => [
            <<<'EXPECTED'
                <?php
                defraculate(
                	1,
                	2,
                	3
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                defraculate(
                	1, 2, 3);
                INPUT,
        ];

        yield 'test violation before closing parenthesis' => [
            <<<'EXPECTED'
                <?php
                defraculate(
                    1,
                    2,
                    3
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                defraculate(1, 2, 3
                );
                INPUT,
        ];

        yield 'test violation before closing parenthesis in nested call' => [
            <<<'EXPECTED'
                <?php
                getSchwifty('rick', defraculate(
                    1,
                    2,
                    3
                ), 'morty');
                EXPECTED,
            <<<'INPUT'
                <?php
                getSchwifty('rick', defraculate(1, 2, 3
                ), 'morty');
                INPUT,
        ];

        yield 'test with comment between arguments' => [
            <<<'EXPECTED'
                <?php
                functionCall(
                    'a', /* comment */
                    'b',
                    'c'
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                functionCall(
                    'a',/* comment */'b',
                    'c'
                );
                INPUT,
        ];

        yield 'test with deeply nested arguments' => [
            <<<'EXPECTED'
                <?php
                foo(
                    'a',
                    'b',
                    [
                        'c',
                        'd', bar('e', 'f'),
                        baz(
                            'g',
                            ['h',
                                'i',
                            ]
                        ),
                    ]
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                foo('a',
                    'b',
                    [
                        'c',
                        'd', bar('e', 'f'),
                        baz('g',
                            ['h',
                                'i',
                            ]),
                    ]);
                INPUT,
        ];

        yield 'multiline string argument' => [
            <<<'UNAFFECTED'
                <?php
                $this->with('<?php
                %s
                class FooClass
                {
                }', $comment, false);
                UNAFFECTED,
        ];

        yield 'arrays with whitespace inside' => [
            <<<'UNAFFECTED'
                <?php
                $a = array/**/(  1);
                $a = array/**/( 12,
                7);
                $a = array/***/(123,  7);
                $a = array (        1,
                2);
                UNAFFECTED,
        ];

        yield 'test code that should not be affected (because not a function nor a method)' => [
            <<<'UNAFFECTED'
                <?php
                if (true &&
                    true
                    ) {
                    // do whatever
                }
                UNAFFECTED,
        ];

        yield 'test ungodly code' => [
            <<<'EXPECTED'
                <?php
                $a = function#
                (#
                #
                $a#
                #
                ,#
                #
                $b,
                    $c#
                #
                )#
                use (
                    $b1,
                    $c1,
                    $d1
                ) {
                };
                EXPECTED,
            <<<'INPUT'
                <?php
                $a = function#
                (#
                #
                $a#
                #
                ,#
                #
                $b,$c#
                #
                )#
                use ($b1,
                $c1,$d1) {
                };
                INPUT,
        ];

        yield 'test list' => [
            <<<'UNAFFECTED'
                <?php
                // no fix
                list($a,
                    $b, $c) = $a;
                isset($a,
                $b, $c);
                unset($a,
                $b, $c);
                array(1,
                    2,3
                );
                UNAFFECTED,
        ];

        yield 'test function argument with multiline echo in it' => [
            <<<'UNAFFECTED'
                <?php
                call_user_func(function ($arguments) {
                    echo 'a',
                      'b';
                }, $argv);
                UNAFFECTED,
        ];

        yield 'test function argument with oneline echo in it' => [
            <<<'EXPECTED'
                <?php
                call_user_func(
                    function ($arguments) {
                    echo 'a', 'b';
                },
                    $argv
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                call_user_func(function ($arguments) {
                    echo 'a', 'b';
                },
                $argv);
                INPUT,
        ];

        yield 'ensure_single_line' => [
            <<<'EXPECTED'
                <?php
                function foo($a, $b) {
                    // foo
                }
                foo($a, $b);
                EXPECTED,
            <<<'INPUT'
                <?php
                function foo(
                    $a,
                    $b
                ) {
                    // foo
                }
                foo(
                    $a,
                    $b
                );
                INPUT,
            ['on_multiline' => 'ensure_single_line'],
        ];

        yield 'ensure_single_line_with_random_comments' => [
            <<<'EXPECTED'
                <?php
                function foo(/* foo */// bar
                    $a, /* foo */// bar
                    $b#foo
                ) {
                    // foo
                }
                foo(/* foo */// bar
                    $a, /* foo */// bar
                    $b#foo
                );
                EXPECTED,
            null,
            ['on_multiline' => 'ensure_single_line'],
        ];

        yield 'ensure_single_line_with_consecutive_newlines' => [
            <<<'EXPECTED'
                <?php
                function foo($a, $b) {
                    // foo
                }
                foo($a, $b);
                EXPECTED,
            <<<'INPUT'
                <?php
                function foo(


                    $a,


                    $b


                ) {
                    // foo
                }
                foo(


                    $a,


                    $b


                );
                INPUT,
            ['on_multiline' => 'ensure_single_line'],
        ];

        yield 'ensure_single_line_methods' => [
            <<<'EXPECTED'
                <?php
                class Foo {
                    public static function foo1($a, $b, $c) {}
                    private function foo2($a, $b, $c) {}
                }
                EXPECTED,
            <<<'INPUT'
                <?php
                class Foo {
                    public static function foo1(
                        $a,
                        $b,
                        $c
                    ) {}
                    private function foo2(
                        $a,
                        $b,
                        $c
                    ) {}
                }
                INPUT,
            ['on_multiline' => 'ensure_single_line'],
        ];

        yield 'ensure_single_line_methods_in_anonymous_class' => [
            <<<'EXPECTED'
                <?php
                new class {
                    public static function foo1($a, $b, $c) {}
                    private function foo2($a, $b, $c) {}
                };
                EXPECTED,
            <<<'INPUT'
                <?php
                new class {
                    public static function foo1(
                        $a,
                        $b,
                        $c
                    ) {}
                    private function foo2(
                        $a,
                        $b,
                        $c
                    ) {}
                };
                INPUT,
            ['on_multiline' => 'ensure_single_line'],
        ];

        yield 'ensure_single_line_keep_spaces_after_comma' => [
            <<<'EXPECTED'
                <?php
                function foo($a,    $b) {
                    // foo
                }
                foo($a,    $b);
                EXPECTED,
            <<<'INPUT'
                <?php
                function foo(
                    $a,
                    $b
                ) {
                    // foo
                }
                foo(
                    $a,
                    $b
                );
                INPUT,
            [
                'on_multiline' => 'ensure_single_line',
                'keep_multiple_spaces_after_comma' => true,
            ],
        ];

        yield 'fix closing parenthesis (without trailing comma)' => [
            <<<'EOD'
                <?php
                if (true) {
                    execute(
                        $foo,
                        $bar
                    );
                }
                EOD,
            <<<'EOD'
                <?php
                if (true) {
                    execute(
                        $foo,
                        $bar
                        );
                }
                EOD,
            [
                'on_multiline' => 'ensure_fully_multiline',
            ],
        ];

        yield 'test anonymous functions' => [
            <<<'EOD'
                <?php
                $example = function () use ($message1, $message2) {
                };
                EOD,
            <<<'EOD'
                <?php
                $example = function () use ($message1,$message2) {
                };
                EOD,
        ];

        yield 'test first element in same line, space before comma and inconsistent indent' => [
            <<<'EOD'
                <?php foo(
                    "aaa
                    bbb",
                    $c,
                    $d,
                    $e,
                    $f
                );

                EOD,
            <<<'EOD'
                <?php foo("aaa
                    bbb",
                    $c, $d ,
                        $e,
                        $f);

                EOD,
        ];

        yield 'test first element in same line, space before comma and inconsistent indent with comments' => [
            <<<'EOD'
                <?php foo(
                    "aaa
                    bbb", // comment1
                    $c, /** comment2 */
                    $d,
                    $e/* comment3 */,
                    $f
                );# comment4

                EOD,
            <<<'EOD'
                <?php foo("aaa
                    bbb", // comment1
                    $c, /** comment2 */$d ,
                        $e/* comment3 */,
                        $f);# comment4

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                foo(
                    /* bar */
                    "baz"
                );
                EOD."\n            ",
            <<<'EOD'
                <?php
                foo(
                    /* bar */ "baz"
                );
                EOD."\n            ",
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix2Cases
     */
    public function testFix2(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix2Cases(): iterable
    {
        yield [
            '<?php function A($c, ...$a){}',
            '<?php function A($c ,...$a){}',
        ];

        yield [
            <<<'EXPECTED'
                <?php
                foo(
                    <<<'EOD'
                        bar
                        EOD,
                    'baz'
                );
                EXPECTED,
            <<<'INPUT'
                <?php
                foo(
                    <<<'EOD'
                        bar
                        EOD
                    ,
                    'baz'
                );
                INPUT,
            ['after_heredoc' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php
                foo(
                    $bar,
                    $baz,
                );
                EXPECTED,
            null,
            ['on_multiline' => 'ensure_fully_multiline'],
        ];

        yield [
            <<<'EOD'
                <?php
                functionCall(
                    1,
                    2,
                    3,
                );
                EOD,
            <<<'EOD'
                <?php
                functionCall(
                    1, 2,
                    3,
                );
                EOD,
            [
                'on_multiline' => 'ensure_fully_multiline',
            ],
        ];

        yield [
            '<?php foo(1, 2, 3, );',
            '<?php foo(1,2,3,);',
        ];

        yield [
            <<<'EOD'
                <?php
                $fn = fn(
                    $test1,
                    $test2
                ) => null;
                EOD,
            <<<'EOD'
                <?php
                $fn = fn(
                    $test1, $test2
                ) => null;
                EOD,
            [
                'on_multiline' => 'ensure_fully_multiline',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'multiple attributes' => [
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo]
                        #[Bar]
                        private ?string $name = null,
                    ) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo] #[Bar] private ?string $name = null,
                    ) {}
                }
                EOD,
        ];

        yield 'keep attributes as-is' => [
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo] #[Bar] private ?string $name = null,
                    ) {}
                }
                EOD,
            null,
            [
                'attribute_placement' => 'ignore',
            ],
        ];

        yield 'multiple attributes on the same line as argument' => [
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo] #[Bar] private ?string $name = null,
                    ) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo]
                        #[Bar]
                        private ?string $name = null,
                    ) {}
                }
                EOD,
            [
                'attribute_placement' => 'same_line',
            ],
        ];

        yield 'single attribute markup with comma separated list' => [
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo, Bar]
                        private ?string $name = null,
                    ) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo, Bar] private ?string $name = null,
                    ) {}
                }
                EOD,
        ];

        yield 'attributes with arguments' => [
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo(value: 1234, otherValue: [1, 2, 3])]
                        #[Bar(Bar::BAZ, array('[',']'))]
                        private ?string $name = null,
                    ) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class MyClass
                {
                    public function __construct(
                        private string $id,
                        #[Foo(value: 1234, otherValue: [1, 2, 3])] #[Bar(Bar::BAZ, array('[',']'))] private ?string $name = null,
                    ) {}
                }
                EOD,
        ];

        yield 'fully qualified attributes' => [
            <<<'EOD'
                <?php
                function foo(
                    #[\Foo\Bar]
                    $bar,
                    #[\Foo\Baz]
                    $baz,
                    #[\Foo\Buzz]
                    $buzz
                ) {}
                EOD,
            <<<'EOD'
                <?php
                function foo(
                    #[\Foo\Bar] $bar, #[\Foo\Baz] $baz, #[\Foo\Buzz] $buzz
                ) {}
                EOD,
        ];

        yield 'multiline attributes' => [
            <<<'EOD'
                <?php
                function foo(
                    $foo,
                    #[
                    Foo\Bar,
                    Foo\Baz,
                    Foo\Buzz(a: 'astral', b: 1234),
                ]
                    $bar
                ) {}
                EOD,
            <<<'EOD'
                <?php
                function foo($foo, #[
                    Foo\Bar,
                    Foo\Baz,
                    Foo\Buzz(a: 'astral', b: 1234),
                ] $bar) {}
                EOD,
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

    public static function provideFix81Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                [Foo::class, 'method'](
                    ...
                ) ?>
                EOD,
            <<<'EOD'
                <?php
                [Foo::class, 'method']( ...
                ) ?>
                EOD,
        ];
    }
}
