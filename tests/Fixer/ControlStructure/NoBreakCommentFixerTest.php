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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer
 */
final class NoBreakCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFixWithExplicitDefaultConfiguration($expected, $input = null)
    {
        $this->fixer->configure([
            'comment_text' => 'no break',
        ]);
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
    case 2:
        bar();
        // no break
    default:
        baz();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
    case 2:
        bar();
    default:
        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:

        foo();

        // no break
    case 2:

        bar();

        // no break
    default:

        baz();
}',
                '<?php
switch ($foo) {
    case 1:

        foo();

    case 2:

        bar();

    default:

        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
    case 2:
        bar();
        // no break
    default:
        baz();
}',
                '<?php
switch ($foo) {
    case 1:
        foo(); // no break
    case 2:
        bar(); // no break
    default:
        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        foo();
        // no break
    case 2;
        bar();
        // no break
    default;
        baz();
}',
                '<?php
switch ($foo) {
    case 1;
        foo();
    case 2;
        bar();
    default;
        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        // foo
        // no break
    case 2:
        bar();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
        // foo
    case 2:
        bar();
}',
            ],
            [
                '<?php
switch ($foo) { case 1: foo();
// no break
case 2: bar(); }',
                '<?php
switch ($foo) { case 1: foo(); case 2: bar(); }',
            ],
            [
                '<?php
switch ($foo) { case 1: foo();
// no break
case 2: bar(); }',
                '<?php
switch ($foo) { case 1: foo();case 2: bar(); }',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        foreach ($bar as $baz) {
            break;
        }
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        foreach ($bar as $baz) {
            break;
        }
    case 2;
        bar();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        for ($i = 0; $i < 1; ++$i) {
            break;
        }
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        for ($i = 0; $i < 1; ++$i) {
            break;
        }
    case 2;
        bar();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        foreach ($bar as $baz) {
            break;
        }
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        foreach ($bar as $baz) {
            break;
        }
    case 2;
        bar();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        if ($foo) {
            break;
        }
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        if ($foo) {
            break;
        }
    case 2;
        bar();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        do {
            break;
        } while ($bar);
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        do {
            break;
        } while ($bar);
    case 2;
        bar();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        $foo = function ($bar) {
            foreach ($bar as $baz) {
                break;
            }
        };
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        $foo = function ($bar) {
            foreach ($bar as $baz) {
                break;
            }
        };
    case 2;
        bar();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        switch ($bar) {
            case 1:
                foo();
                // no break
            case 2:
                bar();
        }
        break;
    case 2:
        switch ($bar) {
            case 1:
                bar();
                // no break
            case 2:
                foo();
        }
}',
                '<?php
switch ($foo) {
    case 1:
        switch ($bar) {
            case 1:
                foo();
            case 2:
                bar();
        }
        break;
    case 2:
        switch ($bar) {
            case 1:
                bar();
            case 2:
                foo();
        }
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        switch ($bar):
            case 1:
                foo();
                // no break
            case 2:
                bar();
        endswitch;
        break;
    case 2:
        switch ($bar):
            case 1:
                bar();
                // no break
            case 2:
                foo();
        endswitch;
}',
                '<?php
switch ($foo) {
    case 1:
        switch ($bar):
            case 1:
                foo();
            case 2:
                bar();
        endswitch;
        break;
    case 2:
        switch ($bar):
            case 1:
                bar();
            case 2:
                foo();
        endswitch;
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        continue;
    case 2:
        bar();
        continue;
    default:
        baz();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
        continue;
    case 2:
        bar();
        // no break
        continue;
    default:
        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:

        return foo();


    case 2:

        return bar();


    default:

        return baz();
}',
                '<?php
switch ($foo) {
    case 1:

        return foo();

        // no break

    case 2:

        return bar();

        // no break

    default:

        return baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        return foo();
    case 2:
        return bar();
    default:
        return baz();
}',
                '<?php
switch ($foo) {
    case 1:
        // no break
        return foo();
    case 2:
        // no break
        return bar();
    default:
        return baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        break;
    case 2:
        bar();
        break;
    default:
        baz();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
        break;
    case 2:
        bar();
        // no break
        break;
    default:
        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        break;
    case 2:
        bar();
        break;
    case 3:
        baz();
        break;
    default:
        qux();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
        # no break
        break;
    case 2:
        bar();
        /* no break */
        break;
    case 3:
        baz();
        /** no break */
        break;
    default:
        qux();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
    case 2:
        bar();
        break;
    default:
        baz();
}',
                '<?php
switch ($foo) {
    case 1:
        // no break
    case 2:
        bar();
        // no break
        break;
    default:
        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
}',
            ],
            [
                '<?php
switch ($foo) {
    default:
        foo();
}',
                '<?php
switch ($foo) {
    default:
        foo();
        // no break
}',
            ],
            [
                '<?php switch ($foo) { case 1: switch ($bar) { case 1: switch ($baz) { case 1: $foo = 1;
// no break
case 2: $foo = 2; }
// no break
case 2: switch ($baz) { case 1: $foo = 3;
// no break
case 2: $foo = 4; } }
// no break
case 2: switch ($bar) { case 1: switch ($baz) { case 1: $foo = 5;
// no break
case 2: $foo = 6; }
// no break
case 2: switch ($baz) { case 1: $foo = 7;
// no break
case 2: $foo = 8; } } }',
                '<?php switch ($foo) { case 1: switch ($bar) { case 1: switch ($baz) { case 1: $foo = 1; case 2: $foo = 2; } case 2: switch ($baz) { case 1: $foo = 3; case 2: $foo = 4; } } case 2: switch ($bar) { case 1: switch ($baz) { case 1: $foo = 5; case 2: $foo = 6; } case 2: switch ($baz) { case 1: $foo = 7; case 2: $foo = 8; } } }',
            ],
            [
                '<?php
switch ($foo):
    case 1:
        foo();
        // no break
    case 2:
        bar();
endswitch;',
                '<?php
switch ($foo):
    case 1:
        foo();
    case 2:
        bar();
        // no break
endswitch;',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        ?>foo<?php
        // no break
    case 2:
        ?>bar<?php
        break;
}',
                '<?php
switch ($foo) {
    case 1:
        ?>foo<?php
    case 2:
        ?>bar<?php
        // no break
        break;
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        ?>foo<?php

        // no break
    case 2:
        ?>bar<?php

        break;
}',
                '<?php
switch ($foo) {
    case 1:
        ?>foo<?php

    case 2:
        ?>bar<?php

        // no break
        break;
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        ?>foo<?php // foo
        // no break
    case 2:
        ?>bar<?php // bar
        break;
}',
                '<?php
switch ($foo) {
    case 1:
        ?>foo<?php // foo
    case 2:
        ?>bar<?php // bar
        // no break
        break;
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
    case 2:
        bar();
        // no break
    default:
        baz();
}',
                '<?php
switch ($foo) {
    case 1:
        // no break
        foo();
    case 2:
        // no break
        bar();
    default:
        baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        die;
    case 2:
        exit;
    default:
        die;
}',
                '<?php
switch ($foo) {
    case 1:
        // no break
        die;
    case 2:
        // no break
        exit;
    default:
        die;
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        throw new \Exception();
    case 2:
        throw new \Exception();
    default:
        throw new \Exception();
}',
                '<?php
switch ($foo) {
    case 1:
        // no break
        throw new \Exception();
    case 2:
        // no break
        throw new \Exception();
    default:
        throw new \Exception();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        goto a;
    case 2:
        goto a;
    default:
        goto a;
}

a:
echo \'foo\';',
                '<?php
switch ($foo) {
    case 1:
        // no break
        goto a;
    case 2:
        // no break
        goto a;
    default:
        goto a;
}

a:
echo \'foo\';',
            ],
            [
                '<?php
switch ($foo) {
    case "bar":
        if (1) {
        } else {
        }

        $aaa = new Bar();
        break;
    default:
        $aaa = new Baz();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
?>
<?php
// no break
    default:
?>
<?php
}',
                '<?php
switch ($foo) {
    case 1:
?>
<?php
    default:
?>
<?php
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
?>
<?php
// no break
default:
?>
<?php }',
                '<?php
switch ($foo) {
    case 1:
?>
<?php default:
?>
<?php }',
            ],
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
    case 2:
        bar();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
        // No break
    case 2:
        bar();
}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixPhp70Cases
     * @requires PHP 7.0
     */
    public function testFixPhp70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixPhp70Cases()
    {
        return [
            [
                '<?php
switch ($foo) {
    case 1;
        $foo = new class {
            public function foo($bar)
            {
                foreach ($bar as $baz) {
                    break;
                }
            }
        };
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        $foo = new class {
            public function foo($bar)
            {
                foreach ($bar as $baz) {
                    break;
                }
            }
        };
    case 2;
        bar();
}',
            ],
            [
                '<?php
switch ($foo) {
    case 1;
        $foo = new class(1) {
            public function foo($bar)
            {
                foreach ($bar as $baz) {
                    break;
                }
            }
        };
        // no break
    case 2;
        bar();
}',
                '<?php
switch ($foo) {
    case 1;
        $foo = new class(1) {
            public function foo($bar)
            {
                foreach ($bar as $baz) {
                    break;
                }
            }
        };
    case 2;
        bar();
}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixWithDifferentCommentTextCases
     */
    public function testFixWithDifferentCommentText($expected, $input = null)
    {
        $this->fixer->configure([
            'comment_text' => 'fall-through case!',
        ]);
        $this->doTest($expected, $input);
    }

    public function provideTestFixWithDifferentCommentTextCases()
    {
        $cases = $this->provideTestFixCases();

        $replaceCommentText = function ($php) {
            return strtr($php, [
                'No break' => 'Fall-through case!',
                'no break' => 'fall-through case!',
            ]);
        };

        foreach ($cases as &$case) {
            $case[0] = $replaceCommentText($case[0]);
            if (isset($case[1])) {
                $case[1] = $replaceCommentText($case[1]);
            }
        }

        $cases = array_merge($cases, [
            [
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
        // fall-through case!
    case 2:
        bar();
        // no break
        // fall-through case!
    default:
        baz();
}',
                '<?php
switch ($foo) {
    case 1:
        foo();
        // no break
    case 2:
        bar();
        // no break
    default:
        baz();
}',
            ],
        ]);

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixWithDifferentLineEndingCases
     */
    public function testFixWithDifferentLineEnding($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\r\n"));
        $this->doTest($expected, $input);
    }

    public function provideTestFixWithDifferentLineEndingCases()
    {
        $cases = [];
        foreach ($this->provideTestFixCases() as $case) {
            $case[0] = str_replace("\n", "\r\n", $case[0]);
            if (isset($case[1])) {
                $case[1] = str_replace("\n", "\r\n", $case[1]);
            }

            $cases[] = $case;
        }

        return $cases;
    }

    public function testConfigureWithInvalidOptions()
    {
        $this->setExpectedExceptionRegExp(
            InvalidFixerConfigurationException::class,
            '/^\[no_break_comment\] Invalid configuration: The option "foo" does not exist\. Defined options are: "comment_text"\.$/'
        );
        $this->fixer->configure(['foo' => true]);
    }
}
