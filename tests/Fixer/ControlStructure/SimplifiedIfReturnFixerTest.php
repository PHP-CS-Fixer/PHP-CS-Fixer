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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ControlStructure\SimplifiedIfReturnFixer>
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\SimplifiedIfReturnFixer
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class SimplifiedIfReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
            '<?php return (bool) ($foo)      ;',
            '<?php if ($foo) { return true; } return false;',
        ];

        yield 'simple-negative' => [
            '<?php return ! ($foo)      ;',
            '<?php if ($foo) { return false; } return true;',
        ];

        yield 'simple-negative II' => [
            '<?php return ! (!$foo && $a())      ;',
            '<?php if (!$foo && $a()) { return false; } return true;',
        ];

        yield 'simple-braceless' => [
            '<?php return (bool) ($foo)    ;',
            '<?php if ($foo) return true; return false;',
        ];

        yield 'simple-braceless-negative' => [
            '<?php return ! ($foo)    ;',
            '<?php if ($foo) return false; return true;',
        ];

        yield 'bug-consecutive-ifs' => [
            '<?php if ($bar) { return 1; } return (bool) ($foo)      ;',
            '<?php if ($bar) { return 1; } if ($foo) { return true; } return false;',
        ];

        yield 'bug-consecutive-ifs-negative' => [
            '<?php if ($bar) { return 1; } return ! ($foo)      ;',
            '<?php if ($bar) { return 1; } if ($foo) { return false; } return true;',
        ];

        yield 'bug-consecutive-ifs-braceless' => [
            '<?php if ($bar) return 1; return (bool) ($foo)    ;',
            '<?php if ($bar) return 1; if ($foo) return true; return false;',
        ];

        yield 'bug-consecutive-ifs-braceless-negative' => [
            '<?php if ($bar) return 1; return ! ($foo)    ;',
            '<?php if ($bar) return 1; if ($foo) return false; return true;',
        ];

        yield [
            <<<'EOT'
                <?php
                function f1() { return (bool) ($f1)      ; }
                function f2() { return true; } return false;
                function f3() { return (bool) ($f3)      ; }
                function f4() { return true; } return false;
                function f5() { return (bool) ($f5)      ; }
                function f6() { return false; } return true;
                function f7() { return ! ($f7)      ; }
                function f8() { return false; } return true;
                function f9() { return ! ($f9)      ; }
                EOT,
            <<<'EOT'
                <?php
                function f1() { if ($f1) { return true; } return false; }
                function f2() { return true; } return false;
                function f3() { if ($f3) { return true; } return false; }
                function f4() { return true; } return false;
                function f5() { if ($f5) { return true; } return false; }
                function f6() { return false; } return true;
                function f7() { if ($f7) { return false; } return true; }
                function f8() { return false; } return true;
                function f9() { if ($f9) { return false; } return true; }
                EOT,
        ];

        yield 'preserve-comments' => [
            <<<'EOT'
                <?php
                // C1
                return (bool)
                # C2
                (
                /* C3 */
                $foo
                /** C4 */
                )
                // C5

                # C6

                // C7

                # C8

                /* C9 */

                /** C10 */

                // C11

                # C12
                ;
                /* C13 */
                EOT,
            <<<'EOT'
                <?php
                // C1
                if
                # C2
                (
                /* C3 */
                $foo
                /** C4 */
                )
                // C5
                {
                # C6
                return
                // C7
                true
                # C8
                ;
                /* C9 */
                }
                /** C10 */
                return
                // C11
                false
                # C12
                ;
                /* C13 */
                EOT,
        ];

        yield 'preserve-comments-braceless' => [
            <<<'EOT'
                <?php
                // C1
                return (bool)
                # C2
                (
                /* C3 */
                $foo
                /** C4 */
                )
                // C5
                # C6

                // C7

                # C8

                /* C9 */
                /** C10 */

                // C11

                # C12
                ;
                /* C13 */
                EOT,
            <<<'EOT'
                <?php
                // C1
                if
                # C2
                (
                /* C3 */
                $foo
                /** C4 */
                )
                // C5
                # C6
                return
                // C7
                true
                # C8
                ;
                /* C9 */
                /** C10 */
                return
                // C11
                false
                # C12
                ;
                /* C13 */
                EOT,
        ];

        yield 'else-if' => [
            '<?php if ($bar) { return $bar; } else return (bool) ($foo)      ;',
            '<?php if ($bar) { return $bar; } else if ($foo) { return true; } return false;',
        ];

        yield 'else-if-negative' => [
            '<?php if ($bar) { return $bar; } else return ! ($foo)      ;',
            '<?php if ($bar) { return $bar; } else if ($foo) { return false; } return true;',
        ];

        yield 'else-if-braceless' => [
            '<?php if ($bar) return $bar; else return (bool) ($foo)    ;',
            '<?php if ($bar) return $bar; else if ($foo) return true; return false;',
        ];

        yield 'else-if-braceless-negative' => [
            '<?php if ($bar) return $bar; else return ! ($foo)    ;',
            '<?php if ($bar) return $bar; else if ($foo) return false; return true;',
        ];

        yield 'elseif' => [
            '<?php if ($bar) { return $bar; } return (bool) ($foo)      ;',
            '<?php if ($bar) { return $bar; } elseif ($foo) { return true; } return false;',
        ];

        yield 'elseif-negative' => [
            '<?php if ($bar) { return $bar; } return ! ($foo)      ;',
            '<?php if ($bar) { return $bar; } elseif ($foo) { return false; } return true;',
        ];

        yield 'elseif-braceless' => [
            '<?php if ($bar) return $bar; return (bool) ($foo)    ;',
            '<?php if ($bar) return $bar; elseif ($foo) return true; return false;',
        ];

        yield 'elseif-braceless-negative' => [
            '<?php if ($bar) return $bar; return ! ($foo)    ;',
            '<?php if ($bar) return $bar; elseif ($foo) return false; return true;',
        ];

        yield 'no braces loops' => [
            '<?php
function foo1(string $str, array $letters): bool
{
    foreach ($letters as $letter)
        if ($str === $letter)
            return true;
    return false;
}

function foo2(int $z): bool
{
    for ($i = 0; $i < 3; ++$i)
        if ($i === $z)
            return true;
    return false;
}

function foo3($y): bool
{
    while ($x = bar())
        if ($x === $z)
            return true;
    return false;
}
',
        ];

        yield 'alternative syntax not supported' => [
            '<?php
if ($foo):
    return true;
else:
    return false;
endif;
',
        ];
    }
}
