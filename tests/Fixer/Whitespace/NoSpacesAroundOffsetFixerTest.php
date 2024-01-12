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
     * @dataProvider provideFixSpaceInsideOffsetCases
     */
    public function testFixSpaceInsideOffset(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixSpaceOutsideOffsetCases
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
     * @dataProvider provideCommentsCases
     */
    public function testComments(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideCommentsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php

                $withComments[0] // here is a comment
                    [1] // and here is another
                    [2] = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $a = $b[# z
                 1#z
                 ];
                EOD,
            <<<'EOD'
                <?php
                $a = $b[ # z
                 1#z
                 ];
                EOD,
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

    public static function provideFixSpaceOutsideOffsetCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                $a = $b[0]    ;
                EOD,
            <<<'EOD'
                <?php
                $a = $b   [0]    ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $a = array($b[0]     ,   $b[0]  );
                EOD,
            <<<'EOD'
                <?php
                $a = array($b      [0]     ,   $b [0]  );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $withComments[0] // here is a comment
                    [1] // and here is another
                    [2][3] = 4;
                EOD,
            <<<'EOD'
                <?php
                $withComments [0] // here is a comment
                    [1] // and here is another
                    [2] [3] = 4;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $c = SOME_CONST[0][1][2];
                EOD,
            <<<'EOD'
                <?php
                $c = SOME_CONST [0] [1]   [2];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $f = someFunc()[0][1][2];
                EOD,
            <<<'EOD'
                <?php
                $f = someFunc() [0] [1]   [2];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo[][0][1][2] = 3;
                EOD,
            <<<'EOD'
                <?php
                $foo [] [0] [1]   [2] = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo[0][1][2] = 3;
                EOD,
            <<<'EOD'
                <?php
                $foo [0] [1]   [2] = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $bar = $foo[0][1][2];
                EOD,
            <<<'EOD'
                <?php
                $bar = $foo [0] [1]   [2];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $baz[0][1][2] = 3;
                EOD,
            <<<'EOD'
                <?php
                $baz [0]
                     [1]
                     [2] = 3;
                EOD,
        ];
    }

    public static function provideFixSpaceInsideOffsetCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                $foo = array(1, 2, 3);
                $var = $foo[1];
                EOD,
            <<<'EOD'
                <?php
                $foo = array(1, 2, 3);
                $var = $foo[ 1 ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $arr = [2,   2 , ];
                $var = $arr[0];
                EOD,
            <<<'EOD'
                <?php
                $arr = [2,   2 , ];
                $var = $arr[ 0 ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $arr[2] = 3;
                EOD,
            <<<'EOD'
                <?php
                $arr[ 2    ] = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $arr[] = 3;
                EOD,
            <<<'EOD'
                <?php
                $arr[  ] = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $arr[]["some_offset"][] = 3;
                EOD,
            <<<'EOD'
                <?php
                $arr[  ][ "some_offset"   ][     ] = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $arr[]["some  offset with  spaces"][] = 3;
                EOD,
            <<<'EOD'
                <?php
                $arr[  ][ "some  offset with  spaces"   ][     ] = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = $arr[0];
                EOD,
            <<<'EOD'
                <?php
                $var = $arr[     0   ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = $arr[0][0];
                EOD,
            <<<'EOD'
                <?php
                $var = $arr[    0        ][ 0  ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = $arr[$a[$b]];
                EOD,
            <<<'EOD'
                <?php
                $var = $arr[    $a    [ $b    ]  ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = $arr[$a[$b]];
                EOD,
            <<<'EOD'
                <?php
                $var = $arr[	$a	[	$b	]	];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = $arr[0][
                     0];
                EOD,
            <<<'EOD'
                <?php
                $var = $arr[0][
                     0 ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = $arr[0][0
                         ];
                EOD,
            <<<'EOD'
                <?php
                $var = $arr[0][     0
                         ];
                EOD,
        ];
    }

    /**
     * @param array{positions: list<string>} $configuration
     *
     * @dataProvider provideFixWithConfigurationCases
     */
    public function testFixWithConfiguration(string $expected, string $input, array $configuration): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string, array<mixed>}>
     */
    public static function provideFixWithConfigurationCases(): iterable
    {
        yield 'Config "default".' => [
            <<<'EOD'
                <?php [ $a ] = $a;
                if ($controllerName = $request->attributes->get(1)) {
                    return false;
                }
                [  $class  ,   $method  ] = $this->splitControllerClassAndMethod($controllerName);
                $a = $b[0];

                EOD,
            <<<'EOD'
                <?php [ $a ] = $a;
                if ($controllerName = $request->attributes->get(1)) {
                    return false;
                }
                [  $class  ,   $method  ] = $this->splitControllerClassAndMethod($controllerName);
                $a = $b   [0];

                EOD,
            ['positions' => ['inside', 'outside']],
        ];
    }

    /**
     * @param array{positions?: list<string>} $configuration
     *
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixPre80Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                $foo{0}{1}{2} = 3;
                EOD,
            <<<'EOD'
                <?php
                $foo {0} {1}   {2} = 3;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foobar = $foo{0}[1]{2};
                EOD,
            <<<'EOD'
                <?php
                $foobar = $foo {0} [1]   {2};
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = $arr[0]{0
                         };
                EOD,
            <<<'EOD'
                <?php
                $var = $arr[0]{     0
                         };
                EOD,
        ];

        yield from self::provideMultiDimensionalArrayCases();
    }

    /**
     * @param array{positions: array<string>} $configuration
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input, array $configuration): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string, array<mixed>}>
     */
    public static function provideFix80Cases(): iterable
    {
        foreach (self::provideMultiDimensionalArrayCases() as $index => $test) {
            $test[0] = str_replace('{', '[', $test[0]);
            $test[0] = str_replace('}', ']', $test[0]);
            $test[1] = str_replace('{', '[', $test[1]);
            $test[1] = str_replace('}', ']', $test[1]);

            yield $index => $test;
        }
    }

    public function testWrongConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[no_spaces_around_offset\] Invalid configuration: The option "positions" .*\.$/');

        $this->fixer->configure(['positions' => ['foo']]);
    }

    /**
     * @return iterable<array{string, string, array{positions: array<string>}}>
     */
    private static function provideMultiDimensionalArrayCases(): iterable
    {
        yield [
            <<<'EOT'
                <?php
                $arr1[]  ["some_offset"] [] {"foo"} = 3;
                EOT,
            <<<'EOT'
                <?php
                $arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
                EOT,
            ['positions' => ['inside']],
        ];

        yield [
            <<<'EOT'
                <?php
                $arr1[  ][ "some_offset"   ][     ]{ "foo" } = 3;
                EOT,
            <<<'EOT'
                <?php
                $arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
                EOT,
            ['positions' => ['outside']],
        ];
    }
}
