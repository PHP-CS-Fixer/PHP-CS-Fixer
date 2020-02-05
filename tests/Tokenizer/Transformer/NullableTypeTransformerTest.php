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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\NullableTypeTransformer
 */
final class NullableTypeTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     * @requires PHP 7.1
     */
    public function testProcess($source, array $expectedTokens = [])
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_NULLABLE_TYPE,
            ]
        );
    }

    public function provideProcessCases()
    {
        return [
            [
                '<?php function foo(?Barable $barA, ?Barable $barB): ?Fooable {}',
                [
                    5 => CT::T_NULLABLE_TYPE,
                    11 => CT::T_NULLABLE_TYPE,
                    18 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php interface Fooable { function foo(): ?Fooable; }',
                [
                    14 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php
                    $a = 1 ? "aaa" : "bbb";
                    $b = 1 ? fnc() : [];
                    $c = 1 ?: [];
                ',
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideProcess74Cases
     * @requires PHP 7.4
     */
    public function testProcess74($source, array $expectedTokens = [])
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_NULLABLE_TYPE,
            ]
        );
    }

    public function provideProcess74Cases()
    {
        return [
            [
                '<?php class Foo { private ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { protected ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { public ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { var ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { var ? Foo\Bar $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideProcessPhp74Cases
     * @requires PHP 7.4
     */
    public function testProcessPhp74($source, array $expectedTokens = [])
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_NULLABLE_TYPE,
            ]
        );
    }

    public function provideProcessPhp74Cases()
    {
        return [
            [
                '<?php fn(?Barable $barA, ?Barable $barB): ?Fooable => null;',
                [
                    3 => CT::T_NULLABLE_TYPE,
                    9 => CT::T_NULLABLE_TYPE,
                    16 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { public ?array $foo; public static ?array $bar; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                    19 => CT::T_NULLABLE_TYPE,
                ],
            ],
        ];
    }
}
