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
        foreach (static::pairs() as $key => $value) {
            yield sprintf('%s when position is "beginning"', $key) => $value;

            yield sprintf('%s when position is "end"', $key) => [
                $value[1],
                $value[0],
                ['position' => 'end'],
            ];
        }

        yield 'ignore add operator when only booleans enabled' => [
            '<?php
return $foo
    +
    $bar;
',
            null,
            ['only_booleans' => true],
        ];

        yield 'handle operator when on separate line when position is "beginning"' => [
            '<?php
return $foo
    || $bar;
',
            '<?php
return $foo
    ||
    $bar;
',
        ];

        yield 'handle operator when on separate line when position is "end"' => [
            '<?php
return $foo ||
    $bar;
',
            '<?php
return $foo
    ||
    $bar;
',
            ['position' => 'end'],
        ];

        yield 'handle Elvis operator with space inside' => [
            '<?php
return $foo
    ?: $bar;
',
            '<?php
return $foo ? :
    $bar;
',
        ];

        yield 'handle Elvis operator with space inside when position is "end"' => [
            '<?php
return $foo ?:
    $bar;
',
            '<?php
return $foo
    ? : $bar;
',
            ['position' => 'end'],
        ];

        yield 'handle Elvis operator with comment inside' => [
            '<?php
return $foo/* Lorem ipsum */
    ?: $bar;
',
            '<?php
return $foo ?/* Lorem ipsum */:
    $bar;
',
        ];

        yield 'handle Elvis operators with comment inside when position is "end"' => [
            '<?php
return $foo ?:
    /* Lorem ipsum */$bar;
',
            '<?php
return $foo
    ?/* Lorem ipsum */: $bar;
',
            ['position' => 'end'],
        ];

        yield 'assign by reference' => [
            '<?php
                $a
                    = $b;
                $c =&
                     $d;
            ',
            '<?php
                $a =
                    $b;
                $c =&
                     $d;
            ',
        ];

        yield 'passing by reference' => [
            '<?php
                function foo(
                    &$a,
                    &$b,
                    int
                        &$c,
                    \Bar\Baz
                        &$d
                ) {};',
            null,
            ['position' => 'end'],
        ];

        yield 'multiple switches' => [
            '<?php
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
                }',
        ];

        yield 'return type' => [
            '<?php
            function foo()
            :
            bool
            {};',
        ];

        yield 'go to' => ['<?php
                prepare_value:
                $objectsPool[$value] = [$id = \count($objectsPool)];
        '];

        yield 'alternative syntax' => [
            '<?php
if (true):
    echo 1;
else:
    echo 2;
endif;

while (true):
    echo "na";
endwhile;
',
            null,
            ['position' => 'beginning'],
        ];

        yield 'nullable type when position is "end"' => [
            '<?php
                function foo(
                    ?int $x,
                    ?int $y,
                    ?int $z
                ) {};',
            null,
            ['position' => 'end'],
        ];
    }

    /**
     * @return iterable<array{0: string, 1: null|string, 2?: array<string, mixed>}>
     */
    private static function pairs(): iterable
    {
        yield 'handle equal sign' => [
            '<?php
$foo
    = $bar;
',
            '<?php
$foo =
    $bar;
',
        ];

        yield 'handle add operator' => [
            '<?php
return $foo
    + $bar;
',
            '<?php
return $foo +
    $bar;
',
            ['only_booleans' => false],
        ];

        yield 'handle uppercase operator' => [
            '<?php
return $foo
    AND $bar;
',
            '<?php
return $foo AND
    $bar;
',
        ];

        yield 'handle concatenation operator' => [
            '<?php
return $foo
    .$bar;
',
            '<?php
return $foo.
    $bar;
',
        ];

        yield 'handle ternary operator' => [
            '<?php
return $foo
    ? $bar
    : $baz;
',
            '<?php
return $foo ?
    $bar :
    $baz;
',
        ];

        yield 'handle multiple operators' => [
            '<?php
return $foo
    || $bar
    || $baz;
',
            '<?php
return $foo ||
    $bar ||
    $baz;
',
        ];

        yield 'handle multiple operators with nested' => [
            '<?php
return $foo
    || $bar
    || ($bar2 || $bar3)
    || $baz;
',
            '<?php
return $foo ||
    $bar ||
    ($bar2 || $bar3) ||
    $baz;
',
        ];

        yield 'handle operator when no whitespace is before' => [
            '<?php
function foo() {
    return $a
        ||$b;
}
',
            '<?php
function foo() {
    return $a||
        $b;
}
',
        ];

        yield 'handle operator with one-line comments' => [
            '<?php
function getNewCuyamaTotal() {
    return 562 // Population
        + 2150 // Ft. above sea level
        + 1951; // Established
}
',
            '<?php
function getNewCuyamaTotal() {
    return 562 + // Population
        2150 + // Ft. above sea level
        1951; // Established
}
',
        ];

        yield 'handle operator with PHPDoc comments' => [
            '<?php
function getNewCuyamaTotal() {
    return 562 /** Population */
        + 2150 /** Ft. above sea level */
        + 1951; /** Established */
}
',
            '<?php
function getNewCuyamaTotal() {
    return 562 + /** Population */
        2150 + /** Ft. above sea level */
        1951; /** Established */
}
',
        ];

        yield 'handle operator with multiple comments next to each other' => [
            '<?php
function foo() {
    return isThisTheRealLife() // First comment
        // Second comment
        // Third comment
        || isThisJustFantasy();
}
',
            '<?php
function foo() {
    return isThisTheRealLife() || // First comment
        // Second comment
        // Third comment
        isThisJustFantasy();
}
',
        ];

        yield 'handle nested operators' => [
            '<?php
function foo() {
    return $a
        && (
            $b
            || $c
        )
        && $d;
}
',
            '<?php
function foo() {
    return $a &&
        (
            $b ||
            $c
        ) &&
        $d;
}
',
        ];

        yield 'handle Elvis operator' => [
            '<?php
return $foo
    ?: $bar;
',
            '<?php
return $foo ?:
    $bar;
',
        ];

        yield 'handle ternary operator inside of switch' => [
            '<?php
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
',
            '<?php
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
',
        ];

        yield 'handle ternary operator with switch inside' => [
            '<?php
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
            ',
            '<?php
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
            ',
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

        if (\PHP_VERSION_ID >= 80000) {
            $operators[] = '?->';
        }

        foreach ($operators as $operator) {
            yield sprintf('handle %s operator', $operator) => [
                sprintf('<?php
                    $foo
                        %s $bar;
                ', $operator),
                sprintf('<?php
                    $foo %s
                        $bar;
                ', $operator),
            ];
        }

        yield 'handle => operator' => [
            '<?php
[$foo
    => $bar];
',
            '<?php
[$foo =>
    $bar];
',
        ];

        yield 'handle & operator with constant' => [
            '<?php
\Foo::bar
    & $baz;
',
            '<?php
\Foo::bar &
    $baz;
',
        ];
    }
}
