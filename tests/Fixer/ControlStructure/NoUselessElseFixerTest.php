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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer
 */
final class NoUselessElseFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePHPCloseTagCases
     */
    public function testCloseTagCases($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function providePHPCloseTagCases()
    {
        return array(
            array(
                '<?php
                    if (true) {
                        $b = $a > 2 ? "" : die
                        ?>
                    <?php
                    } else {
                        echo 798;
                    }',
            ),
            array(
                '<?php
                    if (true) {
                        $b = $a > 2 ? "" : die
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
            ),
            array(
                '<?php
                    if (true) {
                        if($a) die
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
            ),
            array(
                '<?php
                    if (true) {
                        echo 1;
                        ?>
                    <?php ; // useless semicolon case
                    } else {
                        echo 798;
                    }',
            ),
            array(
                '<?php
                    if (true) {
                        echo 777;
                        if(false) die ?>
                    <?php
                    } else {
                        echo 778;
                    }',
            ),
            array(
                '<?php
                    if (true)
                        echo 3;
                    else {
                        ?><?php
                        echo 4;
                    }
                ',
            ),
            array(
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
            ),
            array(
                '<?php
if (true)
    echo 4;
?><?php echo 5;',
                '<?php
if (true)
    echo 4;
else?><?php echo 5;',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixIfElseIfElseCases
     */
    public function testFixIfElseIfElse($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixIfElseIfElseCases()
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

        $cases[] = array(
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
            ', );

        $cases[] = array(
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
            ', );

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixIfElseCases
     */
    public function testFixIfElse($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixIfElseCases()
    {
        $expected =
            '<?php
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

        $input =
            '<?php
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

        $cases = $this->generateCases($expected, $input);

        $cases[] = array(
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
        );

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixNestedIfs
     */
    public function testFixNestedIfs($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixNestedIfs()
    {
        return array(
            array(
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
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideBefore54FixCases
     */
    public function testBefore54Fix($expected, $input = null)
    {
        if (PHP_VERSION_ID >= 50400) {
            $this->markTestSkipped('PHP lower than 5.4 is required.');
        }

        $this->doTest($expected, $input);
    }

    public function provideBefore54FixCases()
    {
        $expected =
            '<?php
                $a = 1; $b = 0;
                while(true) {
                    while(true) {
                        ++$b;
                        if ($b > $a) {
                            %s %%s;
                        }  //
                            echo 22;
                        //
                    }
                }
            ';

        $input =
            '<?php
                $a = 1; $b = 0;
                while(true) {
                    while(true) {
                        ++$b;
                        if ($b > $a) {
                            %s %%s;
                        } else {//
                            echo 22;
                        }//
                    }
                }
            ';

        $cases = array();
        foreach (array('continue', 'break') as $stop) {
            $expectedTemplate = sprintf($expected, $stop);
            $inputTemplate = sprintf($input, $stop);
            foreach (array('1+1', '$a', '(1+1)', '($a)') as $value) {
                $cases[] = array(
                    sprintf($expectedTemplate, $value),
                    sprintf($inputTemplate, $value),
                );
            }
        }

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixEmptyElseCases
     */
    public function testFixEmptyElse($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixEmptyElseCases()
    {
        return array(
            array(
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
            ),
            array(
                '<?php if($a){}',
                '<?php if($a){}else{}',
            ),
            array(
                '<?php if($a){ $a = ($b); }  ',
                '<?php if($a){ $a = ($b); } else {}',
            ),
            array(
                '<?php if ($a) {;}   if ($a) {;}  /**/ if($a){}',
                '<?php if ($a) {;} else {} if ($a) {;} else {/**/} if($a){}else{}',
            ),
            array(
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
            ),
            array(
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
            ),
        );
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideNegativeCases
     */
    public function testNegativeCases($expected)
    {
        $this->doTest($expected);
    }

    public function provideNegativeCases()
    {
        return array(
            array(
                '<?php
                    if ($a0) {
                        //
                    } else {
                        echo 0;
                    }
                ',
            ),
            array(
                '<?php
                    if (false)
                        echo "a";
                    else

                    echo "a";
                ',
            ),
            array(
                '<?php if($a2){;} else {echo 27;}',
            ),
            array(
                '<?php if ($a3) {test();} else {echo 3;}',
            ),
            array(
                '<?php if ($a4) {$b = function () {};} else {echo 4;}',
            ),
            array(
                '<?php if ($a5) {$b = function () use ($a){};} else {echo 5;}',
            ),
            array(
                '<?php
                    if ($a) {
                        if ($b) return;
                    } else {
                        echo 1;
                    }
                ',
            ),
            array(
                '<?php
                    if ($a) {
                        if ($b) throw new \Exception();
                    } else {
                        echo 1;
                    }
                ',
            ),
            array(
                '<?php
                    if ($a) {
                        if ($b) { throw new \Exception(); }
                    } else {
                        echo 1;
                    }
                ',
            ),
            array(
                '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : die;
                    else
                        echo 40;

                    echo "end";
                ',
            ),
            array(
                '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : exit(1);
                    else
                        echo 40;

                    echo "end";
                ',
            ),
            array(
                '<?php
                    $a = true; // 6
                    if (true === $a)
                        $b = true === $a ? 1 : exit(1);
                    else
                        echo 4;

                    echo "end";
                ',
            ),
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php
                    return function() {
                        if (false) {

                        } elseif (3 > 2) {

                        } else {
                            echo 1;
                        }
                    };',
            ),
            array(
                '<?php
                    return function() {
                        if (false) {
                            return 1;
                        } elseif (3 > 2) {

                        } else {
                            echo 1;
                        }
                    };',
            ),
        );
    }

    /**
     * @param string $source
     * @param int    $index
     *
     * @dataProvider provideBlockDetectionCases
     */
    public function testBlockDetection(array $expected, $source, $index)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        $method = new \ReflectionMethod($this->fixer, 'getPreviousBlock');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $index);

        $this->assertSame($expected, $result);
    }

    public function provideBlockDetectionCases()
    {
        $cases = array();

        $source = '<?php
                    if ($a)
                        echo 1;
                    elseif ($a) ///
                        echo 2;
                    else if ($b) /**/ echo 3;
                    else
                        echo 4;
                    ';
        $cases[] = array(array(2, 11), $source, 13);
        $cases[] = array(array(13, 24), $source, 26);
        $cases[] = array(array(13, 24), $source, 26);
        $cases[] = array(array(26, 39), $source, 41);

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
        $cases[] = array(array(2, 25), $source, 27);
        $cases[] = array(array(27, 40), $source, 42);
        if (!defined('HHVM_VERSION')) {
            // HHVM 3.6.x tokenizes in a different way
            $cases[] = array(array(59, 72), $source, 74);
        }

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideConditionsWithoutBraces
     */
    public function testConditionsWithoutBraces($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideConditionsWithoutBraces()
    {
        $cases = array();
        $statements = array(
            'die;',
            'throw new Exception($i);',
            'while($i < 1) throw/*{}*/new Exception($i);',
            'while($i < 1){throw new Exception($i);}',
            'do{throw new Exception($i);}while($i < 1);',
            'foreach($a as $b)throw new Exception($i);',
            'foreach($a as $b){throw new Exception($i);}',
        );

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

        foreach ($statements as $statement) {
            $cases[] = array(sprintf($ifTemplate, $statement));
            $cases[] = array(sprintf($ifElseTemplate, $statement));
            $cases[] = array(sprintf($ifElseIfTemplate, $statement));
        }

        $cases[] = array(
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
        );

        return $cases;
    }

    /**
     * @param string            $input
     * @param string<int, bool> $indexes
     *
     * @dataProvider provideIsInConditionWithoutBracesCases
     */
    public function testIsInConditionWithoutBraces($indexes, $input)
    {
        $reflection = new \ReflectionObject($this->fixer);
        $method = $reflection->getMethod('isInConditionWithoutBraces');
        $method->setAccessible(true);

        $tokens = Tokens::fromCode($input);
        foreach ($indexes as $index => $expected) {
            $this->assertSame(
                $expected,
                $method->invoke($this->fixer, $tokens, $index, 0),
                sprintf('Failed in condition without braces check for index %d', $index)
            );
        }
    }

    public function provideIsInConditionWithoutBracesCases()
    {
        return array(
            array(
                array(
                    18 => false, // return
                    25 => false, // return
                    36 => false, // return
                ),
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
            ),
            array(
                array(
                    0 => false,
                    29 => false, // throw
                ),
                '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        if($a){}else{throw new Exception($i);}
                ',
            ),
            array(
                array(
                    0 => false,
                    38 => true, // throw
                ),
                '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        for($i =0;$i < 1;++$i) throw new Exception($i);
                ',
            ),
            array(
                array(
                    0 => false,
                    26 => true, // throw
                    28 => true, // new
                    30 => true, // Exception
                ),
                '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        while(false){throw new Exception($i);}
                ',
            ),
            array(
                array(
                    0 => false,
                    30 => true, // throw
                    32 => true, // new
                    34 => true, // Exception
                ),
                '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        foreach($a as $b){throw new Exception($i);}
                ',
            ),
            array(
                array(
                    0 => false,
                    25 => true, // throw
                    27 => true, // new
                    29 => true, // Exception
                ),
                '<?php
                    if ($v) { $ret = "foo"; }
                    else
                        while(false)throw new Exception($i);
                ',
            ),
            array(
                array(
                    26 => true, // throw
                ),
                '<?php
                    if ($v) { $ret = "foo"; }
                    elseif($a)
                        do{throw new Exception($i);}while(false);
                ',
            ),
            array(
                array(
                    4 => false, // 1
                    13 => true, // if (2nd)
                    21 => true, // true
                    33 => true, // while
                    43 => false, // echo
                    45 => false, // 2
                    46 => false, // ;
                    51 => false, // echo (123)
                ),
                '<?php
                    echo 1;
                    if ($a) if ($a) while(true)echo 1;
                    elseif($c) while(true){if($d){echo 2;}};
                    echo 123;
                ',
            ),
            array(
                array(
                    2 => false, // echo
                    13 => true, // echo
                    15 => true, // 2
                    20 => true, // die
                    23 => false, // echo
                ),
                '<?php
                    echo 1;
                    if ($a) echo 2;
                    else die; echo 3;
                ',
            ),
            array(
                array(
                    8 => true,  // die
                    9 => true,  // /**/
                    15 => true, // die
                ),
                '<?php
                    if ($a)
                        die/**/;
                    else
                        /**/die/**/;#
                ',
            ),
            array(
                array(
                    8 => true,  // die
                    9 => true,  // /**/
                    15 => true, // die
                ),
                '<?php
                    if ($a)
                        die/**/;
                    else
                        /**/die/**/?>
                ',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @return array<string, string>
     */
    private function generateCases($expected, $input = null)
    {
        $cases = array();
        foreach (array(
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
        ) as $case) {
            if (null === $input) {
                $cases[] = array(sprintf($expected, $case));
                $cases[] = array(sprintf($expected, strtoupper($case)));
                $cases[] = array(sprintf($expected, strtolower($case)));
            } else {
                $cases[] = array(sprintf($expected, $case), sprintf($input, $case));
                $cases[] = array(sprintf($expected, strtoupper($case)), sprintf($input, strtoupper($case)));
                $cases[] = array(sprintf($expected, strtolower($case)), sprintf($input, strtolower($case)));
            }
        }

        return $cases;
    }
}
