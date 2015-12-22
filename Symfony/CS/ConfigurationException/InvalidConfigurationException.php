<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\ConfigurationException;

use Symfony\CS\Console\Command\FixCommand;

/**
 * Exceptions of this type are thrown on misconfiguration of the Fixer.
 *
 * @author SpacePossum
 *
 * @internal
 */
class InvalidConfigurationException extends \InvalidArgumentException
{
    /**
     * @param string   $message
     * @param int|null $code
     */
    public function __construct($message, $code = null)
    {
        parent::__construct($message, null === $code ? FixCommand::EXIT_STATUS_FLAG_HAS_INVALID_CONFIG : $code);
    }
}
