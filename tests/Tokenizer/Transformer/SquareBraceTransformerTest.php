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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Transformer\SquareBraceTransformer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\SquareBraceTransformer
 */
final class SquareBraceTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param list<int> $inspectIndexes
     *
     * @dataProvider provideIsShortArrayCases
     */
    public function testIsShortArray(string $source, array $inspectIndexes, bool $expected): void
    {
        $transformer = new SquareBraceTransformer();
        $reflection = new \ReflectionObject($transformer);
        $method = $reflection->getMethod('isShortArray');
        $method->setAccessible(true);

        $tokens = Tokens::fromCode($source);
        foreach ($inspectIndexes as $index) {
            static::assertTrue($tokens->offsetExists($index), sprintf('Index %d does not exist.', $index));
        }

        foreach ($tokens as $index => $token) {
            if (\in_array($index, $inspectIndexes, true)) {
                static::assertSame('[', $tokens[$index]->getContent(), sprintf('Token @ index %d must have content \']\'', $index));
                $exp = $expected;
            } elseif ('[' === $tokens[$index]->getContent()) {
                $exp = !$expected;
            } else {
                continue;
            }

            static::assertSame(
                $expected,
                $method->invoke($transformer, $tokens, $index),
                sprintf('Excepted token "%s" @ index %d %sto be detected as short array.', $tokens[$index]->toJson(), $index, $exp ? '' : 'not ')
            );
        }
    }

    public function provideIsShortArrayCases(): array
    {
        return [
            ['<?php $a=[];', [3], false],
            ['<?php [$a] = [$b];', [7], false],
            ['<?php [$a] = $b;', [1], false],
            ['<?php [$a] = [$b] = [$b];', [1], false],
            ['<?php function A(){}[$a] = [$b] = [$b];', [8], false],
            ['<?php [$foo, $bar] = [$baz, $bat] = [$a, $b];', [10], false],
            ['<?php [[$a, $b], [$c, $d]] = [[1, 2], [3, 4]];', [1], false],
            ['<?php ["a" => $a, "b" => $b, "c" => $c] = $array;', [1], false],
            ['<?php [$a, $b,, [$c, $d]] = $a;', [1, 9], false],
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_ARRAY_SQUARE_BRACE_OPEN,
                CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ]
        );
    }

    public function provideProcessCases(): array
    {
        return [
            'Array offset only.' => [
                '<?php $a = array(); $a[] = 0; $a[1] = 2;',
            ],
            'Short array construction.' => [
                '<?php $b = [1, 2, 3];',
                [
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php function foo(array $c = [ ]) {}',
                [
                    11 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [];',
                [
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    2 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [1, "foo"];',
                [
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    6 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [[]];',
                [
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    2 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    3 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                    4 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php ["foo", ["bar", "baz"]];',
                [
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    10 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                    11 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php (array) [1, 2];',
                [
                    3 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    8 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [1,2][$x];',
                [
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    5 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php $a[] = []?>',
                [
                    7 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    8 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php $b = [1];',
                [
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    7 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php $c[] = 2?>',
            ],
            [
                '<?php $d[3] = 4;',
            ],
            [
                '<?php $e = [];',
                [
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    6 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php array();',
            ],
            [
                '<?php $x[] = 1;',
            ],
            [
                '<?php $x[1];',
            ],
            [
                '<?php $x [ 1 ];',
            ],
            [
                '<?php ${"x"}[1];',
            ],
            [
                '<?php FOO[1];',
            ],
            [
                '<?php array("foo")[1];',
            ],
            [
                '<?php foo()[1];',
            ],
            [
                '<?php "foo"[1];//[]',
            ],
            [
                '<?php

class Test
{
    public function updateAttributeKey($key, $value)
    {
        $this->{camel_case($attributes)}[$key] = $value;
    }
}',
            ],
            [
                '<?php [$a, $b, $c] = [1, 2, 3];',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    9 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    13 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    21 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php ["a" => $a, "b" => $b, "c" => $c] = $array;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    21 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [$e] = $d; if ($a){}[$a, $b] = b();',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    3 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    17 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    22 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php $a = [$x] = [$y] = [$z] = [];', // this sample makes no sense, however is in valid syntax
                [
                    5 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    11 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    13 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    17 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    19 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    23 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    24 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [$$a, $b] = $array;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [$a, $b,, [$c, $d]] = $a;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    9 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    14 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    15 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            'nested I' => [
                '<?php [$a[]] = $b;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    5 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            'nested II (with array offset)' => [
                '<?php [$a[1]] = $b;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    6 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            'nested III' => [
                '<?php [$a[1], [$b], $c[2]] = $d;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    8 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    10 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    17 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [[[$a]/**/], $b[1], [/**/[$c]] /** */ ] = $d[1][2][3];',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    2 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    3 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    5 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    16 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    18 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    20 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    21 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    25 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php foreach ($z as [$a, $b]) {}',
                [
                    8 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    13 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php foreach ($a as $key => [$x, $y]) {}',
                [
                    12 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    17 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [$key => [$x, $y]];',
                [
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    6 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    11 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                    12 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php array($key => [$x, $y]);',
                [
                    7 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    12 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [$key => [$x, $y] = foo()];',
                [
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    6 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    11 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    18 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ],
            ],
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcess72Cases
     */
    public function testProcess72(string $source, array $expectedTokens): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ]
        );
    }

    public function provideProcess72Cases(): array
    {
        return [
            [
                '<?php [&$a, $b] = $a;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [$a, &$b] = $a;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [&$a, &$b] = $a;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    8 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
            [
                '<?php [[ [&$a, &$b], [&$c] ], [&$d/* */]] = $e;',
                [
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    2 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    4 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    11 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    14 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    17 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    19 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    22 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    26 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    27 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ],
            ],
        ];
    }
}
