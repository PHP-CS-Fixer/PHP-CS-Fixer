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
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\ConfigurationException\InvalidConfigurationException
 */
final class InvalidConfigurationExceptionTest extends TestCase
{
    public function testIsInvalidArgumentException(): void
    {
        $exception = new InvalidConfigurationException('I cannot do that, Dave.');

        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function testDefaults(): void
    {
        $message = 'I cannot do that, Dave.';

        $exception = new InvalidConfigurationException($message);

        self::assertSame($message, $exception->getMessage());
        self::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_CONFIG, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }

    public function testConstructorSetsValues(): void
    {
        $message = 'I cannot do that, Dave.';
        $code = 9000;
        $previous = new \RuntimeException();

        $exception = new InvalidConfigurationException(
            $message,
            $code,
            $previous
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($code, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
