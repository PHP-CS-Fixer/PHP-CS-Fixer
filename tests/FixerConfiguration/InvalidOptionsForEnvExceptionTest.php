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

namespace PhpCsFixer\Tests\FixerConfiguration;

use PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException
 */
final class InvalidOptionsForEnvExceptionTest extends TestCase
{
    public function testInvalidOptionsForEnvException(): void
    {
        $exception = new InvalidOptionsForEnvException();
        self::assertInstanceOf(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class, $exception);
    }
}
