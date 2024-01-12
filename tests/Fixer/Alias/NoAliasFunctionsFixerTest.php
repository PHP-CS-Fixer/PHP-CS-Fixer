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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer
 */
final class NoAliasFunctionsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, string[]> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        $defaultSets = [
            '@internal',
            '@IMAP',
            '@pg',
        ];

        foreach (self::provideAllCases() as $set => $cases) {
            if (\in_array($set, $defaultSets, true)) {
                yield from $cases;
            } else {
                foreach ($cases as $case) {
                    yield [$case[0]];
                }
            }
        }

        // static case to fix - in case previous generation is broken
        yield [
            '<?php is_int($a);',
            '<?php is_integer($a);',
        ];

        yield [
            '<?php socket_set_option($a, $b, $c, $d);',
            '<?php socket_setopt($a, $b, $c, $d);',
        ];

        yield 'dns -> mxrr' => [
            '<?php getmxrr();',
            '<?php dns_get_mx();',
        ];

        yield [
            '<?php $b=is_int(count(implode($b,$a)));',
            '<?php $b=is_integer(sizeof(join($b,$a)));',
        ];

        yield [
            <<<'EOD'
                <?php
                interface JoinInterface
                {
                    public function &join();
                }

                abstract class A
                {
                    abstract public function join($a);

                    public function is_integer($a)
                    {
                        $fputs = "is_double(\$a);\n"; // key_exists($b, $c);
                        echo $fputs."\$is_writable";
                        \B::close();
                        Scope\is_long();
                        namespace\is_long();
                        $a->pos();
                        new join();
                        new \join();
                        new ScopeB\join(mt_rand(0, 100));
                    }
                }
                EOD,
        ];

        yield '@internal' => [
            <<<'EOD'
                <?php
                                $a = rtrim($b);
                                $a = imap_header($imap_stream, 1);
                                mbereg_search_getregs();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_header($imap_stream, 1);
                                mbereg_search_getregs();
                EOD."\n            ",
            ['sets' => ['@internal']],
        ];

        yield '@IMAP' => [
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_headerinfo($imap_stream, 1);
                                mb_ereg_search_getregs();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_header($imap_stream, 1);
                                mb_ereg_search_getregs();
                EOD."\n            ",
            ['sets' => ['@IMAP']],
        ];

        yield '@mbreg' => [
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_header($imap_stream, 1);
                                mb_ereg_search_getregs();
                                mktime();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_header($imap_stream, 1);
                                mbereg_search_getregs();
                                mktime();
                EOD."\n            ",
            ['sets' => ['@mbreg']],
        ];

        yield '@all' => [
            <<<'EOD'
                <?php
                                $a = rtrim($b);
                                $a = imap_headerinfo($imap_stream, 1);
                                mb_ereg_search_getregs();
                                time();
                                time();
                                $foo = exif_read_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);

                                mktime($a);
                                echo gmmktime(1, 2, 3, 4, 5, 6);
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_header($imap_stream, 1);
                                mbereg_search_getregs();
                                mktime();
                                gmmktime();
                                $foo = read_exif_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);

                                mktime($a);
                                echo gmmktime(1, 2, 3, 4, 5, 6);
                EOD."\n            ",
            ['sets' => ['@all']],
        ];

        yield '@IMAP, @mbreg' => [
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_headerinfo($imap_stream, 1);
                                mb_ereg_search_getregs();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a = chop($b);
                                $a = imap_header($imap_stream, 1);
                                mbereg_search_getregs();
                EOD."\n            ",
            ['sets' => ['@IMAP', '@mbreg']],
        ];

        yield '@time' => [
            <<<'EOD'
                <?php
                                time();
                                time();

                                MKTIME($A);
                                ECHO GMMKTIME(1, 2, 3, 4, 5, 6);
                EOD."\n            ",
            <<<'EOD'
                <?php
                                MKTIME();
                                GMMKTIME();

                                MKTIME($A);
                                ECHO GMMKTIME(1, 2, 3, 4, 5, 6);
                EOD."\n            ",
            ['sets' => ['@time']],
        ];

        yield '@exif' => [
            <<<'EOD'
                <?php
                                $foo = exif_read_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $foo = read_exif_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);
                EOD."\n            ",
            ['sets' => ['@exif']],
        ];

        foreach (self::provideAllCases() as $set => $cases) {
            foreach ($cases as $case) {
                yield [
                    $case[0],
                    $case[1] ?? null,
                    ['sets' => [$set]],
                ];
            }
        }
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'simple 8.1' => [
            '<?php $a = is_double(...);',
        ];
    }

    private static function provideAllCases(): iterable
    {
        $reflectionConstant = new \ReflectionClassConstant(NoAliasFunctionsFixer::class, 'SETS');

        /** @var array<string, string[]> $allAliases */
        $allAliases = $reflectionConstant->getValue();

        $sets = $allAliases;
        unset($sets['@time']); // Tested manually
        $sets = array_keys($sets);

        foreach ($sets as $set) {
            $aliases = $allAliases[$set];
            $cases = [];

            foreach ($aliases as $alias => $master) {
                // valid cases
                $cases[] = ["<?php \$smth->{$alias}(\$a);"];
                $cases[] = ["<?php {$alias}Smth(\$a);"];
                $cases[] = ["<?php smth_{$alias}(\$a);"];
                $cases[] = ["<?php new {$alias}(\$a);"];
                $cases[] = ["<?php new Smth\\{$alias}(\$a);"];
                $cases[] = ["<?php Smth\\{$alias}(\$a);"];
                $cases[] = ["<?php namespace\\{$alias}(\$a);"];
                $cases[] = ["<?php Smth::{$alias}(\$a);"];
                $cases[] = ["<?php new {$alias}\\smth(\$a);"];
                $cases[] = ["<?php {$alias}::smth(\$a);"];
                $cases[] = ["<?php {$alias}\\smth(\$a);"];
                $cases[] = ['<?php "SELECT ... '.$alias.'(\$a) ...";'];
                $cases[] = ['<?php "SELECT ... '.strtoupper($alias).'($a) ...";'];
                $cases[] = ["<?php 'test'.'{$alias}' . 'in concatenation';"];
                $cases[] = ['<?php "test" . "'.$alias.'"."in concatenation";'];
                $cases[] = [
                    <<<'EOD'
                        <?php
                            class
                        EOD.' '.ucfirst($alias).<<<'EOD'
                        ing
                            {
                                const
                        EOD.' '.$alias.<<<'EOD'
                         = 1;

                                public function
                        EOD.' '.$alias.'($'.$alias.<<<'EOD'
                        )
                                {
                                    if (defined("
                        EOD.$alias.'") || $'.$alias.' instanceof '.$alias.<<<'EOD'
                        ) {
                                        echo
                        EOD.' '.$alias.<<<'EOD'
                        ;
                                    }
                                }
                            }

                            class
                        EOD.' '.$alias.' extends '.ucfirst($alias).<<<'EOD'
                        ing{
                                const
                        EOD.' '.$alias.' = "'.$alias.<<<'EOD'
                        ";
                            }
                        EOD."\n    ",
                ];

                // cases to be fixed
                $cases[] = [
                    "<?php {$master}(\$a);",
                    "<?php {$alias}(\$a);",
                ];

                $cases[] = [
                    "<?php \\{$master}(\$a);",
                    "<?php \\{$alias}(\$a);",
                ];

                $cases[] = [
                    <<<EOD
                        <?php {$master}
                                                        (\$a);
                        EOD,
                    <<<EOD
                        <?php {$alias}
                                                        (\$a);
                        EOD,
                ];

                $cases[] = [
                    "<?php /* foo */ {$master} /** bar */ (\$a);",
                    "<?php /* foo */ {$alias} /** bar */ (\$a);",
                ];

                $cases[] = [
                    "<?php a({$master}());",
                    "<?php a({$alias}());",
                ];

                $cases[] = [
                    "<?php a(\\{$master}());",
                    "<?php a(\\{$alias}());",
                ];
            }

            yield $set => $cases;
        }
    }
}
