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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $finalCases = [];
        $defaultSets = [
            '@internal',
            '@IMAP',
            '@pg',
        ];
        foreach ($this->provideAllCases() as $set => $cases) {
            if (\in_array($set, $defaultSets, true)) {
                $finalCases = array_merge($finalCases, $cases);
            } else {
                foreach ($cases as $case) {
                    $finalCases[] = [$case[0]];
                }
            }
        }

        // static case to fix - in case previous generation is broken
        $finalCases[] = [
            '<?php is_int($a);',
            '<?php is_integer($a);',
        ];

        $finalCases[] = [
            '<?php $b=is_int(count(implode($b,$a)));',
            '<?php $b=is_integer(sizeof(join($b,$a)));',
        ];

        $finalCases[] = [
            '<?php
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
}',
        ];

        return $finalCases;
    }

    /**
     * @param string                  $expected
     * @param null|string             $input
     * @param array<string, string[]> $configuration
     *
     * @dataProvider provideFixWithConfigurationCases
     */
    public function testFixWithConfiguration(string $expected, ?string $input, array $configuration): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideFixWithConfigurationCases()
    {
        $finalCases = [
            '@internal' => [
                '<?php
                    $a = rtrim($b);
                    $a = imap_header($imap_stream, 1);
                    mbereg_search_getregs();
                ',
                '<?php
                    $a = chop($b);
                    $a = imap_header($imap_stream, 1);
                    mbereg_search_getregs();
                ',
                ['sets' => ['@internal']],
            ],
            '@IMAP' => [
                '<?php
                    $a = chop($b);
                    $a = imap_headerinfo($imap_stream, 1);
                    mb_ereg_search_getregs();
                ',
                '<?php
                    $a = chop($b);
                    $a = imap_header($imap_stream, 1);
                    mb_ereg_search_getregs();
                ',
                ['sets' => ['@IMAP']],
            ],
            '@mbreg' => [
                '<?php
                    $a = chop($b);
                    $a = imap_header($imap_stream, 1);
                    mb_ereg_search_getregs();
                    mktime();
                ',
                '<?php
                    $a = chop($b);
                    $a = imap_header($imap_stream, 1);
                    mbereg_search_getregs();
                    mktime();
                ',
                ['sets' => ['@mbreg']],
            ],
            '@all' => [
                '<?php
                    $a = rtrim($b);
                    $a = imap_headerinfo($imap_stream, 1);
                    mb_ereg_search_getregs();
                    time();
                    time();
                    $foo = exif_read_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);

                    mktime($a);
                    echo gmmktime(1, 2, 3, 4, 5, 6);
                ',
                '<?php
                    $a = chop($b);
                    $a = imap_header($imap_stream, 1);
                    mbereg_search_getregs();
                    mktime();
                    gmmktime();
                    $foo = read_exif_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);

                    mktime($a);
                    echo gmmktime(1, 2, 3, 4, 5, 6);
                ',
                ['sets' => ['@all']],
            ],
            '@IMAP, @mbreg' => [
                '<?php
                    $a = chop($b);
                    $a = imap_headerinfo($imap_stream, 1);
                    mb_ereg_search_getregs();
                ',
                '<?php
                    $a = chop($b);
                    $a = imap_header($imap_stream, 1);
                    mbereg_search_getregs();
                ',
                ['sets' => ['@IMAP', '@mbreg']],
            ],
            '@time' => [
                '<?php
                    time();
                    time();

                    MKTIME($A);
                    ECHO GMMKTIME(1, 2, 3, 4, 5, 6);
                ',
                '<?php
                    MKTIME();
                    GMMKTIME();

                    MKTIME($A);
                    ECHO GMMKTIME(1, 2, 3, 4, 5, 6);
                ',
                ['sets' => ['@time']],
            ],
            '@exif' => [
                '<?php
                    $foo = exif_read_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);
                ',
                '<?php
                    $foo = read_exif_data($filename, $sections_needed, $sub_arrays, $read_thumbnail);
                ',
                ['sets' => ['@exif']],
            ],
        ];

        foreach ($this->provideAllCases() as $set => $cases) {
            foreach ($cases as $case) {
                $finalCases[] = [
                    $case[0],
                    $case[1] ?? null,
                    ['sets' => [$set]],
                ];
            }
        }

        return $finalCases;
    }

    private function provideAllCases(): array
    {
        $reflectionConstant = new \ReflectionClassConstant(\PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer::class, 'SETS');
        /** @var array<string, string[]> $aliases */
        $allAliases = $reflectionConstant->getValue();

        $finalCases = [];
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
                    '<?php
    class '.ucfirst($alias).'ing
    {
        const '.$alias.' = 1;

        public function '.$alias.'($'.$alias.')
        {
            if (defined("'.$alias.'") || $'.$alias.' instanceof '.$alias.') {
                echo '.$alias.';
            }
        }
    }

    class '.$alias.' extends '.ucfirst($alias).'ing{
        const '.$alias.' = "'.$alias.'";
    }
    ',
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
                    "<?php {$master}
                                (\$a);",
                    "<?php {$alias}
                                (\$a);",
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

            $finalCases[$set] = $cases;
        }

        return $finalCases;
    }
}
