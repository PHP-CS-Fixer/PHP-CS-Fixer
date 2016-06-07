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

namespace Symfony\CS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\CS\ToolInfo;

/**
 * @author Igor Wiedler <igor@wiedler.ch>
 * @author Stephane PY <py.stephane1@gmail.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
            ->setDescription('Update php-cs-fixer.phar to the latest stable version.')
            ->setHelp(<<<'EOT'
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

        $currentVersion = explode('-', $this->getApplication()->getVersion());
        $currentVersion = $currentVersion[0]; // ignore index #1 if exists (drop non-stable versions like `-DEV`)
        $currentVersion = explode('.', $currentVersion);
        if (!isset($currentVersion[2])) {
            $currentVersion[2] = 0; // fill patch version if missing
        }

        list($major, $minor, $patch) = $this->findBestVersion($currentVersion[0], $currentVersion[1], $currentVersion[2]);

        if ($this->getApplication()->getVersion() === $this->buildVersionString($major, $minor, $patch)) {
            $output->writeln('<info>php-cs-fixer is already up to date.</info>');

            return;
        }

        $remoteFilename = $this->buildVersionFileUrl($major, $minor, $patch);
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

    private function checkIfVersionFileExists($major, $minor, $patch)
    {
        $url = $this->buildVersionFileUrl($major, $minor, $patch);
        $headers = get_headers($url);

        return stripos($headers[0], '200 OK') ? true : false;
    }

    private function findBestVersion($major, $minor, $patch)
    {
        if ($this->checkIfVersionFileExists($major, $minor, $patch + 1)) {
            return $this->findBestVersion($major, $minor, $patch + 1);
        }

        if ($this->checkIfVersionFileExists($major, $minor + 1, 0)) {
            return $this->findBestVersion($major, $minor + 1, 0);
        }

        if ($this->checkIfVersionFileExists($major + 1, 0, 0)) {
            return $this->findBestVersion($major + 1, 0, 0);
        }

        return array($major, $minor, $patch);
    }

    private function buildVersionFileUrl($major, $minor, $patch)
    {
        return sprintf('http://get.sensiolabs.org/php-cs-fixer-v%s.phar', $this->buildVersionString($major, $minor, $patch));
    }

    private function buildVersionString($major, $minor, $patch)
    {
        return sprintf('%d.%d.%d', $major, $minor, $patch);
    }
}
