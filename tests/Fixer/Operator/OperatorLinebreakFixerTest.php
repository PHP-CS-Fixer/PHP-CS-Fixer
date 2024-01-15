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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author  Kuba Werłos <werlos@gmail.com>
 *
 * @covers \PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer
 *
 * @internal
 */
final class OperatorLinebreakFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
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
        foreach (self::pairs() as $key => $value) {
            yield sprintf('%s when position is "beginning"', $key) => $value;

            yield sprintf('%s when position is "end"', $key) => [
                $value[1],
                $value[0],
                ['position' => 'end'],
            ];
        }

        yield 'ignore add operator when only booleans enabled' => [
            <<<'EOD'
                <?php
                return $foo
                    +
                    $bar;

                EOD,
            null,
            ['only_booleans' => true],
        ];

        yield 'handle operator when on separate line when position is "beginning"' => [
            <<<'EOD'
                <?php
                return $foo
                    || $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo
                    ||
                    $bar;

                EOD,
        ];

        yield 'handle operator when on separate line when position is "end"' => [
            <<<'EOD'
                <?php
                return $foo ||
                    $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo
                    ||
                    $bar;

                EOD,
            ['position' => 'end'],
        ];

        yield 'handle Elvis operator with space inside' => [
            <<<'EOD'
                <?php
                return $foo
                    ?: $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo ? :
                    $bar;

                EOD,
        ];

        yield 'handle Elvis operator with space inside when position is "end"' => [
            <<<'EOD'
                <?php
                return $foo ?:
                    $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo
                    ? : $bar;

                EOD,
            ['position' => 'end'],
        ];

        yield 'handle Elvis operator with comment inside' => [
            <<<'EOD'
                <?php
                return $foo/* Lorem ipsum */
                    ?: $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo ?/* Lorem ipsum */:
                    $bar;

                EOD,
        ];

        yield 'handle Elvis operators with comment inside when position is "end"' => [
            <<<'EOD'
                <?php
                return $foo ?:
                    /* Lorem ipsum */$bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo
                    ?/* Lorem ipsum */: $bar;

                EOD,
            ['position' => 'end'],
        ];

        yield 'assign by reference' => [
            <<<'EOD'
                <?php
                                $a
                                    = $b;
                                $c =&
                                     $d;
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a =
                                    $b;
                                $c =&
                                     $d;
                EOD."\n            ",
        ];

        yield 'passing by reference' => [
            <<<'EOD'
                <?php
                                function foo(
                                    &$a,
                                    &$b,
                                    int
                                        &$c,
                                    \Bar\Baz
                                        &$d
                                ) {};
                EOD,
            null,
            ['position' => 'end'],
        ];

        yield 'multiple switches' => [
            <<<'EOD'
                <?php
                                switch ($foo) {
                                   case 1:
                                      break;
                                   case 2:
                                      break;
                                }
                                switch($bar) {
                                   case 1:
                                      break;
                                   case 2:
                                      break;
                                }
                EOD,
        ];

        yield 'return type' => [
            <<<'EOD'
                <?php
                            function foo()
                            :
                            bool
                            {};
                EOD,
        ];

        yield 'go to' => [<<<'EOD'
            <?php
                            prepare_value:
                            $objectsPool[$value] = [$id = \count($objectsPool)];
            EOD."\n        "];

        yield 'alternative syntax' => [
            <<<'EOD'
                <?php
                if (true):
                    echo 1;
                else:
                    echo 2;
                endif;

                while (true):
                    echo "na";
                endwhile;

                EOD,
            null,
            ['position' => 'beginning'],
        ];

        yield 'nullable type when position is "end"' => [
            <<<'EOD'
                <?php
                                function foo(
                                    ?int $x,
                                    ?int $y,
                                    ?int $z
                                ) {};
                EOD,
            null,
            ['position' => 'end'],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'handle ?-> operator' => [
            <<<'EOD'
                <?php
                                    $foo
                                        ?-> $bar;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $foo ?->
                                        $bar;
                EOD."\n                ",
        ];
    }

    /**
     * @return iterable<array{0: string, 1: null|string, 2?: array<string, mixed>}>
     */
    private static function pairs(): iterable
    {
        yield 'handle equal sign' => [
            <<<'EOD'
                <?php
                $foo
                    = $bar;

                EOD,
            <<<'EOD'
                <?php
                $foo =
                    $bar;

                EOD,
        ];

        yield 'handle add operator' => [
            <<<'EOD'
                <?php
                return $foo
                    + $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo +
                    $bar;

                EOD,
            ['only_booleans' => false],
        ];

        yield 'handle uppercase operator' => [
            <<<'EOD'
                <?php
                return $foo
                    AND $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo AND
                    $bar;

                EOD,
        ];

        yield 'handle concatenation operator' => [
            <<<'EOD'
                <?php
                return $foo
                    .$bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo.
                    $bar;

                EOD,
        ];

        yield 'handle ternary operator' => [
            <<<'EOD'
                <?php
                return $foo
                    ? $bar
                    : $baz;

                EOD,
            <<<'EOD'
                <?php
                return $foo ?
                    $bar :
                    $baz;

                EOD,
        ];

        yield 'handle multiple operators' => [
            <<<'EOD'
                <?php
                return $foo
                    || $bar
                    || $baz;

                EOD,
            <<<'EOD'
                <?php
                return $foo ||
                    $bar ||
                    $baz;

                EOD,
        ];

        yield 'handle multiple operators with nested' => [
            <<<'EOD'
                <?php
                return $foo
                    || $bar
                    || ($bar2 || $bar3)
                    || $baz;

                EOD,
            <<<'EOD'
                <?php
                return $foo ||
                    $bar ||
                    ($bar2 || $bar3) ||
                    $baz;

                EOD,
        ];

        yield 'handle operator when no whitespace is before' => [
            <<<'EOD'
                <?php
                function foo() {
                    return $a
                        ||$b;
                }

                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    return $a||
                        $b;
                }

                EOD,
        ];

        yield 'handle operator with one-line comments' => [
            <<<'EOD'
                <?php
                function getNewCuyamaTotal() {
                    return 562 // Population
                        + 2150 // Ft. above sea level
                        + 1951; // Established
                }

                EOD,
            <<<'EOD'
                <?php
                function getNewCuyamaTotal() {
                    return 562 + // Population
                        2150 + // Ft. above sea level
                        1951; // Established
                }

                EOD,
        ];

        yield 'handle operator with PHPDoc comments' => [
            <<<'EOD'
                <?php
                function getNewCuyamaTotal() {
                    return 562 /** Population */
                        + 2150 /** Ft. above sea level */
                        + 1951; /** Established */
                }

                EOD,
            <<<'EOD'
                <?php
                function getNewCuyamaTotal() {
                    return 562 + /** Population */
                        2150 + /** Ft. above sea level */
                        1951; /** Established */
                }

                EOD,
        ];

        yield 'handle operator with multiple comments next to each other' => [
            <<<'EOD'
                <?php
                function foo() {
                    return isThisTheRealLife() // First comment
                        // Second comment
                        // Third comment
                        || isThisJustFantasy();
                }

                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    return isThisTheRealLife() || // First comment
                        // Second comment
                        // Third comment
                        isThisJustFantasy();
                }

                EOD,
        ];

        yield 'handle nested operators' => [
            <<<'EOD'
                <?php
                function foo() {
                    return $a
                        && (
                            $b
                            || $c
                        )
                        && $d;
                }

                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    return $a &&
                        (
                            $b ||
                            $c
                        ) &&
                        $d;
                }

                EOD,
        ];

        yield 'handle Elvis operator' => [
            <<<'EOD'
                <?php
                return $foo
                    ?: $bar;

                EOD,
            <<<'EOD'
                <?php
                return $foo ?:
                    $bar;

                EOD,
        ];

        yield 'handle ternary operator inside of switch' => [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                        return $isOK ? 1 : -1;
                    case (
                            $a
                            ? 2
                            : 3
                        ) :
                        return 23;
                    case $b[
                            $a
                            ? 4
                            : 5
                        ]
                        : return 45;
                }

                EOD,
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                        return $isOK ? 1 : -1;
                    case (
                            $a ?
                            2 :
                            3
                        ) :
                        return 23;
                    case $b[
                            $a ?
                            4 :
                            5
                        ]
                        : return 45;
                }

                EOD,
        ];

        yield 'handle ternary operator with switch inside' => [
            <<<'EOD'
                <?php
                                $a
                                    ? array_map(
                                        function () {
                                            switch (true) {
                                                case 1:
                                                    return true;
                                            }
                                        },
                                        [1, 2, 3]
                                    )
                                    : false;
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a ?
                                    array_map(
                                        function () {
                                            switch (true) {
                                                case 1:
                                                    return true;
                                            }
                                        },
                                        [1, 2, 3]
                                    ) :
                                    false;
                EOD."\n            ",
        ];

        $operators = [
            '+', '-', '*', '/', '%', '**', // Arithmetic
            '+=', '-=', '*=', '/=', '%=', '**=', // Arithmetic assignment
            '=', // Assignment
            '&', '|', '^', '<<', '>>', // Bitwise
            '&=', '|=', '^=', '<<=', '>>=', // Bitwise assignment
            '==', '===', '!=', '<>', '!==', '<', '>', '<=', '>=',  // Comparison
            'and', 'or', 'xor', '&&', '||', // Logical
            '.', '.=', // String
            '->', // Object
            '::', // Scope Resolution
        ];

        $operators[] = '??';
        $operators[] = '<=>';

        foreach ($operators as $operator) {
            yield sprintf('handle %s operator', $operator) => [
                sprintf(<<<'EOD'
                    <?php
                                        $foo
                                            %s $bar;
                    EOD."\n                ", $operator),
                sprintf(<<<'EOD'
                    <?php
                                        $foo %s
                                            $bar;
                    EOD."\n                ", $operator),
            ];
        }

        yield 'handle => operator' => [
            <<<'EOD'
                <?php
                [$foo
                    => $bar];

                EOD,
            <<<'EOD'
                <?php
                [$foo =>
                    $bar];

                EOD,
        ];

        yield 'handle & operator with constant' => [
            <<<'EOD'
                <?php
                \Foo::bar
                    & $baz;

                EOD,
            <<<'EOD'
                <?php
                \Foo::bar &
                    $baz;

                EOD,
        ];
    }
}
