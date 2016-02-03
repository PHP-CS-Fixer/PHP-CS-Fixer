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

namespace PhpCsFixer\ConfigurationException;

use PhpCsFixer\Console\Command\FixCommand;

/**
 * Exception thrown by Fixers on misconfiguration.
 *
 * @author SpacePossum
 *
 * @internal
 */
class InvalidFixerConfigurationException extends InvalidConfigurationException
{
    /**
     * @param string $fixerName
     * @param string $message
     */
    public function __construct($fixerName, $message)
    {
        parent::__construct(sprintf('[%s] %s', $fixerName, $message), FixCommand::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG);
    }
}
