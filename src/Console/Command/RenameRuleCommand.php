<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\CS\FixerFactory;
use Symfony\CS\ToolInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class RenameRuleCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rename-rule')
            ->setDefinition(
                array(
                    new InputArgument('oldName', InputArgument::REQUIRED, 'Old name', null),
                    new InputArgument('newName', InputArgument::REQUIRED, 'New name', null),
                )
            )
            ->setDescription('Rename rule.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oldName = $input->getArgument('oldName');
        $newName = $input->getArgument('newName');

        $fixerFactory = FixerFactory::create()
            ->registerBuiltInFixers()
        ;

        if (!$fixerFactory->hasRule($oldName)) {
            throw new \RuntimeException(sprintf('No rule "%s" found.', $oldName));
        }

        if ($fixerFactory->hasRule($newName)) {
            throw new \RuntimeException(sprintf('Rule "%s" already exists.', $newName));
        }

        $fixerOldName = $this->ruleNameToFixerName($oldName);
        $fixerNewName = $this->ruleNameToFixerName($newName);

        $fs = new Filesystem();
        $finder = Finder::create()
            ->files()
            ->in(__DIR__.'../../../')
            ->in(__DIR__.'../../../../tests')
        ;

        foreach ($finder as $file) {
            $realPath = $file->getRealPath();

            $content = str_replace(
                array($oldName, $fixerOldName),
                array($newName, $fixerNewName),
                $file->getContents(),
                $count
            );

            if ($count) {
                file_put_contents($realPath, $content);
            }

            $tmpPath = str_replace(
                array($oldName, $fixerOldName),
                array($newName, $fixerNewName),
                $realPath,
                $count
            );

            if ($count) {
                $fs->rename($realPath, $tmpPath);
            }
        }
    }

    private function ruleNameToFixerName($ruleName)
    {
        return str_replace('_', '', ucwords($ruleName, '_')).'Fixer';
    }
}
