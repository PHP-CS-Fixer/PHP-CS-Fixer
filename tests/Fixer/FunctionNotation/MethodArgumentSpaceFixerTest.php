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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;
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
     * @param string      $expected
     * @param null|string $input
     * @param array       $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $configuration = [])
    {
        $indent = '    ';
        $lineEnding = "\n";

        if (null !== $expected) {
            if (false !== strpos($expected, "\t")) {
                $indent = "\t";
            } elseif (preg_match('/\n  \S/', $expected)) {
                $indent = '  ';
            }

            if (false !== strpos($expected, "\r")) {
                $lineEnding = "\r\n";
            }
        }

        $this->fixer->configure($configuration);
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig(
            $indent,
            $lineEnding
        ));

        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param array       $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFixWithDifferentLineEndings($expected, $input = null, array $configuration = [])
    {
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        return $this->testFix(
            str_replace("\n", "\r\n", $expected),
            $input,
            $configuration
        );
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
// space '.'
$var1 = $a->some_method(
    $var2
);

// space '.'
$var2 = some_function(
    $var2
);

    // space     '.'
    $var2a = $z[1](
        $var2a
    );
    '.'
    $var3 = function(  $a, $b  ) { };
',
                '<?php
// space '.'
$var1 = $a->some_method(
    $var2);

// space '.'
$var2 = some_function(
    $var2);

    // space     '.'
    $var2a = $z[1](
        $var2a
    );
    '.'
    $var3 = function(  $a , $b  ) { };
',
                [
                    'on_multiline' => 'ensure_fully_multiline',
                ],
            ],
            'default' => [
                '<?php xyz("", "", "", "");',
                '<?php xyz("","","","");',
            ],
            'test method arguments' => [
                '<?php function xyz($a=10, $b=20, $c=30) {}',
                '<?php function xyz($a=10,$b=20,$c=30) {}',
            ],
            'test method arguments with multiple spaces' => [
                '<?php function xyz($a=10, $b=20, $c=30) {}',
                '<?php function xyz($a=10,         $b=20 , $c=30) {}',
            ],
            'test method arguments with multiple spaces (kmsac)' => [
                '<?php function xyz($a=10,         $b=20, $c=30) {}',
                '<?php function xyz($a=10,         $b=20 , $c=30) {}',
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'test method call (I)' => [
                '<?php xyz($a=10, $b=20, $c=30);',
                '<?php xyz($a=10 ,$b=20,$c=30);',
            ],
            'test method call (II)' => [
                '<?php xyz($a=10, $b=20, $this->foo(), $c=30);',
                '<?php xyz($a=10,$b=20 ,$this->foo() ,$c=30);',
            ],
            'test method call with multiple spaces (I)' => [
                '<?php xyz($a=10, $b=20, $c=30);',
                '<?php xyz($a=10 , $b=20 ,          $c=30);',
            ],
            'test method call with multiple spaces (I) (kmsac)' => [
                '<?php xyz($a=10, $b=20,          $c=30);',
                '<?php xyz($a=10 , $b=20 ,          $c=30);',
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'test method call with tab' => [
                '<?php xyz($a=10, $b=20, $c=30);',
                "<?php xyz(\$a=10 , \$b=20 ,\t \$c=30);",
            ],
            'test method call with tab (kmsac)' => [
                "<?php xyz(\$a=10, \$b=20,\t \$c=30);",
                "<?php xyz(\$a=10 , \$b=20 ,\t \$c=30);",
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'test method call with \n not affected' => [
                "<?php xyz(\$a=10, \$b=20,\n                    \$c=30);",
            ],
            'test method call with \r\n not affected' => [
                "<?php xyz(\$a=10, \$b=20,\r\n                    \$c=30);",
            ],
            'test method call with multiple spaces (II)' => [
                '<?php xyz($a=10, $b=20, $this->foo(), $c=30);',
                '<?php xyz($a=10,$b=20 ,         $this->foo() ,$c=30);',
            ],
            'test method call with multiple spaces (II) (kmsac)' => [
                '<?php xyz($a=10, $b=20,         $this->foo(), $c=30);',
                '<?php xyz($a=10,$b=20 ,         $this->foo() ,$c=30);',
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'test receiving data in list context with omitted values' => [
                '<?php list($a, $b, , , $c) = foo();',
                '<?php list($a, $b,, ,$c) = foo();',
            ],
            'test receiving data in list context with omitted values and multiple spaces' => [
                '<?php list($a, $b, , , $c) = foo();',
                '<?php list($a, $b,,    ,$c) = foo();',
            ],
            'test receiving data in list context with omitted values and multiple spaces (kmsac)' => [
                '<?php list($a, $b, ,    , $c) = foo();',
                '<?php list($a, $b,,    ,$c) = foo();',
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'skip array' => [
                '<?php array(10 , 20 ,30); $foo = [ 10,50 , 60 ] ?>',
            ],
            'list call with trailing comma' => [
                '<?php list($path, $mode, ) = foo();',
                '<?php list($path, $mode,) = foo();',
            ],
            'list call with trailing comma multi line' => [
                '<?php
list(
    $a,
    $b,
) = foo();
',
                '<?php
list(
    $a   ,
    $b  ,
) = foo();
',
            ],
            'inline comments with spaces' => [
                '<?php xyz($a=10, /*comment1*/ $b=2000, /*comment2*/ $c=30);',
                '<?php xyz($a=10,    /*comment1*/ $b=2000,/*comment2*/ $c=30);',
            ],
            'inline comments with spaces (kmsac)' => [
                '<?php xyz($a=10,    /*comment1*/ $b=2000, /*comment2*/ $c=30);',
                '<?php xyz($a=10,    /*comment1*/ $b=2000,/*comment2*/ $c=30);',
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'must keep align comments' => [
                '<?php function xyz(
                    $a=10,      //comment1
                    $b=20,      //comment2
                    $c=30) {
                }',
            ],
            'must keep align comments (2)' => [
                '<?php function xyz(
                    $a=10,  //comment1
                    $b=2000,//comment2
                    $c=30) {
                }',
            ],
            'multiline comments also must be ignored (I)' => [
                '<?php function xyz(
                    $a=10,  /* comment1a
                               comment1b
                            */
                    $b=2000,/* comment2a
                        comment 2b
                        comment 2c */
                    $c=30) {
                }',
            ],
            'multiline comments also must be ignored (II)' => [
                '<?php
                    function xyz(
                        $a=10, /* multiline comment
                                 not at the end of line
                                */ $b=2000,
                        $a2=10 /* multiline comment
                                 not at the end of line
                                */ , $b2=2000,
                        $c=30) {
                    }',
                '<?php
                    function xyz(
                        $a=10, /* multiline comment
                                 not at the end of line
                                */ $b=2000,
                        $a2=10 /* multiline comment
                                 not at the end of line
                                */ ,$b2=2000,
                        $c=30) {
                    }',
            ],
            'multi line testing method arguments' => [
                '<?php function xyz(
                    $a=10,
                    $b=20,
                    $c=30) {
                }',
                '<?php function xyz(
                    $a=10 ,
                    $b=20,
                    $c=30) {
                }',
            ],
            'multi line testing method call' => [
                '<?php xyz(
                    $a=10,
                    $b=20,
                    $c=30
                    );',
                '<?php xyz(
                    $a=10 ,
                    $b=20,
                    $c=30
                    );',
            ],
            'skip arrays but replace arg methods' => [
                '<?php fnc(1, array(2, func2(6, 7) ,4), 5);',
                '<?php fnc(1,array(2, func2(6,    7) ,4),    5);',
            ],
            'skip arrays but replace arg methods (kmsac)' => [
                '<?php fnc(1, array(2, func2(6,    7) ,4),    5);',
                '<?php fnc(1,array(2, func2(6,    7) ,4),    5);',
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'ignore commas inside call argument' => [
                '<?php fnc(1, array(2, 3 ,4), 5);',
            ],
            'skip multi line array' => [
                '<?php
                    array(
                        10 ,
                        20,
                        30
                    );',
            ],
            'skip short array' => [
                '<?php
    $foo = ["a"=>"apple", "b"=>"bed" ,"c"=>"car"];
    $bar = ["a" ,"b" ,"c"];
    ',
            ],
            'don\'t change HEREDOC and NOWDOC' => [
                "<?php
    \$this->foo(
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
",
            ],
            'with_random_comments on_multiline:ignore' => [
                '<?php xyz#
 (#
""#
,#
$a#
);',
                null,
                ['on_multiline' => 'ignore'],
            ],
            'with_random_comments on_multiline:ensure_single_line' => [
                '<?php xyz#
 (#
""#
,#
$a#
);',
                null,
                ['on_multiline' => 'ensure_single_line'],
            ],
            'with_random_comments on_multiline:ensure_fully_multiline' => [
                '<?php xyz#
 (#
""#
,#
$a#
 );',
                '<?php xyz#
 (#
""#
,#
$a#
);',
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'keep_multiple_spaces_after_comma_with_newlines' => [
                "<?php xyz(\$a=10,\n\$b=20);",
                "<?php xyz(\$a=10,   \n\$b=20);",
                ['keep_multiple_spaces_after_comma' => true],
            ],
            'test half-multiline function becomes fully-multiline' => [
                <<<'EXPECTED'
<?php
functionCall(
    'a',
    'b',
    'c'
);
EXPECTED
                ,
                <<<'INPUT'
<?php
functionCall(
    'a', 'b',
    'c'
);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test wrongly formatted half-multiline function becomes fully-multiline' => [
                '<?php
f(
    1,
    2,
    3
);',
                '<?php
f(1,2,
3);',
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'function calls with here doc cannot be anything but multiline' => [
                <<<'EXPECTED'
<?php
str_replace(
    "\n",
    PHP_EOL,
    <<<'TEXT'
   1) someFile.php

TEXT
);
EXPECTED
                ,
                <<<'INPUT'
<?php
str_replace("\n", PHP_EOL, <<<'TEXT'
   1) someFile.php

TEXT
);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test barely multiline function with blank lines becomes fully-multiline' => [
                <<<'EXPECTED'
<?php
functionCall(
    'a',
    'b',
    'c'
);
EXPECTED
                ,
                <<<'INPUT'
<?php
functionCall('a', 'b',

    'c');
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test indentation is preserved' => [
                <<<'EXPECTED'
<?php
if (true) {
    functionCall(
        'a',
        'b',
        'c'
    );
}
EXPECTED
                ,
                <<<'INPUT'
<?php
if (true) {
    functionCall(
        'a', 'b',
        'c'
    );
}
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test multiline array arguments do not trigger multiline' => [
                <<<'EXPECTED'
<?php
defraculate(1, array(
    'a',
    'b',
    'c',
), 42);
EXPECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test multiline function arguments do not trigger multiline' => [
                <<<'EXPECTED'
<?php
defraculate(1, function () {
    $a = 42;
}, 42);
EXPECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test violation after opening parenthesis' => [
                <<<'EXPECTED'
<?php
defraculate(
    1,
    2,
    3
);
EXPECTED
                ,
                <<<'INPUT'
<?php
defraculate(
    1, 2, 3);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test violation after opening parenthesis, indented with two spaces' => [
                <<<'EXPECTED'
<?php
defraculate(
  1,
  2,
  3
);
EXPECTED
                ,
                <<<'INPUT'
<?php
defraculate(
  1, 2, 3);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test violation after opening parenthesis, indented with tabs' => [
                <<<'EXPECTED'
<?php
defraculate(
	1,
	2,
	3
);
EXPECTED
                ,
                <<<'INPUT'
<?php
defraculate(
	1, 2, 3);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test violation before closing parenthesis' => [
                <<<'EXPECTED'
<?php
defraculate(
    1,
    2,
    3
);
EXPECTED
                ,
                <<<'INPUT'
<?php
defraculate(1, 2, 3
);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test violation before closing parenthesis in nested call' => [
                <<<'EXPECTED'
<?php
getSchwifty('rick', defraculate(
    1,
    2,
    3
), 'morty');
EXPECTED
                ,
                <<<'INPUT'
<?php
getSchwifty('rick', defraculate(1, 2, 3
), 'morty');
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test with comment between arguments' => [
                <<<'EXPECTED'
<?php
functionCall(
    'a', /* comment */
    'b',
    'c'
);
EXPECTED
                ,
                <<<'INPUT'
<?php
functionCall(
    'a',/* comment */'b',
    'c'
);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test with deeply nested arguments' => [
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
EXPECTED
                ,
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
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'multiline string argument' => [
                <<<'UNAFFECTED'
<?php
$this->with('<?php
%s
class FooClass
{
}', $comment, false);
UNAFFECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'arrays with whitespace inside' => [
                <<<'UNAFFECTED'
<?php
$a = array/**/(  1);
$a = array/**/( 12,
7);
$a = array/***/(123,  7);
$a = array (        1,
2);
UNAFFECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test code that should not be affected (because not a function nor a method)' => [
                <<<'UNAFFECTED'
<?php
if (true &&
    true
    ) {
    // do whatever
}
UNAFFECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test ungodly code' => [
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
use ($b,
$c,$d) {
};
EXPECTED
                ,
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
use ($b,
$c,$d) {
};
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test list' => [
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
UNAFFECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test function argument with multiline echo in it' => [
                <<<'UNAFFECTED'
<?php
call_user_func(function ($arguments) {
    echo 'a',
      'b';
}, $argv);
UNAFFECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'test function argument with oneline echo in it' => [
                <<<'EXPECTED'
<?php
call_user_func(
    function ($arguments) {
    echo 'a', 'b';
},
    $argv
);
EXPECTED
                ,
                <<<'INPUT'
<?php
call_user_func(function ($arguments) {
    echo 'a', 'b';
},
$argv);
INPUT
                ,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            'ensure_single_line' => [
                <<<'EXPECTED'
<?php
function foo($a, $b) {
    // foo
}
foo($a, $b);
EXPECTED
                ,
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
INPUT
                ,
                ['on_multiline' => 'ensure_single_line'],
            ],
            'ensure_single_line_with_random_comments' => [
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
EXPECTED
                ,
                null,
                ['on_multiline' => 'ensure_single_line'],
            ],
            'ensure_single_line_with_consecutive_newlines' => [
                <<<'EXPECTED'
<?php
function foo($a, $b) {
    // foo
}
foo($a, $b);
EXPECTED
                ,
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
INPUT
                ,
                ['on_multiline' => 'ensure_single_line'],
            ],
            'ensure_single_line_methods' => [
                <<<'EXPECTED'
<?php
class Foo {
    public static function foo1($a, $b, $c) {}
    private function foo2($a, $b, $c) {}
}
EXPECTED
                ,
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
INPUT
                ,
                ['on_multiline' => 'ensure_single_line'],
            ],
            'ensure_single_line_keep_spaces_after_comma' => [
                <<<'EXPECTED'
<?php
function foo($a,    $b) {
    // foo
}
foo($a,    $b);
EXPECTED
                ,
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
INPUT
                ,
                [
                    'on_multiline' => 'ensure_single_line',
                    'keep_multiple_spaces_after_comma' => true,
                ],
            ],
            'fix closing parenthesis (without trailing comma)' => [
                '<?php
if (true) {
    execute(
        $foo,
        $bar
    );
}',
                '<?php
if (true) {
    execute(
        $foo,
        $bar
        );
}',
                [
                    'on_multiline' => 'ensure_fully_multiline',
                ],
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix56Cases
     */
    public function testFix56($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix56Cases()
    {
        return [
            [
                '<?php function A($c, ...$a){}',
                '<?php function A($c ,...$a){}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix73Cases
     * @requires PHP 7.3
     */
    public function testFix73($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFix73Cases()
    {
        return [
            [
                <<<'EXPECTED'
<?php
foo(
    <<<'EOD'
        bar
        EOD,
    'baz'
);
EXPECTED
                ,
                <<<'INPUT'
<?php
foo(
    <<<'EOD'
        bar
        EOD
    ,
    'baz'
);
INPUT
                ,
                ['after_heredoc' => true],
            ],
            [
                <<<'EXPECTED'
<?php
foo(
    $bar,
    $baz,
);
EXPECTED
                ,
                null,
                ['on_multiline' => 'ensure_fully_multiline'],
            ],
            [
                '<?php
functionCall(
    1,
    2,
    3,
);',
                '<?php
functionCall(
    1, 2,
    3,
);',
                [
                    'on_multiline' => 'ensure_fully_multiline',
                ],
            ],
            [
                '<?php foo(1, 2, 3, );',
                '<?php foo(1,2,3,);',
            ],
        ];
    }

    /**
     * @group legacy
     * @expectedDeprecation PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer::fixSpace is deprecated and will be removed in 3.0.
     */
    public function testLegacyFixSpace()
    {
        $this->fixer->fixSpace(Tokens::fromCode('<?php xyz("", "", "", "");'), 1);
    }

    /**
     * @group legacy
     * @expectedDeprecation Option "ensure_fully_multiline" for rule "method_argument_space" is deprecated and will be removed in version 3.0. Use option "on_multiline" instead.
     */
    public function testDeprecatedEnsureFullyMultilineOption()
    {
        $this->fixer->configure([
            'ensure_fully_multiline' => true,
        ]);

        $expected = <<<'EXPECTED'
<?php
functionCall(
    'a',
    'b',
    'c'
);
EXPECTED;

        $input = <<<'INPUT'
<?php
functionCall(
    'a', 'b',
    'c'
);
INPUT;

        $this->doTest($expected, $input);
    }
}
