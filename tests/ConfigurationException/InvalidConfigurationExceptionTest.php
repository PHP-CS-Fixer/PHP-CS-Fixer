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
    public function testIsInvalidArgumentException()
    {
        $exception = new InvalidConfigurationException('I cannot do that, Dave.');

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function testDefaults()
    {
        $message = 'I cannot do that, Dave.';

        $exception = new InvalidConfigurationException($message);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_CONFIG, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorSetsValues()
    {
        $message = 'I cannot do that, Dave.';
        $code = 9000;
        $previous = new \RuntimeException();

        $exception = new InvalidConfigurationException(
            $message,
            $code,
            $previous
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
