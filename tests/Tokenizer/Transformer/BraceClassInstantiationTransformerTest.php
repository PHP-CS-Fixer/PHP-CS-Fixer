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

/**
 * @author Sebastiaans Stok <s.stok@rollerscapes.net>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\BraceClassInstantiationTransformer
 */
final class BraceClassInstantiationTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int|string> $expectedTokens
     * @param list<int>              $observedKinds
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens, array $observedKinds = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            $observedKinds
        );
    }

    public static function provideProcessCases(): array
    {
        return [
            [
                '<?php echo (new Process())->getOutput();',
                [
                    3 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    9 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
                [
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php echo (new Process())::getOutput();',
                [
                    3 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    9 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
                [
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php return foo()->bar(new Foo())->bar();',
                [
                    4 => '(',
                    5 => ')',
                    8 => '(',
                    12 => '(',
                    13 => ')',
                    14 => ')',
                    17 => '(',
                    18 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php $foo[0](new Foo())->bar();',
                [
                    5 => '(',
                    9 => '(',
                    10 => ')',
                    11 => ')',
                    14 => '(',
                    15 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php $foo{0}(new Foo())->bar();',
                [
                    5 => '(',
                    9 => '(',
                    10 => ')',
                    11 => ')',
                    14 => '(',
                    15 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php $foo(new Foo())->bar();',
                [
                    2 => '(',
                    6 => '(',
                    7 => ')',
                    8 => ')',
                    11 => '(',
                    12 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php $$foo(new Foo())->bar();',
                [
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                    12 => '(',
                    13 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php if ($foo){}(new Foo)->foo();',
                [
                    8 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    12 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
                [
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php echo (((new \stdClass()))->a);',
                [
                    5 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    12 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
                [
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php $foo = array(new Foo());',
                [
                    6 => '(',
                    10 => '(',
                    11 => ')',
                    12 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php if (new Foo()) { } elseif (new Bar()) { } else if (new Baz()) { }',
                [
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                    17 => '(',
                    21 => '(',
                    22 => ')',
                    23 => ')',
                    33 => '(',
                    37 => '(',
                    38 => ')',
                    39 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php switch (new Foo()) { }',
                [
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php for (new Foo();;) { }',
                [
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    11 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php foreach (new Foo() as $foo) { }',
                [
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    13 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php while (new Foo()) { }',
                [
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php do { } while (new Foo());',
                [
                    9 => '(',
                    13 => '(',
                    14 => ')',
                    15 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php $static = new static(new \SplFileInfo(__FILE__));',
                [
                    8 => '(',
                    13 => '(',
                    15 => ')',
                    16 => ')',
                ],
                [
                    '(',
                    ')',
                    '(',
                    ')',
                ],
            ],
            [
                '<?php $foo = new class(new \stdClass()) {};',
                [
                    8 => '(',
                    13 => '(',
                    14 => ')',
                    15 => ')',
                ],
                [
                    '(',
                    ')',
                ],
            ],
            [
                '<?php $foo = (new class(new \stdClass()) {});',
                [
                    5 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    20 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
                [
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
            [
                '<?php $fn = fn() => null;',
                [
                    6 => '(',
                    7 => ')',
                ],
                [
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ],
            ],
        ];
    }

    /**
     * @param array<int, int|string> $expectedTokens
     * @param list<int>              $observedKinds
     *
     * @dataProvider provideProcessPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testProcessPhp80(array $expectedTokens, array $observedKinds, string $source): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            $observedKinds
        );
    }

    public static function provideProcessPhp80Cases(): iterable
    {
        yield [
            [
                5 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                8 => '(',
                10 => '(',
                11 => ')',
                12 => ')',
                13 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
            ],
            [
                '(',
                ')',
                CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
            ],
            '<?php $a = (new (foo()));',
        ];

        yield [
            [
                5 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                15 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
            ],
            [
                CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
            ],
            '<?php $a = (new #[Foo]
                class{}) ?>',
        ];
    }

    /**
     * @param array<int, int|string> $expectedTokens
     * @param list<int>              $observedKinds
     *
     * @dataProvider provideProcessPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testProcessPhp81(array $expectedTokens, array $observedKinds, string $source): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            $observedKinds
        );
    }

    public static function provideProcessPhp81Cases(): iterable
    {
        yield [
            [
                20 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                24 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                43 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                47 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                54 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                64 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                107 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                111 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
            ],
            [
                CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
            ],
            '<?php
class Test {
    public function __construct(
        public $prop = (new Foo),
    ) {}
}

function test(
    $foo = (new A),
    $baz = (new C(x: 2)),
) {
}

static $x = new (Foo);

const C = new (Foo);

function test2($param = (new Foo)) {}
',
        ];
    }
}
