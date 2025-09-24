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

use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException
 *
 * @author Andreas Möller <am@localheinz.com>
 */
final class RequiredFixerConfigurationExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';

        $exception = new RequiredFixerConfigurationException(
            $fixerName,
            $message
        );

        self::assertSame(\sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        self::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }

    public function testConstructorSetsValues(): void
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';
        $previous = new \RuntimeException();

        $exception = new RequiredFixerConfigurationException(
            $fixerName,
            $message,
            $previous
        );

        self::assertSame(\sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        self::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
