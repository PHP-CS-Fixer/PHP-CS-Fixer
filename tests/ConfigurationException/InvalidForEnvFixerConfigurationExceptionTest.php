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

use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException
 */
final class InvalidForEnvFixerConfigurationExceptionTest extends TestCase
{
    public function testDefaults()
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Andreas!';

        $exception = new InvalidForEnvFixerConfigurationException(
            $fixerName,
            $message
        );

        static::assertInstanceOf(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class, $exception);
        static::assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        static::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        static::assertSame($fixerName, $exception->getFixerName());
        static::assertNull($exception->getPrevious());
    }
}
