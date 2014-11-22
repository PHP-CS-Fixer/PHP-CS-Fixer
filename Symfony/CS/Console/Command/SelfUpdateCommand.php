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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\CS\ToolInfo;

/**
 * @author Igor Wiedler <igor@wiedler.ch>
 * @author Stephane PY <py.stephane1@gmail.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class SelfUpdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(array('selfupdate'))
            ->setDescription('Update php-cs-fixer.phar to the latest version.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command replace your php-cs-fixer.phar by the
latest version from cs.sensiolabs.org.

<info>php php-cs-fixer.phar %command.name%</info>

EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!ToolInfo::isInstalledAsPhar()) {
            $output->writeln('<error>Self-update is available only for PHAR version.</error>');

            return 1;
        }

        if (false !== $remoteVersion = @file_get_contents('http://get.sensiolabs.org/php-cs-fixer.version')) {
            if ($this->getApplication()->getVersion() === $remoteVersion) {
                $output->writeln('<info>php-cs-fixer is already up to date.</info>');

                return;
            }
        }

        $remoteFilename = 'http://get.sensiolabs.org/php-cs-fixer.phar';
        $localFilename = $_SERVER['argv'][0];
        $tempFilename = basename($localFilename, '.phar').'-tmp.phar';

        if (false === @file_get_contents($remoteFilename)) {
            $output->writeln('<error>Unable to download new versions from the server.</error>');

            return 1;
        }

        try {
            copy($remoteFilename, $tempFilename);
            chmod($tempFilename, 0777 & ~umask());

            // test the phar validity
            $phar = new \Phar($tempFilename);
            // free the variable to unlock the file
            unset($phar);
            rename($tempFilename, $localFilename);

            $output->writeln('<info>php-cs-fixer updated.</info>');
        } catch (\Exception $e) {
            if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
                throw $e;
            }

            unlink($tempFilename);
            $output->writeln(sprintf('<error>The download is corrupt (%s).</error>', $e->getMessage()));
            $output->writeln('<error>Please re-run the self-update command to try again.</error>');

            return 1;
        }
    }
}
