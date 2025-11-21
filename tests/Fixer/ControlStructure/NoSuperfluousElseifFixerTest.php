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

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractNoUselessElseFixer
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoSuperfluousElseifFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
if ($some) { return 1; } if ($a == 6){ $test = false; } //',
            '<?php
if ($some) { return 1; } elseif ($a == 6){ $test = false; } //',
        ];

        yield [
            '<?php

                if ($foo) {
                    return 1;
                }
                if ($bar) {
                    return 2;
                }
                if ($baz) {
                    return 3;
                } else {
                    return 4;
                }',
            '<?php

                if ($foo) {
                    return 1;
                } elseif ($bar) {
                    return 2;
                } else if ($baz) {
                    return 3;
                } else {
                    return 4;
                }',
        ];

        yield [
            '<?php

                if ($foo)
                    return 1;
                if ($bar)
                    echo \'bar\';
                else {
                    return 3;
                }',
            '<?php

                if ($foo)
                    return 1;
                elseif ($bar)
                    echo \'bar\';
                else {
                    return 3;
                }',
        ];

        yield [
            '<?php

                if ($foo)
                    ?><?php
                elseif ($bar)
                    ?><?php
                else {
                    ?><?php
                }',
        ];

        yield [
            '<?php

                if ($foo) {
                    ?><?php
                    return;
                }
                if ($bar)
                    ?><?php
                else {
                    ?><?php
                }',
            '<?php

                if ($foo) {
                    ?><?php
                    return;
                } elseif ($bar)
                    ?><?php
                else {
                    ?><?php
                }',
        ];

        yield [
            '<?php

                while (1) {
                    if (2) {
                        if (3) {
                            if (4) {
                                die;
                            }
                            if (5) {
                                exit;
                            } else {#foo
                                throw new \Exception();
                            }
                            '.'
                            continue;



                        }
                        if (6) {
                            return null;
                        } else {
                            return 1;
                        }
                        '.'
                        break;
                    }
                    /* bar */if (7)
                        return 2 + 3;
                    else {# baz
                        die(\'foo\');
                    }
                }',
            '<?php

                while (1) {
                    if (2) {
                        if (3) {
                            if (4) {
                                die;
                            } elseif (5) {
                                exit;
                            } else {#foo
                                throw new \Exception();
                            }
                            '.'
                            continue;



                        } else if (6) {
                            return null;
                        } else {
                            return 1;
                        }
                        '.'
                        break;
                    } else/* bar */if (7)
                        return 2 + 3;
                    else {# baz
                        die(\'foo\');
                    }
                }',
        ];

        yield [
            '<?php

                if ($a === false)
                {
                    if ($v) { $ret = "foo"; }
                    elseif($a)
                        die;
                }
                elseif($a)
                    $ret .= $value;

                return $ret;',
        ];

        yield [
            '<?php

                if ($a)
                    echo 1;
                else if ($b)
                    die;
                else {
                    echo 2;
                }',
        ];

        yield [
            '<?php

                if ($a) {
                    echo 1;
                } else if ($b)
                    die;
                else {
                    echo 2;
                }',
        ];

        yield [
            '<?php

                if ($a) {
                    echo 1;
                } else if ($b) {
                    die;
                } else {
                    echo 2;
                }',
        ];

        yield [
            '<?php

                if ($foo) {
                    return 1;
                }
                if ($bar) {
                    return 2;
                }
                if ($baz) {
                    throw new class extends Exception{};
                } else {
                    return 4;
                }',
            '<?php

                if ($foo) {
                    return 1;
                } elseif ($bar) {
                    return 2;
                } else if ($baz) {
                    throw new class extends Exception{};
                } else {
                    return 4;
                }',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected): void
    {
        $this->doTest($expected);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php
            if ($foo) {
                $a = $bar ?? throw new \Exception();
            } elseif ($bar) {
                echo 1;
            }
            ',
        ];
    }
}
