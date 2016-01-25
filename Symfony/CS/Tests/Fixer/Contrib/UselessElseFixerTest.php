<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author SpacePossum
 */
class UselessElseFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixIfElseIfElseCases
     */
    public function testFixIfElseIfElse($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixIfElseIfElseCases()
    {
        $expected =
            '<?php
                if($a) {
                    return;
                } elseif($a1) {
                    if ($b) {echo 1; die;}  echo 2;
                    return 1;
                } elseif($b) {
                    %s
                }  '.'
                    echo 2;
                '.'
            ';

        $input =
            '<?php
                if($a) {
                    return;
                } elseif($a1) {
                    if ($b) {echo 1; die;} else {echo 2};
                    return 1;
                } elseif($b) {
                    %s
                } else {
                    echo 2;
                }
            ';

        $cases = $this->generateCases($expected, $input);

        $expected =
            '<?php
                if($a) {
                    echo 1;
                } elseif($b) {
                    %s
                } else {
                    echo 3;
                }
            ';

        $cases = array_merge($cases, $this->generateCases($expected));

        $expected =
            '<?php
                if ($a) {
                    echo 1;
                } elseif  ($a1) {
                    echo 2;
                } elseif  ($b) {
                    echo $b+1; //
                    /* test */
                    %s
                } else {
                    echo 3;
                }
            ';

        $cases = array_merge($cases, $this->generateCases($expected));

        $cases[] = array(
            '<?php
                if ($a)
                    echo 1;
                else if($b)
                    echo 2;
                elseif($c)
                    echo 3;
                    if ($a) {

                    }elseif($d) {
                        return 1;
                    }
                else
                    echo 4;
            ');

        $cases[] = array(
            '<?php
                if ($a)
                    echo 1;
                else if($b) {
                    echo 2;
                } elseif($c) {
                    echo 3;
                    if ($d) {
                        echo 4;
                    } elseif($e)
                        return 1;
                } else
                    echo 4;
            ');

        return $cases;
    }

    /**
     * @dataProvider provideFixIfElseCases
     */
    public function testFixIfElse($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixIfElseCases()
    {
        $expected =
            '<?php
                if ($a) {
                    %s
                }  '.'
                    echo 1;
                '.'
            ';

        $input =
            '<?php
                if ($a) {
                    %s
                } else {
                    echo 1;
                }
            ';

        $cases = $this->generateCases($expected, $input);

        // short 'if' statements
        $expected =
            '<?php
                if ($a)
                    %s
                '.'
                    echo 1;
            ';

        $input =
            '<?php
                if ($a)
                    %s
                else
                    echo 1;
            ';

        $cases = array_merge($cases, $this->generateCases($expected, $input));

        // short and not short combined
        $cases[] = array(
            '<?php
                if ($a)
                    return;
                 '.'
                    echo 1;
                '.'
            ',
            '<?php
                if ($a)
                    return;
                else {
                    echo 1;
                }
            ',
        );

        $cases[] = array(
            '<?php
                if ($a) {
                    GOTO jump;
                }  '.'
                    echo 1;
                '.'

                jump:
            ',
            '<?php
                if ($a) {
                    GOTO jump;
                } else {
                    echo 1;
                }

                jump:
            ',
        );

        // empty if block (can be fixed to if (!$a) + move tokens, making else over complete, not part of this fixer)
        $cases[] = array(
            '<?php
                if ($a) {
                    //
                } else {
                    echo 1;
                }
            ',
        );

        $cases[] = array(
            '<?php if($a){;} else {echo 1;}',
        );

        $cases[] = array(
            '<?php if ($a) {test();} else {echo 1;}',
        );

        $cases[] = array(
            '<?php if ($a) {$b = function () {};} else {echo 1;}',
        );

        $cases[] = array(
            '<?php if ($a) {$b = function () use ($a){};} else {echo 1;}',
        );

        $cases[] = array(
            '<?php if($a){}',
            '<?php if($a){}else{}',
        );

        $cases[] = array(
            '<?php if($a){ $a = ($b); }  ',
            '<?php if($a){ $a = ($b); } else {}',
        );

        $cases[] = array(
            '<?php if ($a) {;}   if ($a) {;}  /**/ if($a){}',
            '<?php if ($a) {;} else {} if ($a) {;} else {/**/} if($a){}else{}',
        );

        $cases[] = array(
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
        );

        $cases[] = array(
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
        );

        return $cases;
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @return array<string, string>
     */
    private function generateCases($expected, $input = null)
    {
        $cases = array();
        foreach (
            array(
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

    /**
     * @dataProvider provideBefore54FixCases
     */
    public function testBefore54Fix($expected, $input = null)
    {
        if (PHP_VERSION_ID >= 50400) {
            $this->markTestSkipped('PHP lower than 5.4 is required.');
        }

        $this->makeTest($expected, $input);
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
                            echo 2;
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
                            echo 2;
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
}
