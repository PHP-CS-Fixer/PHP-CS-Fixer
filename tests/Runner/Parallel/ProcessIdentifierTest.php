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

namespace PhpCsFixer\Tests\Runner\Parallel;

use PhpCsFixer\Runner\Parallel\ParallelisationException;
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\ProcessIdentifier
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ProcessIdentifierTest extends TestCase
{
    public function testCreateIdentifier(): void
    {
        $identifier = ProcessIdentifier::create();

        self::assertStringStartsWith('php-cs-fixer_parallel_', $identifier->toString());
    }

    /**
     * @dataProvider provideFromRawCases
     */
    public function testFromRaw(string $rawIdentifier, bool $valid): void
    {
        if (!$valid) {
            self::expectException(ParallelisationException::class);
        }

        $identifier = ProcessIdentifier::fromRaw($rawIdentifier);
        self::assertSame($rawIdentifier, $identifier->toString());
    }

    /**
     * @return iterable<int, array{0: string, 1: bool}>
     */
    public static function provideFromRawCases(): iterable
    {
        yield ['php-cs-fixer_parallel_foo', true];

        yield ['invalid', false];
    }
}
