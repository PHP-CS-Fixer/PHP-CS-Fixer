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
use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException
 */
final class InvalidForEnvFixerConfigurationExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $fixerName = 'hal';
        $message = 'I cannot do that, Andreas!';

        $exception = new InvalidForEnvFixerConfigurationException(
            $fixerName,
            $message
        );

        self::assertInstanceOf(InvalidFixerConfigurationException::class, $exception);
        self::assertSame(sprintf('[%s] %s', $fixerName, $message), $exception->getMessage());
        self::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG, $exception->getCode());
        self::assertSame($fixerName, $exception->getFixerName());
        self::assertNull($exception->getPrevious());
    }
}
