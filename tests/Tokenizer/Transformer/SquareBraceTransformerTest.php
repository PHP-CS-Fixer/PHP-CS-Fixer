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
 *
 * @phpstan-import-type _TransformerTestExpectedKindsUnderIndex from AbstractTransformerTestCase
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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

        $tokens = Tokens::fromCode($source);
        foreach ($inspectIndexes as $index) {
            self::assertTrue($tokens->offsetExists($index), \sprintf('Index %d does not exist.', $index));
        }

        foreach ($tokens as $index => $token) {
            if (\in_array($index, $inspectIndexes, true)) {
                self::assertSame('[', $token->getContent(), \sprintf('Token @ index %d must have content \']\'', $index));
                $exp = $expected;
            } elseif ('[' === $token->getContent()) {
                $exp = !$expected;
            } else {
                continue;
            }

            self::assertSame(
                $expected,
                \Closure::bind(static fn (SquareBraceTransformer $transformer): bool => $transformer->isShortArray($tokens, $index), null, SquareBraceTransformer::class)($transformer),
                \sprintf('Excepted token "%s" @ index %d %sto be detected as short array.', $token->toJson(), $index, $exp ? '' : 'not '),
            );
        }
    }

    /**
     * @return iterable<int, array{string, list<int>, bool}>
     */
    public static function provideIsShortArrayCases(): iterable
    {
        yield ['<?php $a=[];', [3], false];

        yield ['<?php [$a] = [$b];', [7], false];

        yield ['<?php [$a] = $b;', [1], false];

        yield ['<?php [$a] = [$b] = [$b];', [1], false];

        yield ['<?php function A(){}[$a] = [$b] = [$b];', [8], false];

        yield ['<?php [$foo, $bar] = [$baz, $bat] = [$a, $b];', [10], false];

        yield ['<?php [[$a, $b], [$c, $d]] = [[1, 2], [3, 4]];', [1], false];

        yield ['<?php ["a" => $a, "b" => $b, "c" => $c] = $array;', [1], false];

        yield ['<?php [$a, $b,, [$c, $d]] = $a;', [1, 9], false];
    }

    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
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
            ],
        );
    }

    /**
     * @return iterable<array{0: string, 1?: _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcessCases(): iterable
    {
        yield 'Array offset only.' => [
            '<?php $a = array(); $a[] = 0; $a[1] = 2;',
        ];

        yield 'Short array construction.' => [
            '<?php $b = [1, 2, 3];',
            [
                5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php function foo(array $c = [ ]) {}',
            [
                11 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [];',
            [
                1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                2 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [1, "foo"];',
            [
                1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                6 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [[]];',
            [
                1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                2 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                3 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                4 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php ["foo", ["bar", "baz"]];',
            [
                1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                10 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                11 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php (array) [1, 2];',
            [
                3 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                8 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [1,2][$x];',
            [
                1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                5 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php $a[] = []?>',
            [
                7 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                8 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php $b = [1];',
            [
                5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                7 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php $c[] = 2?>',
        ];

        yield [
            '<?php $d[3] = 4;',
        ];

        yield [
            '<?php $e = [];',
            [
                5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                6 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php array();',
        ];

        yield [
            '<?php $x[] = 1;',
        ];

        yield [
            '<?php $x[1];',
        ];

        yield [
            '<?php $x [ 1 ];',
        ];

        yield [
            '<?php ${"x"}[1];',
        ];

        yield [
            '<?php FOO[1];',
        ];

        yield [
            '<?php array("foo")[1];',
        ];

        yield [
            '<?php foo()[1];',
        ];

        yield [
            '<?php "foo"[1];//[]',
        ];

        yield [
            '<?php

class Test
{
    public function updateAttributeKey($key, $value)
    {
        $this->{camel_case($attributes)}[$key] = $value;
    }
}',
        ];

        yield [
            '<?php [$a, $b, $c] = [1, 2, 3];',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                9 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                13 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                21 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php ["a" => $a, "b" => $b, "c" => $c] = $array;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                21 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [$e] = $d; if ($a){}[$a, $b] = b();',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                3 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                17 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                22 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
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
        ];

        yield [
            '<?php [$$a, $b] = $array;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [$a, $b,, [$c, $d]] = $a;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                9 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                14 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                15 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield 'nested I' => [
            '<?php [$a[]] = $b;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                5 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield 'nested II (with array offset)' => [
            '<?php [$a[1]] = $b;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                6 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield 'nested III' => [
            '<?php [$a[1], [$b], $c[2]] = $d;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                8 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                10 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                17 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
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
        ];

        yield [
            '<?php foreach ($z as [$a, $b]) {}',
            [
                8 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                13 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php foreach ($a as $key => [$x, $y]) {}',
            [
                12 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                17 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [$key => [$x, $y]];',
            [
                1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                6 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                11 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                12 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php array($key => [$x, $y]);',
            [
                7 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                12 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [$key => [$x, $y] = foo()];',
            [
                1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                6 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                11 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                18 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [&$a, $b] = $a;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [$a, &$b] = $a;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
            '<?php [&$a, &$b] = $a;',
            [
                1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                8 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            ],
        ];

        yield [
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
        ];
    }
}
