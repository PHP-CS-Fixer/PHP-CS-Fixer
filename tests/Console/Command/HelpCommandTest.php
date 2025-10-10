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

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\HelpCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class HelpCommandTest extends TestCase
{
    /**
     * @param non-empty-list<string> $allowedValues
     *
     * @dataProvider provideGetDescriptionWithAllowedValuesCases
     */
    public function testGetDescriptionWithAllowedValues(string $expected, string $description, array $allowedValues): void
    {
        self::assertSame($expected, HelpCommand::getDescriptionWithAllowedValues($description, $allowedValues));
    }

    /**
     * @return iterable<int, array{string, string, non-empty-list<string>}>
     */
    public static function provideGetDescriptionWithAllowedValuesCases(): iterable
    {
        yield [
            'Option description (can be `yes` or `no`).',
            'Option description (%s).',
            ['yes', 'no'],
        ];

        yield [
            'Option description (can be `txt`, `json` or `markdown`).',
            'Option description (%s).',
            ['txt', 'json', 'markdown'],
        ];
    }

    /**
     * @param null|mixed $expected
     *
     * @dataProvider provideGetDisplayableAllowedValuesCases
     */
    public function testGetDisplayableAllowedValues($expected, FixerOptionInterface $input): void
    {
        self::assertSame($expected, HelpCommand::getDisplayableAllowedValues($input));
    }

    /**
     * @return iterable<int, array{null|mixed, FixerOption}>
     */
    public static function provideGetDisplayableAllowedValuesCases(): iterable
    {
        yield [null, new FixerOption('foo', 'bar', false, null, ['int'])];

        yield [['A', 'B', 'x', 'z'], new FixerOption('foo', 'bar', false, null, ['string'], ['z', 'x', 'B', 'A'])];

        yield [[0, 3, 9], new FixerOption('foo', 'bar', false, null, ['int'], [0, 3, 9, static fn () => true])];

        yield [null, new FixerOption('foo', 'bar')];
    }
}
