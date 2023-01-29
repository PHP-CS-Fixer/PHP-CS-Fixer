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

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer
 */
final class NoSpacesAroundOffsetFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideInsideCases
     */
    public function testFixSpaceInsideOffset(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideOutsideCases
     */
    public function testFixSpaceOutsideOffset(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function testLeaveNewLinesAlone(): void
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    private function bar()
    {
        if ([1, 2, 3] && [
            'foo',
            'bar' ,
            'baz'// a comment just to mix things up
        ]) {
            return 1;
        };
    }
}
EOF;
        $this->doTest($expected);
    }

    /**
     * @dataProvider provideCommentCases
     */
    public function testCommentsCases(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideCommentCases(): array
    {
        return [
            [
                '<?php

$withComments[0] // here is a comment
    [1] // and here is another
    [2] = 3;',
            ],
            [
                '<?php
$a = $b[# z
 1#z
 ];',
                '<?php
$a = $b[ # z
 1#z
 ];',
            ],
        ];
    }

    public function testLeaveComplexString(): void
    {
        $expected = <<<'EOF'
<?php

echo "I am printing some spaces here    {$foo->bar[1]}     {$foo->bar[1]}.";
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctions(): void
    {
        $expected = <<<'EOF'
<?php

function someFunc()    {   $someVar = [];   }
EOF;
        $this->doTest($expected);
    }

    public static function provideOutsideCases(): iterable
    {
        yield from [
            [
                '<?php
$a = $b[0]    ;',
                '<?php
$a = $b   [0]    ;',
            ],
            [
                '<?php
$a = array($b[0]     ,   $b[0]  );',
                '<?php
$a = array($b      [0]     ,   $b [0]  );',
            ],
            [
                '<?php
$withComments[0] // here is a comment
    [1] // and here is another
    [2][3] = 4;',
                '<?php
$withComments [0] // here is a comment
    [1] // and here is another
    [2] [3] = 4;',
            ],
            [
                '<?php
$c = SOME_CONST[0][1][2];',
                '<?php
$c = SOME_CONST [0] [1]   [2];',
            ],
            [
                '<?php
$f = someFunc()[0][1][2];',
                '<?php
$f = someFunc() [0] [1]   [2];',
            ],
            [
                '<?php
$foo[][0][1][2] = 3;',
                '<?php
$foo [] [0] [1]   [2] = 3;',
            ],
            [
                '<?php
$foo[0][1][2] = 3;',
                '<?php
$foo [0] [1]   [2] = 3;',
            ],
            [
                '<?php
$bar = $foo[0][1][2];',
                '<?php
$bar = $foo [0] [1]   [2];',
            ],
            [
                '<?php
$baz[0][1][2] = 3;',
                '<?php
$baz [0]
     [1]
     [2] = 3;',
            ],
        ];

        if (\PHP_VERSION_ID < 8_00_00) {
            yield [
                '<?php
$foo{0}{1}{2} = 3;',
                '<?php
$foo {0} {1}   {2} = 3;',
            ];

            yield [
                '<?php
$foobar = $foo{0}[1]{2};',
                '<?php
$foobar = $foo {0} [1]   {2};',
            ];

            yield [
                '<?php
$var = $arr[0]{0
         };',
                '<?php
$var = $arr[0]{     0
         };',
            ];
        }
    }

    public static function provideInsideCases(): array
    {
        return [
            [
                '<?php
$foo = array(1, 2, 3);
$var = $foo[1];',
                '<?php
$foo = array(1, 2, 3);
$var = $foo[ 1 ];',
            ],
            [
                '<?php
$arr = [2,   2 , ];
$var = $arr[0];',
                '<?php
$arr = [2,   2 , ];
$var = $arr[ 0 ];',
            ],
            [
                '<?php
$arr[2] = 3;',
                '<?php
$arr[ 2    ] = 3;',
            ],
            [
                '<?php
$arr[] = 3;',
                '<?php
$arr[  ] = 3;',
            ],
            [
                '<?php
$arr[]["some_offset"][] = 3;',
                '<?php
$arr[  ][ "some_offset"   ][     ] = 3;',
            ],
            [
                '<?php
$arr[]["some  offset with  spaces"][] = 3;',
                '<?php
$arr[  ][ "some  offset with  spaces"   ][     ] = 3;',
            ],
            [
                '<?php
$var = $arr[0];',
                '<?php
$var = $arr[     0   ];',
            ],
            [
                '<?php
$var = $arr[0][0];',
                '<?php
$var = $arr[    0        ][ 0  ];',
            ],
            [
                '<?php
$var = $arr[$a[$b]];',
                '<?php
$var = $arr[    $a    [ $b    ]  ];',
            ],
            [
                '<?php
$var = $arr[$a[$b]];',
                '<?php
$var = $arr[	$a	[	$b	]	];',
            ],
            [
                '<?php
$var = $arr[0][
     0];',
                '<?php
$var = $arr[0][
     0 ];',
            ],
            [
                '<?php
$var = $arr[0][0
         ];',
                '<?php
$var = $arr[0][     0
         ];',
            ],
        ];
    }

    /**
     * @param list<string> $configuration
     *
     * @dataProvider provideConfigurationCases
     */
    public function testFixWithConfiguration(array $configuration, string $expected, string $input): void
    {
        $this->fixer->configure(['positions' => $configuration]);
        $this->doTest($expected, $input);
    }

    public static function provideConfigurationCases(): iterable
    {
        $tests = [
            [
                ['inside', 'outside'],
                <<<'EOT'
<?php
$arr1[]["some_offset"][]{"foo"} = 3;
EOT
                ,
                <<<'EOT'
<?php
$arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
EOT
                ,
            ],
            [
                ['inside'],
                <<<'EOT'
<?php
$arr1[]  ["some_offset"] [] {"foo"} = 3;
EOT
                ,
                <<<'EOT'
<?php
$arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
EOT
                ,
            ],
            [
                ['outside'],
                <<<'EOT'
<?php
$arr1[  ][ "some_offset"   ][     ]{ "foo" } = 3;
EOT
                ,
                <<<'EOT'
<?php
$arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
EOT
                ,
            ],
        ];

        foreach ($tests as $index => $test) {
            if (\PHP_VERSION_ID >= 8_00_00) {
                $test[1] = str_replace('{', '[', $test[1]);
                $test[1] = str_replace('}', ']', $test[1]);
                $test[2] = str_replace('{', '[', $test[2]);
                $test[2] = str_replace('}', ']', $test[2]);
            }

            yield $index => $test;
        }

        yield 'Config "default".' => [
            ['inside', 'outside'],
            '<?php [ $a ] = $a;
if ($controllerName = $request->attributes->get(1)) {
    return false;
}
[  $class  ,   $method  ] = $this->splitControllerClassAndMethod($controllerName);
$a = $b[0];
',
            '<?php [ $a ] = $a;
if ($controllerName = $request->attributes->get(1)) {
    return false;
}
[  $class  ,   $method  ] = $this->splitControllerClassAndMethod($controllerName);
$a = $b   [0];
',
        ];
    }

    public function testWrongConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[no_spaces_around_offset\] Invalid configuration: The option "positions" .*\.$/');

        $this->fixer->configure(['positions' => ['foo']]);
    }
}
