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

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if ($some) { return 1; } if ($a == 6){ $test = false; } //
                EOD,
            <<<'EOD'
                <?php
                if ($some) { return 1; } elseif ($a == 6){ $test = false; } //
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

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
                                }
                EOD,
            <<<'EOD'
                <?php

                                if ($foo) {
                                    return 1;
                                } elseif ($bar) {
                                    return 2;
                                } else if ($baz) {
                                    return 3;
                                } else {
                                    return 4;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                if ($foo)
                                    return 1;
                                if ($bar)
                                    echo 'bar';
                                else {
                                    return 3;
                                }
                EOD,
            <<<'EOD'
                <?php

                                if ($foo)
                                    return 1;
                                elseif ($bar)
                                    echo 'bar';
                                else {
                                    return 3;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                if ($foo)
                                    ?><?php
                                elseif ($bar)
                                    ?><?php
                                else {
                                    ?><?php
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                if ($foo) {
                                    ?><?php
                                    return;
                                }
                                if ($bar)
                                    ?><?php
                                else {
                                    ?><?php
                                }
                EOD,
            <<<'EOD'
                <?php

                                if ($foo) {
                                    ?><?php
                                    return;
                                } elseif ($bar)
                                    ?><?php
                                else {
                                    ?><?php
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

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
                EOD."\n                            ".<<<'EOD'

                                            continue;



                                        }
                                        if (6) {
                                            return null;
                                        } else {
                                            return 1;
                                        }
                EOD."\n                        ".<<<'EOD'

                                        break;
                                    }
                                    /* bar */if (7)
                                        return 2 + 3;
                                    else {# baz
                                        die('foo');
                                    }
                                }
                EOD,
            <<<'EOD'
                <?php

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
                EOD."\n                            ".<<<'EOD'

                                            continue;



                                        } else if (6) {
                                            return null;
                                        } else {
                                            return 1;
                                        }
                EOD."\n                        ".<<<'EOD'

                                        break;
                                    } else/* bar */if (7)
                                        return 2 + 3;
                                    else {# baz
                                        die('foo');
                                    }
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                if ($a === false)
                                {
                                    if ($v) { $ret = "foo"; }
                                    elseif($a)
                                        die;
                                }
                                elseif($a)
                                    $ret .= $value;

                                return $ret;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                if ($a)
                                    echo 1;
                                else if ($b)
                                    die;
                                else {
                                    echo 2;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                if ($a) {
                                    echo 1;
                                } else if ($b)
                                    die;
                                else {
                                    echo 2;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                if ($a) {
                                    echo 1;
                                } else if ($b) {
                                    die;
                                } else {
                                    echo 2;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

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
                                }
                EOD,
            <<<'EOD'
                <?php

                                if ($foo) {
                                    return 1;
                                } elseif ($bar) {
                                    return 2;
                                } else if ($baz) {
                                    throw new class extends Exception{};
                                } else {
                                    return 4;
                                }
                EOD,
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

    public static function provideFix80Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                            if ($foo) {
                                $a = $bar ?? throw new \Exception();
                            } elseif ($bar) {
                                echo 1;
                            }
                EOD."\n            ",
        ];
    }
}
