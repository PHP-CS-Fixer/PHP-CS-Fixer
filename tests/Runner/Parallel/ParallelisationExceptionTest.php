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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Runner\Parallel\ParallelisationException
 */
final class ParallelisationExceptionTest extends TestCase
{
    public function testCreateForUnknownIdentifier(): void
    {
        $identifier = ProcessIdentifier::fromRaw('php-cs-fixer_parallel_foo');
        $exception = ParallelisationException::forUnknownIdentifier($identifier);

        self::assertSame('Unknown process identifier: php-cs-fixer_parallel_foo', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
