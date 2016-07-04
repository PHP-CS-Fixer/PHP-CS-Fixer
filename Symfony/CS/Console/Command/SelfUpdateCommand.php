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

        $remoteTag = $this->getRemoteTag();

        if (null === $remoteTag) {
            $output->writeln('<error>Unable to determine newest version.</error>');

            return;
        }

        if ('v'.$this->getApplication()->getVersion() === $remoteTag) {
            $output->writeln('<info>php-cs-fixer is already up to date.</info>');

            return;
        }

        $remoteFilename = $this->buildVersionFileUrl($remoteTag);
        $localFilename = $_SERVER['argv'][0];
        $tempFilename = basename($localFilename, '.phar').'-tmp.phar';

        try {
            $copyResult = @copy($remoteFilename, $tempFilename);
            if (false === $copyResult) {
                $output->writeln('<error>Unable to download new versions from the server.</error>');

                return 1;
            }

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

    private function buildVersionFileUrl($tag)
    {
        return sprintf('https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/%s/php-cs-fixer.phar', $tag);
    }

    private function getRemoteTag()
    {
        $raw = file_get_contents(
            'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/releases/latest',
            null,
            stream_context_create(array(
                'http' => array(
                    'header' => 'User-Agent: FriendsOfPHP/PHP-CS-Fixer',
                ),
            ))
        );

        if (false === $raw) {
            return;
        }

        $json = json_decode($raw, true);

        if (null === $json) {
            return;
        }

        return $json['tag_name'];
    }
}
