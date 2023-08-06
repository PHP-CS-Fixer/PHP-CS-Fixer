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
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\EnumCaseTransformer
 *
 * @requires PHP 8.1
 */
final class EnumCaseTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens, [CT::T_ENUM_CASE]);
    }

    public static function provideProcessCases(): iterable
    {
        yield 'pure enum' => [
            '<?php
enum Foo
{
    case One;
    case Two;
}
',
            [
                7 => CT::T_ENUM_CASE,
                12 => CT::T_ENUM_CASE,
            ],
        ];

        yield 'pure enum with switch' => [
            '<?php
enum Foo
{
    case One;
    case Two;

    public static function getLowerName(self $instance): string
    {
        switch ($instance->name) {
            case \'One\':
            case \'Two\':
                return strtolower($instance->name);
        }
    }
}
',
            [
                7 => CT::T_ENUM_CASE,
                12 => CT::T_ENUM_CASE,
            ],
        ];

        yield 'backed enum' => [
            '<?php
enum Suit: string
{
    case Hearts = \'hearts\';
    case Spades = \'spades\';
    case Clubs = \'clubs\';
    case Diamonds = \'diamonds\';
}
',
            [
                10 => CT::T_ENUM_CASE,
                19 => CT::T_ENUM_CASE,
                28 => CT::T_ENUM_CASE,
                37 => CT::T_ENUM_CASE,
            ],
        ];

        yield 'backed enum with switch' => [
            '<?php
enum Suit: string
{
    case Hearts = \'hearts\';
    case Spades = \'spades\';
    case Clubs = \'clubs\';
    case Diamonds = \'diamonds\';

    public static function getUppercasedValue(self $instance): string
    {
        switch ($instance->value) {
            case \'hearts\':
            case \'spades\':
                return strtoupper($instance->value);

            default:
                return $instance->value;
        }
    }
}
',
            [
                10 => CT::T_ENUM_CASE,
                19 => CT::T_ENUM_CASE,
                28 => CT::T_ENUM_CASE,
                37 => CT::T_ENUM_CASE,
            ],
        ];
    }
}
