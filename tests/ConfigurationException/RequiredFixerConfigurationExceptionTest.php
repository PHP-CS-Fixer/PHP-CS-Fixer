<?php

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
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException
 */
final class RequiredFixerConfigurationExceptionTest extends TestCase
{
    public function testIsInvalidFixerConfigurationException()
    {
        $exception = new RequiredFixerConfigurationException(
            'hal',
            'I cannot do that, Dave.'
        );

        static::assertInstanceOf(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class, $exception);
    }

    public function testDefaults()
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';

        $exception = new RequiredFixerConfigurationException(
            $fixerName,
            $message
        );

        static::assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        static::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        static::assertNull($exception->getPrevious());
    }

    public function testConstructorSetsValues()
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';
        $previous = new \RuntimeException();

        $exception = new RequiredFixerConfigurationException(
            $fixerName,
            $message,
            $previous
        );

        static::assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        static::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        static::assertSame($previous, $exception->getPrevious());
    }
}
