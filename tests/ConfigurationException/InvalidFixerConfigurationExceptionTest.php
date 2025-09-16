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

namespace PhpCsFixer\Tests\ConfigurationException;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class InvalidFixerConfigurationExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';

        $exception = new InvalidFixerConfigurationException(
            $fixerName,
            $message
        );

        self::assertSame(\sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        self::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        self::assertSame($fixerName, $exception->getFixerName());
        self::assertNull($exception->getPrevious());
    }

    public function testConstructorSetsValues(): void
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';
        $previous = new \RuntimeException();

        $exception = new InvalidFixerConfigurationException(
            $fixerName,
            $message,
            $previous
        );

        self::assertSame(\sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        self::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        self::assertSame($fixerName, $exception->getFixerName());
        self::assertSame($previous, $exception->getPrevious());
    }
}
