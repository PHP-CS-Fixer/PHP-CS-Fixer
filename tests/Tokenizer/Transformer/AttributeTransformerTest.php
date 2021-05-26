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

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\AttributeTransformer
 */
final class AttributeTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     * @requires PHP 8.0
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens);
    }

    public function provideProcessCases()
    {
        yield ['<?php class Foo {
    #[Listens(ProductCreatedEvent::class)]
    public $foo;
}
',
            [
                14 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield ['<?php class Foo {
    #[Required]
    public $bar;
}',
            [
                9 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield [
            '<?php function foo(
    #[MyAttr([1, 2])] Type $myParam,
) {}',
            [
                16 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];
    }

    /**
     * @dataProvider provideNotChangeCases
     */
    public function testNotChange(string $source): void
    {
        Tokens::clearCache();

        foreach (Tokens::fromCode($source) as $token) {
            static::assertFalse($token->isGivenKind([
                CT::T_ATTRIBUTE_CLOSE,
            ]));
        }
    }

    public function provideNotChangeCases()
    {
        yield [
            '<?php
                $foo = [];
                $a[] = $b[1];
                $c = $d[2];
                // [$e] = $f;',
        ];

        if (\PHP_VERSION_ID >= 70100) {
            yield [
                '<?php [$e] = $f;',
            ];
        }
    }
}
