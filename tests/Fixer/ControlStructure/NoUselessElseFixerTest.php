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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractNoUselessElseFixer
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer
 */
final class NoUselessElseFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCloseTagCases
     */
    public function testCloseTag(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideCloseTagCases(): iterable
    {
        yield [
            '<?php
                    if (true) {
                        $b = $a > 2 ? "" : die
                        ?>
                    <?php
                    } else {
                        echo 798;
                    }',
        ];

        yield [
            '<?php
                    if (true) {
                        $b = $a > 2 ? "" : die
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
        ];

        yield [
            '<?php
                    if (true) {
                        if($a) die
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
        ];

        yield [
            '<?php
                    if (true) {
                        echo 1;
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
        ];

        yield [
            '<?php
                    if (true) {
                        echo 777;
                        if(false) die ?>
                    <?php
                    } else {
                        echo 778;
                    }',
        ];

        yield [
            '<?php
                    if (true)
                        echo 3;
                    else {
                        ?><?php
                        echo 4;
                    }
                ',
        ];

        yield [
            '<?php
                    if (true)
                        echo 3;
                    '.'
                    ?><?php
                echo 4;
                ',
            '<?php
                    if (true)
                        echo 3;
                    else
                    ?><?php
                echo 4;
                ',
        ];

        yield [
            '<?php
if (true)
    echo 4;
?><?php echo 5;',
            '<?php
if (true)
    echo 4;
else?><?php echo 5;',
        ];
    }

    /**
     * @dataProvider provideFixIfElseIfElseCases
     */
    public function testFixIfElseIfElse(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixIfElseIfElseCases(): iterable
    {
        $expected =
            '<?php
                while(true) {
                    while(true) {
                        if ($provideFixIfElseIfElseCases) {
                            return;
                        } elseif($a1) {
                            if ($b) {echo 1; die;}  echo 552;
                            return 1;
                        } elseif($b) {
                            %s
                        }  '.'
                            echo 662;
                        '.'
                    }
                }
            ';

        $input =
            '<?php
                while(true) {
                    while(true) {
                        if ($provideFixIfElseIfElseCases) {
                            return;
                        } elseif($a1) {
                            if ($b) {echo 1; die;} else {echo 552;}
                            return 1;
                        } elseif($b) {
                            %s
                        } else {
                            echo 662;
                        }
                    }
                }
            ';

        yield from self::generateCases($expected, $input);

        $expected =
            '<?php
                while(true) {
                    while(true) {
                        if($a) {
                            echo 100;
                        } elseif($b) {
                            %s
                        } else {
                            echo 3;
                        }
                    }
                }
            ';

        yield from self::generateCases($expected);

        $expected =
            '<?php
                while(true) {
                    while(true) {
                        if ($a) {
                            echo 100;
                        } elseif  ($a1) {
                            echo 99887;
                        } elseif  ($b) {
                            echo $b+1; //
                            /* test */
                            %s
                        } else {
                            echo 321;
                        }
                    }
                }
            ';

        yield from self::generateCases($expected);

        yield [
            '<?php
                if ($a)
                    echo 1789;
                else if($b)
                    echo 256;
                elseif($c)
                    echo 3;
                    if ($a) {

                    }elseif($d) {
                        return 1;
                    }
                else
                    echo 4;
            ',
        ];

        yield [
            '<?php
                if ($a)
                    echo 1789;
                else if($b) {
                    echo 256;
                } elseif($c) {
                    echo 3;
                    if ($d) {
                        echo 4;
                    } elseif($e)
                        return 1;
                } else
                    echo 4;
            ',
        ];
    }

    /**
     * @dataProvider provideFixIfElseCases
     */
    public function testFixIfElse(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixIfElseCases(): iterable
    {
        $expected = '<?php
            while(true) {
                while(true) {
                    if ($a) {
                        %s
                    }  '.'
                        echo 1;
                    '.'
                }
            }
        ';

        $input = '<?php
            while(true) {
                while(true) {
                    if ($a) {
                        %s
                    } else {
                        echo 1;
                    }
                }
            }
        ';

        yield from self::generateCases($expected, $input);

        yield [
            '<?php
                if ($a) {
                    GOTO jump;
                }  '.'
                    echo 1789;
                '.'

                jump:
            ',
            '<?php
                if ($a) {
                    GOTO jump;
                } else {
                    echo 1789;
                }

                jump:
            ',
        ];
    }

    /**
     * @dataProvider provideFixNestedIfCases
     */
    public function testFixNestedIf(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixNestedIfCases(): iterable
    {
        yield [
            '<?php
                    if ($x) {
                        if ($y) {
                            return 1;
                        }  '.'
                            return 2;
                        '.'
                    }  '.'
                        return 3;
                    '.'
                ',
            '<?php
                    if ($x) {
                        if ($y) {
                            return 1;
                        } else {
                            return 2;
                        }
                    } else {
                        return 3;
                    }
                ',
        ];
    }

    /**
     * @dataProvider provideFixEmptyElseCases
     */
    public function testFixEmptyElse(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixEmptyElseCases(): iterable
    {
        yield [
            '<?php
                    if (false)
                        echo 1;
                    '.'
                ',
            '<?php
                    if (false)
                        echo 1;
                    else{}
                ',
        ];

        yield [
            '<?php if($a){}',
            '<?php if($a){}else{}',
        ];

        yield [
            '<?php if($a){ $a = ($b); }  ',
            '<?php if($a){ $a = ($b); } else {}',
        ];

        yield [
            '<?php if ($a) {;}   if ($a) {;}  /**/ if($a){}',
            '<?php if ($a) {;} else {} if ($a) {;} else {/**/} if($a){}else{}',
        ];

        yield [
            '<?php
                    if /**/($a) /**/{ //
                        /**/
                        /**/return/**/1/**/;
                        //
                    }/**/  /**/
                        /**/
                        //
                    /**/
                ',
            '<?php
                    if /**/($a) /**/{ //
                        /**/
                        /**/return/**/1/**/;
                        //
                    }/**/ else /**/{
                        /**/
                        //
                    }/**/
                ',
        ];

        yield [
            '<?php
                    if ($a) {
                        if ($b) {
                            if ($c) {
                            } elseif ($d) {
                                return;
                            }  //
                            //
                            return;
                        }  //
                        //
                        return;
                    }  //
                    //
                ',
            '<?php
                    if ($a) {
                        if ($b) {
                            if ($c) {
                            } elseif ($d) {
                                return;
                            } else {//
                            }//
                            return;
                        } else {//
                        }//
                        return;
                    } else {//
                    }//
                ',
        ];
    }

    /**
     * @dataProvider provideNegativeCases
     */
    public function testNegative(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideNegativeCases(): iterable
    {
        yield [
            '<?php
                    if ($a0) {
                        //
                    } else {
                        echo 0;
                    }
                ',
        ];

        yield [
            '<?php
                    if (false)
                        echo "a";
                    else

                    echo "a";
                ',
        ];

        yield [
            '<?php if($a2){;} else {echo 27;}',
        ];

        yield [
            '<?php if ($a3) {test();} else {echo 3;}',
        ];

        yield [
            '<?php if ($a4) {$b = function () {};} else {echo 4;}',
        ];

        yield [
            '<?php if ($a5) {$b = function () use ($a){};} else {echo 5;}',
        ];

        yield [
            '<?php
                    if ($a) {
                        if ($b) return;
                    } else {
                        echo 1;
                    }
                ',
        ];

        yield [
            '<?php
                    if ($a) {
                        if ($b) throw new \Exception();
                    } else {
                        echo 1;
                    }
                ',
        ];

        yield [
            '<?php
                    if ($a) {
                        if ($b) { throw new \Exception(); }
                    } else {
                        echo 1;
                    }
                ',
        ];

        yield [
            '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : die;
                    else
                        echo 40;

                    echo "end";
                ',
        ];

        yield [
            '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : exit(1);
                    else
                        echo 40;

                    echo "end";
                ',
        ];

        yield [
            '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : exit(1);
                    else
                        echo 4;

                    echo "end";
                ',
        ];

        yield [
            '<?php
                    if (false)
                        die;
                    elseif (true)
                        if(true)echo 777;else die;
                    else if (true)
                        die;
                    elseif (false)
                        die;
                    else
                        echo 7;
                ',
        ];

        yield [
            '<?php
                    $tmp = function($b){$b();};
                    $a =1;
                    return $tmp(function () use ($a) {
                        if ($a) {
                            $a++;
                        } else {
                            $a--;
                        }
                    });
                ',
        ];

        yield [
            '<?php
                    $tmp = function($b){$b();};
                    $a =1;
                    return $tmp(function () use ($a) {
                        if ($a) {
                            $a++;
                        } elseif($a > 2) {
                            return 1;
                        } else {
                            $a--;
                        }
                    });
                ',
        ];

        yield [
            '<?php
                    return function() {
                        if (false) {

                        } elseif (3 > 2) {

                        } else {
                            echo 1;
                        }
                    };',
        ];

        yield [
            '<?php
                    return function() {
                        if (false) {
                            return 1;
                        } elseif (3 > 2) {

                        } else {
                            echo 1;
                        }
                    };',
        ];
    }

    /**
     * @dataProvider provideNegativePhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testNegativePhp80(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideNegativePhp80Cases(): iterable
    {
        $cases = [
            '$bar = $foo1 ?? throw new \Exception($e);',
            '$callable = fn() => throw new Exception();',
            '$value = $falsableValue ?: throw new InvalidArgumentException();',
            '$value = !empty($array)
                    ? reset($array)
                    : throw new InvalidArgumentException();',
            '$a = $condition && throw new Exception();',
            '$a = $condition || throw new Exception();',
            '$a = $condition and throw new Exception();',
            '$a = $condition or throw new Exception();',
        ];

        $template = '<?php
                if ($foo) {
                    %s
                } else {
                    echo 123;
                }
            ';

        foreach ($cases as $index => $case) {
            yield [sprintf('PHP8 Negative case %d', $index) => sprintf($template, $case)];
        }
    }

    /**
     * @param list<int> $expected
     *
     * @dataProvider provideBlockDetectionCases
     */
    public function testBlockDetection(array $expected, string $source, int $index): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        $method = new \ReflectionMethod(get_parent_class($this->fixer), 'getPreviousBlock');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $index);

        self::assertSame($expected, $result);
    }

    public static function provideBlockDetectionCases(): iterable
    {
        $source = '<?php
                    if ($a)
                        echo 1;
                    elseif ($a) ///
                        echo 2;
                    else if ($b) /**/ echo 3;
                    else
                        echo 4;
                    ';

        yield [[2, 11], $source, 13];

        yield [[13, 24], $source, 26];

        yield [[13, 24], $source, 26];

        yield [[26, 39], $source, 41];

        $source = '<?php
                    if ($a) {
                        if ($b) {

                        }
                        echo 1;
                    } elseif (true) {
                        echo 2;
                    } else if (false) {
                        echo 3;
                    } elseif ($c) {
                        echo 4;
                    } else
                        echo 1;
                    ';

        yield [[2, 25], $source, 27];

        yield [[27, 40], $source, 42];

        yield [[59, 72], $source, 74];
    }

    /**
     * @dataProvider provideConditionsWithoutBracesCases
     */
    public function testConditionsWithoutBraces(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideConditionsWithoutBracesCases(): iterable
    {
        $statements = [
            'die;',
            'throw new Exception($i);',
            'while($i < 1) throw/*{}*/new Exception($i);',
            'while($i < 1){throw new Exception($i);}',
            'do{throw new Exception($i);}while($i < 1);',
            'foreach($a as $b)throw new Exception($i);',
            'foreach($a as $b){throw new Exception($i);}',
        ];

        foreach ($statements as $statement) {
            yield from self::generateConditionsWithoutBracesCase($statement);
        }

        yield [
            '<?php
                if ($a === false)
                {
                    if ($v) { $ret = "foo"; if($d){return 1;}echo $a;}
                }
                else
                    $ret .= $value;

                return $ret;',
            '<?php
                if ($a === false)
                {
                    if ($v) { $ret = "foo"; if($d){return 1;}else{echo $a;}}
                }
                else
                    $ret .= $value;

                return $ret;',
        ];

        yield from self::generateConditionsWithoutBracesCase('throw new class extends Exception{};');

        yield from self::generateConditionsWithoutBracesCase('throw new class ($a, 9) extends Exception{ public function z($a, $b){ echo 7;} };');
    }

    /**
     * @dataProvider provideConditionsWithoutBraces80Cases
     *
     * @requires PHP 8.0
     */
    public function testConditionsWithoutBraces80(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideConditionsWithoutBraces80Cases(): iterable
    {
        yield from self::generateConditionsWithoutBracesCase('$b = $a ?? throw new Exception($i);');
    }

    /**
     * @param array<int, bool> $indexes
     *
     * @dataProvider provideIsInConditionWithoutBracesCases
     */
    public function testIsInConditionWithoutBraces(array $indexes, string $input): void
    {
        $reflection = new \ReflectionObject($this->fixer);
        $method = $reflection->getMethod('isInConditionWithoutBraces');
        $method->setAccessible(true);
        $tokens = Tokens::fromCode($input);

        foreach ($indexes as $index => $expected) {
            self::assertSame(
                $expected,
                $method->invoke($this->fixer, $tokens, $index, 0),
                sprintf('Failed in condition without braces check for index %d', $index)
            );
        }
    }

    public static function provideIsInConditionWithoutBracesCases(): iterable
    {
        yield [
            [
                18 => false, // return
                25 => false, // return
                36 => false, // return
            ],
            '<?php
                    if ($x) {
                        if ($y) {
                            return 1;
                        }
                            return 2;

                    } else {
                        return 3;
                    }
                ',
        ];

        yield [
            [
                0 => false,
                29 => false, // throw
            ],
            '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        if($a){}else{throw new Exception($i);}
                ',
        ];

        yield [
            [
                0 => false,
                38 => true, // throw
            ],
            '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        for($i =0;$i < 1;++$i) throw new Exception($i);
                ',
        ];

        yield [
            [
                0 => false,
                26 => true, // throw
                28 => true, // new
                30 => true, // Exception
            ],
            '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        while(false){throw new Exception($i);}
                ',
        ];

        yield [
            [
                0 => false,
                30 => true, // throw
                32 => true, // new
                34 => true, // Exception
            ],
            '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        foreach($a as $b){throw new Exception($i);}
                ',
        ];

        yield [
            [
                0 => false,
                25 => true, // throw
                27 => true, // new
                29 => true, // Exception
            ],
            '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        while(false)throw new Exception($i);
                ',
        ];

        yield [
            [
                26 => true, // throw
            ],
            '<?php
                    if ($v) { $ret = "foo"; }
                    elseif($a)
                        do{throw new Exception($i);}while(false);
                ',
        ];

        yield [
            [
                4 => false, // 1
                13 => true, // if (2nd)
                21 => true, // true
                33 => true, // while
                43 => false, // echo
                45 => false, // 2
                46 => false, // ;
                51 => false, // echo (123)
            ],
            '<?php
                    echo 1;
                    if ($a) if ($a) while(true)echo 1;
                    elseif($c) while(true){if($d){echo 2;}};
                    echo 123;
                ',
        ];

        yield [
            [
                2 => false, // echo
                13 => true, // echo
                15 => true, // 2
                20 => true, // die
                23 => false, // echo
            ],
            '<?php
                    echo 1;
                    if ($a) echo 2;
                    else die; echo 3;
                ',
        ];

        yield [
            [
                8 => true,  // die
                9 => true,  // /**/
                15 => true, // die
            ],
            '<?php
                    if ($a)
                        die/**/;
                    else
                        /**/die/**/;#
                ',
        ];

        yield [
            [
                8 => true,  // die
                9 => true,  // /**/
                15 => true, // die
            ],
            '<?php
                    if ($a)
                        die/**/;
                    else
                        /**/die/**/?>
                ',
        ];
    }

    /**
     * @return iterable<array{0: non-empty-string, 1?: non-empty-string}>
     */
    private static function generateConditionsWithoutBracesCase(string $statement): iterable
    {
        $ifTemplate = '<?php
            if ($a === false)
            {
                if ($v) %s
            }
            else
                $ret .= $value;

            return $ret;';

        $ifElseIfTemplate = '<?php
            if ($a === false)
            {
                if ($v) { $ret = "foo"; }
                elseif($a)
                    %s
            }
            else
                $ret .= $value;

            return $ret;';

        $ifElseTemplate = '<?php
            if ($a === false)
            {
                if ($v) { $ret = "foo"; }
                else
                    %s
            }
            else
                $ret .= $value;

            return $ret;';

        yield [sprintf($ifTemplate, $statement)];

        yield [sprintf($ifElseTemplate, $statement)];

        yield [sprintf($ifElseIfTemplate, $statement)];
    }

    /**
     * @return array<array<string>>
     */
    private static function generateCases(string $expected, ?string $input = null): array
    {
        $cases = [];

        foreach ([
            'exit;',
            'exit();',
            'exit(1);',
            'die;',
            'die();',
            'die(1);',
            'break;',
            'break 2;',
            'break (2);',
            'continue;',
            'continue 2;',
            'continue (2);',
            'return;',
            'return 1;',
            'return (1);',
            'return "a";',
            'return 8+2;',
            'return null;',
            'return sum(1+8*6, 2);',
            'throw $e;',
            'throw ($e);',
            'throw new \Exception;',
            'throw new \Exception();',
            'throw new \Exception((string)12+1);',
        ] as $case) {
            if (null === $input) {
                $cases[] = [sprintf($expected, $case)];
                $cases[] = [sprintf($expected, strtoupper($case))];
                $cases[] = [sprintf($expected, strtolower($case))];
            } else {
                $cases[] = [sprintf($expected, $case), sprintf($input, $case)];
                $cases[] = [sprintf($expected, strtoupper($case)), sprintf($input, strtoupper($case))];
                $cases[] = [sprintf($expected, strtolower($case)), sprintf($input, strtolower($case))];
            }
        }

        return $cases;
    }
}
