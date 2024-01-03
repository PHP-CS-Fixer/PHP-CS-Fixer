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
 */
final class HelpCommandTest extends TestCase
{
    /**
     * @param null|mixed $expected
     *
     * @dataProvider provideGetDisplayableAllowedValuesCases
     */
    public function testGetDisplayableAllowedValues($expected, FixerOptionInterface $input): void
    {
        self::assertSame($expected, HelpCommand::getDisplayableAllowedValues($input));
    }

    public static function provideGetDisplayableAllowedValuesCases(): iterable
    {
        yield [null, new FixerOption('foo', 'bar', false, null, ['int'], [])];

        yield [['A', 'B', 'x', 'z'], new FixerOption('foo', 'bar', false, null, ['string'], ['z', 'x', 'B', 'A'])];

        yield [[0, 3, 9], new FixerOption('foo', 'bar', false, null, ['int'], [0, 3, 9, static fn () => true])];

        yield [null, new FixerOption('foo', 'bar')];
    }
}
