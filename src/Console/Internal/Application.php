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

namespace PhpCsFixer\Console\Internal;

use PhpCsFixer\Console\Application as PublicApplication;
use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Console\Internal\Command\DocumentationCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\CompleteCommand;
use Symfony\Component\Console\Command\DumpCompletionCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class Application extends BaseApplication
{
    public function __construct(
        ?Filesystem $filesystem = null
    ) {
        $filesystem ??= new Filesystem();

        parent::__construct(
            \sprintf('%s - %s', PublicApplication::NAME, 'internal'),
            PublicApplication::VERSION,
        );

        $this->add(new DocumentationCommand($filesystem));
    }

    // polyfill for `add` method, as it is not available in Symfony 8.0
    public function add(Command $command): ?Command
    {
        if (method_exists($this, 'addCommand')) { // @phpstan-ignore-line
            return $this->addCommand($command);
        }

        return parent::add($command);
    }

    public function getLongVersion(): string
    {
        return str_replace(
            PublicApplication::NAME,
            $this->getName(),
            PublicApplication::getAboutWithRuntime(true)
        );
    }

    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new ListCommand(), new CompleteCommand(), new DumpCompletionCommand()];
    }
}
