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
     * @dataProvider providePHPCloseTagCases
     */
    public function testCloseTagCases(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function providePHPCloseTagCases(): array
    {
        return [
            [
                '<?php
                    if (true) {
                        $b = $a > 2 ? "" : die
                        ?>
                    <?php
                    } else {
                        echo 798;
                    }',
            ],
            [
                '<?php
                    if (true) {
                        $b = $a > 2 ? "" : die
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
            ],
            [
                '<?php
                    if (true) {
                        if($a) die
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
            ],
            [
                '<?php
                    if (true) {
                        echo 1;
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
            ],
            [
                '<?php
                    if (true) {
                        echo 777;
                        if(false) die ?>
                    <?php
                    } else {
                        echo 778;
                    }',
            ],
            [
                '<?php
                    if (true)
                        echo 3;
                    else {
                        ?><?php
                        echo 4;
                    }
                ',
            ],
            [
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
            ],
            [
                '<?php
if (true)
    echo 4;
?><?php echo 5;',
                '<?php
if (true)
    echo 4;
else?><?php echo 5;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixIfElseIfElseCases
     */
    public function testFixIfElseIfElse(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixIfElseIfElseCases(): array
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

        $cases = $this->generateCases($expected, $input);

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

        $cases = array_merge($cases, $this->generateCases($expected));

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

        $cases = array_merge($cases, $this->generateCases($expected));

        $cases[] = [
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

        $cases[] = [
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

        return $cases;
    }

    /**
     * @dataProvider provideFixIfElseCases
     */
    public function testFixIfElse(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixIfElseCases(): iterable
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

        yield from $this->generateCases($expected, $input);

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

    public function provideFixNestedIfCases(): array
    {
        return [
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixEmptyElseCases
     */
    public function testFixEmptyElse(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixEmptyElseCases(): array
    {
        return [
            [
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
            ],
            [
                '<?php if($a){}',
                '<?php if($a){}else{}',
            ],
            [
                '<?php if($a){ $a = ($b); }  ',
                '<?php if($a){ $a = ($b); } else {}',
            ],
            [
                '<?php if ($a) {;}   if ($a) {;}  /**/ if($a){}',
                '<?php if ($a) {;} else {} if ($a) {;} else {/**/} if($a){}else{}',
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideNegativeCases
     */
    public function testNegativeCases(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideNegativeCases(): iterable
    {
        yield from [
            [
                '<?php
                    if ($a0) {
                        //
                    } else {
                        echo 0;
                    }
                ',
            ],
            [
                '<?php
                    if (false)
                        echo "a";
                    else

                    echo "a";
                ',
            ],
            [
                '<?php if($a2){;} else {echo 27;}',
            ],
            [
                '<?php if ($a3) {test();} else {echo 3;}',
            ],
            [
                '<?php if ($a4) {$b = function () {};} else {echo 4;}',
            ],
            [
                '<?php if ($a5) {$b = function () use ($a){};} else {echo 5;}',
            ],
            [
                '<?php
                    if ($a) {
                        if ($b) return;
                    } else {
                        echo 1;
                    }
                ',
            ],
            [
                '<?php
                    if ($a) {
                        if ($b) throw new \Exception();
                    } else {
                        echo 1;
                    }
                ',
            ],
            [
                '<?php
                    if ($a) {
                        if ($b) { throw new \Exception(); }
                    } else {
                        echo 1;
                    }
                ',
            ],
            [
                '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : die;
                    else
                        echo 40;

                    echo "end";
                ',
            ],
            [
                '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : exit(1);
                    else
                        echo 40;

                    echo "end";
                ',
            ],
            [
                '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : exit(1);
                    else
                        echo 4;

                    echo "end";
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                    return function() {
                        if (false) {

                        } elseif (3 > 2) {

                        } else {
                            echo 1;
                        }
                    };',
            ],
            [
                '<?php
                    return function() {
                        if (false) {
                            return 1;
                        } elseif (3 > 2) {

                        } else {
                            echo 1;
                        }
                    };',
            ],
        ];
    }

    /**
     * @dataProvider provideNegativePhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testNegativePhp80Cases(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideNegativePhp80Cases(): iterable
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

        $method = new \ReflectionMethod($this->fixer, 'getPreviousBlock');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $index);

        static::assertSame($expected, $result);
    }

    public function provideBlockDetectionCases(): array
    {
        $cases = [];

        $source = '<?php
                    if ($a)
                        echo 1;
                    elseif ($a) ///
                        echo 2;
                    else if ($b) /**/ echo 3;
                    else
                        echo 4;
                    ';
        $cases[] = [[2, 11], $source, 13];
        $cases[] = [[13, 24], $source, 26];
        $cases[] = [[13, 24], $source, 26];
        $cases[] = [[26, 39], $source, 41];

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
        $cases[] = [[2, 25], $source, 27];
        $cases[] = [[27, 40], $source, 42];
        $cases[] = [[59, 72], $source, 74];

        return $cases;
    }

    /**
     * @dataProvider provideConditionsWithoutBracesCases
     */
    public function testConditionsWithoutBraces(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideConditionsWithoutBracesCases(): iterable
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
            yield from $this->generateConditionsWithoutBracesCase($statement);
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

        yield from $this->generateConditionsWithoutBracesCase('throw new class extends Exception{};');

        yield from $this->generateConditionsWithoutBracesCase('throw new class ($a, 9) extends Exception{ public function z($a, $b){ echo 7;} };');
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

    public function provideConditionsWithoutBraces80Cases(): iterable
    {
        yield from $this->generateConditionsWithoutBracesCase('$b = $a ?? throw new Exception($i);');
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
            static::assertSame(
                $expected,
                $method->invoke($this->fixer, $tokens, $index, 0),
                sprintf('Failed in condition without braces check for index %d', $index)
            );
        }
    }

    public function provideIsInConditionWithoutBracesCases(): array
    {
        return [
            [
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
            ],
            [
                [
                    0 => false,
                    29 => false, // throw
                ],
                '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        if($a){}else{throw new Exception($i);}
                ',
            ],
            [
                [
                    0 => false,
                    38 => true, // throw
                ],
                '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        for($i =0;$i < 1;++$i) throw new Exception($i);
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                [
                    26 => true, // throw
                ],
                '<?php
                    if ($v) { $ret = "foo"; }
                    elseif($a)
                        do{throw new Exception($i);}while(false);
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @return iterable<array{0: non-empty-string, 1?: non-empty-string}>
     */
    private function generateConditionsWithoutBracesCase(string $statement): iterable
    {
        $ifTemplate = '<?php
            if ($a === false)
            {
                if ($v) %s
            }
            else
                $ret .= $value;

            return $ret;'
        ;

        $ifElseIfTemplate = '<?php
            if ($a === false)
            {
                if ($v) { $ret = "foo"; }
                elseif($a)
                    %s
            }
            else
                $ret .= $value;

            return $ret;'
        ;

        $ifElseTemplate = '<?php
            if ($a === false)
            {
                if ($v) { $ret = "foo"; }
                else
                    %s
            }
            else
                $ret .= $value;

            return $ret;'
        ;

        yield [sprintf($ifTemplate, $statement)];

        yield [sprintf($ifElseTemplate, $statement)];

        yield [sprintf($ifElseIfTemplate, $statement)];
    }

    /**
     * @return array<array<string>>
     */
    private function generateCases(string $expected, ?string $input = null): array
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
