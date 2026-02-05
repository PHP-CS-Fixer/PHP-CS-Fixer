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

use PhpCsFixer\ConfigurationException\UnresolvableAutoRuleSetConfigurationException;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\ConfigurationException\UnresolvableAutoRuleSetConfigurationException
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class UnresolvableAutoRuleSetConfigurationExceptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $message = 'I cannot do that, Dave.';

        $exception = new UnresolvableAutoRuleSetConfigurationException($message);

        self::assertSame($message, $exception->getMessage());
        self::assertSame(FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_CONFIG, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }

    public function testConstructorSetsValues(): void
    {
        $message = 'I cannot do that, Dave.';
        $code = 9_000;
        $previous = new \RuntimeException();

        $exception = new UnresolvableAutoRuleSetConfigurationException(
            $message,
            $code,
            $previous,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($code, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
