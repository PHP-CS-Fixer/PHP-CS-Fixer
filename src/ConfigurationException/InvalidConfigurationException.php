<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *     and contributors <https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/graphs/contributors>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\ConfigurationException;

use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;

/**
 * Exceptions of this type are thrown on misconfiguration of the Fixer.
 *
 * @internal
 *
 * @final Only internal extending this class is supported
 */
class InvalidConfigurationException extends \InvalidArgumentException
{
    public function __construct(string $message, ?int $code = null, ?\Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $code ?? FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_CONFIG,
            $previous
        );
    }
}
