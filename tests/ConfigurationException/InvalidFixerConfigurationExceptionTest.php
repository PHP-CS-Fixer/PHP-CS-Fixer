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

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
 */
final class InvalidFixerConfigurationExceptionTest extends TestCase
{
    public function testIsInvalidArgumentException(): void
    {
        $exception = new InvalidFixerConfigurationException('foo', 'I cannot do that, Dave.');

        static::assertInstanceOf(InvalidConfigurationException::class, $exception);
    }

    public function testDefaults(): void
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';

        $exception = new InvalidFixerConfigurationException(
            $fixerName,
            $message
        );

        static::assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        static::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        static::assertSame($fixerName, $exception->getFixerName());
        static::assertNull($exception->getPrevious());
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

        static::assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        static::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        static::assertSame($fixerName, $exception->getFixerName());
        static::assertSame($previous, $exception->getPrevious());
    }
}
