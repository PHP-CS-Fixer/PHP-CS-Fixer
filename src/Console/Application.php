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

namespace PhpCsFixer\Console;

use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\Command\ReadmeCommand;
use PhpCsFixer\Console\Command\SelfUpdateCommand;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class Application extends BaseApplication
{
    const VERSION = '2.0.0-RC';

    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('PHP CS Fixer', self::VERSION);

        $this->add(new FixCommand());
        $this->add(new ReadmeCommand());
        $this->add(new SelfUpdateCommand());
    }

    public function getLongVersion()
    {
        $version = parent::getLongVersion().' by <comment>Fabien Potencier</comment> and <comment>Dariusz Ruminski</comment>';
        $commit = '@git-commit@';

        if ('@'.'git-commit@' !== $commit) {
            $version .= ' ('.substr($commit, 0, 7).')';
        }

        return $version;
    }
}
