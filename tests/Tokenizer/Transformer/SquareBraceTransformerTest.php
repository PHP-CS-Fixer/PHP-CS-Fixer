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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Transformer\SquareBraceTransformer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\SquareBraceTransformer
 */
final class SquareBraceTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @requires PHP 7.1
     *
     * @param string $source
     * @param int[]  $inspectIndexes
     * @param bool   $expected
     *
     * @dataProvider provideIsShortArrayCases
     */
    public function testIsShortArray($source, $inspectIndexes, $expected)
    {
        $transformer = new SquareBraceTransformer();
        $reflection = new \ReflectionObject($transformer);
        $method = $reflection->getMethod('isShortArray');
        $method->setAccessible(true);

        $tokens = Tokens::fromCode($source);
        foreach ($inspectIndexes as $index) {
            $this->assertTrue($tokens->offsetExists($index), sprintf('Index %d does not exist.', $index));
        }

        foreach ($tokens as $index => $token) {
            if (in_array($index, $inspectIndexes, true)) {
                $this->assertSame('[', $tokens[$index]->getContent(), sprintf('Token @ index %d must have content \']\'', $index));
                $exp = $expected;
            } elseif ('[' === $tokens[$index]->getContent()) {
                $exp = !$expected;
            } else {
                continue;
            }

            $this->assertSame(
                $expected,
                $method->invoke($transformer, $tokens, $index),
                sprintf('Excepted token "%s" @ index %d %sto be detected as short array.', $tokens[$index]->toJson(), $index, $exp ? '' : 'not ')
            );
        }
    }

    public function provideIsShortArrayCases()
    {
        return array(
            array('<?php $a=[];', array(3), false),
            array('<?php [$a] = [$b];', array(7), false),
            array('<?php [$a] = $b;', array(1), false),
            array('<?php [$a] = [$b] = [$b];', array(1), false),
            array('<?php function A(){}[$a] = [$b] = [$b];', array(8), false),
            array('<?php [$foo, $bar] = [$baz, $bat] = [$a, $b];', array(10), false),
            array('<?php [[$a, $b], [$c, $d]] = [[1, 2], [3, 4]];', array(1), false),
            array('<?php ["a" => $a, "b" => $b, "c" => $c] = $array;', array(1), false),
        );
    }

    /**
     * @param string $source
     * @param array  $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_ARRAY_SQUARE_BRACE_OPEN,
                CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            'Array offset only.' => array(
                '<?php $a = array(); $a[] = 0; $a[1] = 2;',
            ),
            'Short array construction.' => array(
                '<?php $b = [1, 2, 3];',
                array(
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php function foo(array $c = [ ]) {}',
                array(
                    11 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    2 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [1, "foo"];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    6 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [[]];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    2 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    3 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                    4 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php ["foo", ["bar", "baz"]];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    10 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                    11 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php (array) [1, 2];',
                array(
                    3 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    8 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [1,2][$x];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    5 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php $a[] = []?>',
                array(
                    7 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    8 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php $b = [1];',
                array(
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    7 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php $c[] = 2?>',
            ),
            array(
                '<?php $d[3] = 4;',
            ),
            array(
                '<?php $e = [];',
                array(
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    6 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php array();',
            ),
            array(
                '<?php $x[] = 1;',
            ),
            array(
                '<?php $x[1];',
            ),
            array(
                '<?php $x [ 1 ];',
            ),
            array(
                '<?php ${"x"}[1];',
            ),
            array(
                '<?php FOO[1];',
            ),
            array(
                '<?php array("foo")[1];',
            ),
            array(
                '<?php foo()[1];',
            ),
            array(
                '<?php "foo"[1];//[]',
            ),
        );
    }

    /**
     * @param string          $source
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases71
     * @requires PHP 7.1
     */
    public function testProcess71($source, array $expectedTokens)
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_ARRAY_SQUARE_BRACE_OPEN,
                CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            )
        );
    }

    public function provideProcessCases71()
    {
        return array(
            array(
                '<?php [$a, $b, $c] = [1, 2, 3];',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    9 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    13 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    21 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php ["a" => $a, "b" => $b, "c" => $c] = $array;',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    21 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [$e] = $d; if ($a){}[$a, $b] = b();',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    3 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    17 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    22 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php $a = [$x] = [$y] = [$z] = [];', // this sample makes no sense, however is in valid syntax
                array(
                    5 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    11 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    13 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    17 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    19 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    23 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    24 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [$$a, $b] = $array;',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ),
            ),
        );
    }

    /**
     * @param string          $source
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases72
     * @requires PHP 7.2
     */
    public function testProcess72($source, array $expectedTokens)
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_ARRAY_SQUARE_BRACE_OPEN,
                CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            )
        );
    }

    public function provideProcessCases72()
    {
        return array(
            array(
                '<?php [&$a, $b] = $a;',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [$a, &$b] = $a;',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    7 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [&$a, &$b] = $a;',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    8 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                ),
            ),
        );
    }
}
