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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\UseTransformer
 *
 * @phpstan-import-type _TransformerTestExpectedTokens from AbstractTransformerTestCase
 * @phpstan-import-type _TransformerTestObservedKindsOrPrototypes from AbstractTransformerTestCase
 */
final class UseTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedTokens $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                \T_USE,
                CT::T_USE_LAMBDA,
                CT::T_USE_TRAIT,
            ]
        );
    }

    /**
     * @return iterable<array{string, _TransformerTestExpectedTokens}>
     */
    public static function provideProcessCases(): iterable
    {
        yield [
            '<?php use Foo;',
            [
                1 => \T_USE,
            ],
        ];

        yield [
            '<?php $foo = function() use ($bar) {};',
            [
                9 => CT::T_USE_LAMBDA,
            ],
        ];

        yield [
            '<?php class Foo { use Bar; }',
            [
                7 => CT::T_USE_TRAIT,
            ],
        ];

        yield [
            '<?php namespace Aaa; use Bbb; class Foo { use Bar; function baz() { $a=1; return function () use ($a) {}; } }',
            [
                6 => \T_USE,
                17 => CT::T_USE_TRAIT,
                42 => CT::T_USE_LAMBDA,
            ],
        ];

        yield [
            '<?php
                namespace A {
                    class Foo {}
                    echo Foo::class;
                }

                namespace B {
                    use \stdClass;

                    echo 123;
                }',
            [
                30 => \T_USE,
            ],
        ];

        yield [
            '<?php use Foo; $a = Bar::class;',
            [
                1 => \T_USE,
            ],
        ];

        yield 'nested anonymous classes' => [
            '<?php

namespace SomeWhereOverTheRainbow;

trait Foo {
public function test()
{
    $a = time();
    return function() use ($a) { echo $a; };
}
};

$a = new class(
new class() {
    use Foo;
}
) {
public function __construct($bar)
{
    $a = $bar->test();
    $a();
}
};
',
            [
                38 => CT::T_USE_LAMBDA,
                76 => CT::T_USE_TRAIT,
            ],
        ];

        yield [
            '<?php
use A\{B,};
use function D;
use C\{D,E,};
',
            [
                1 => \T_USE,
                11 => \T_USE,
                18 => \T_USE,
            ],
        ];
    }

    /**
     * @param _TransformerTestExpectedTokens $expectedTokens
     *
     * @requires PHP 8.1
     *
     * @dataProvider provideProcessPhp81Cases
     */
    public function testProcessPhp81(string $source, array $expectedTokens = []): void
    {
        $this->doTest($source, $expectedTokens, [CT::T_USE_TRAIT]);
    }

    /**
     * @return iterable<int, array{string, _TransformerTestExpectedTokens}>
     */
    public static function provideProcessPhp81Cases(): iterable
    {
        yield [
            '<?php enum Foo: string
{
    use Bar;

    case Test1 = "a";
}
',
            [
                10 => CT::T_USE_TRAIT,
            ],
        ];
    }
}
