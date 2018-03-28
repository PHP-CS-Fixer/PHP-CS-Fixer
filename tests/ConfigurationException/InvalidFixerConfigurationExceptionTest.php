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
    public function testIsInvalidArgumentException()
    {
        $exception = new InvalidFixerConfigurationException('foo', 'I cannot do that, Dave.');

        $this->assertInstanceOf(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class, $exception);
    }

    public function testDefaults()
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';

        $exception = new InvalidFixerConfigurationException(
            $fixerName,
            $message
        );

        $this->assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        $this->assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        $this->assertSame($fixerName, $exception->getFixerName());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorSetsValues()
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Dave.';
        $previous = new \RuntimeException();

        $exception = new InvalidFixerConfigurationException(
            $fixerName,
            $message,
            $previous
        );

        $this->assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        $this->assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        $this->assertSame($fixerName, $exception->getFixerName());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
