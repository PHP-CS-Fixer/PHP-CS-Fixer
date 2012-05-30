<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\CS\Console\Command\FixCommand;
use Symfony\CS\Console\Command\CompileCommand;
use Symfony\CS\Console\Command\ReadmeCommand;
use Symfony\CS\Console\Command\SelfUpdateCommand;
use Symfony\CS\Fixer;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('PHP CS Fixer', Fixer::VERSION);

        $this->add(new FixCommand());
        $this->add(new CompileCommand());
        $this->add(new ReadmeCommand());
        $this->add(new SelfUpdateCommand());
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Fabien Potencier</comment>';
    }
}
