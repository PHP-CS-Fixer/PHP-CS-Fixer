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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer
 */
final class NoSuperfluousElseifFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
                '<?php

                if ($foo)
                    ?><?php
                elseif ($bar)
                    ?><?php
                else {
                    ?><?php
                }',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php

                if ($a)
                    echo 1;
                else if ($b)
                    die;
                else {
                    echo 2;
                }',
            ],
            [
                '<?php

                if ($a) {
                    echo 1;
                } else if ($b)
                    die;
                else {
                    echo 2;
                }',
            ],
            [
                '<?php

                if ($a) {
                    echo 1;
                } else if ($b) {
                    die;
                } else {
                    echo 2;
                }',
            ],
        ];
    }
}
