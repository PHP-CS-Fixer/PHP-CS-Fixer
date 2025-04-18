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

namespace PhpCsFixer\Console\Command;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface;
use PhpCsFixer\PharCheckerInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\ToolInfoInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Igor Wiedler <igor@wiedler.ch>
 * @author Stephane PY <py.stephane1@gmail.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
#[AsCommand(name: 'self-update')]
final class SelfUpdateCommand extends Command
{
    protected static $defaultName = 'self-update';

    private NewVersionCheckerInterface $versionChecker;

    private ToolInfoInterface $toolInfo;

    private PharCheckerInterface $pharChecker;

    public function __construct(
        NewVersionCheckerInterface $versionChecker,
        ToolInfoInterface $toolInfo,
        PharCheckerInterface $pharChecker
    ) {
        parent::__construct();

        $this->versionChecker = $versionChecker;
        $this->toolInfo = $toolInfo;
        $this->pharChecker = $pharChecker;
    }

    protected function configure(): void
    {
        $this
            ->setAliases(['selfupdate'])
            ->setDefinition(
                [
                    new InputOption('--force', '-f', InputOption::VALUE_NONE, 'Force update to next major version if available.'),
                ]
            )
            ->setDescription('Update php-cs-fixer.phar to the latest stable version.')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> command replace your php-cs-fixer.phar by the
                    latest version released on:
                    <comment>https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases</comment>

                    <info>$ php php-cs-fixer.phar %command.name%</info>

                    EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($output instanceof ConsoleOutputInterface) {
            $stdErr = $output->getErrorOutput();
            $stdErr->writeln(Application::getAboutWithRuntime(true));
        }

        if (!$this->toolInfo->isInstalledAsPhar()) {
            $output->writeln('<error>Self-update is available only for PHAR version.</error>');

            return 1;
        }

        $currentVersion = $this->getApplication()->getVersion();
        Preg::match('/^v?(?<major>\d+)\./', $currentVersion, $matches);
        $currentMajor = (int) $matches['major'];

        try {
            $latestVersion = $this->versionChecker->getLatestVersion();
            $latestVersionOfCurrentMajor = $this->versionChecker->getLatestVersionOfMajor($currentMajor);
        } catch (\Exception $exception) {
            $output->writeln(\sprintf(
                '<error>Unable to determine newest version: %s</error>',
                $exception->getMessage()
            ));

            return 1;
        }

        if (1 !== $this->versionChecker->compareVersions($latestVersion, $currentVersion)) {
            $output->writeln('<info>PHP CS Fixer is already up-to-date.</info>');

            return 0;
        }

        $remoteTag = $latestVersion;

        if (
            0 !== $this->versionChecker->compareVersions($latestVersionOfCurrentMajor, $latestVersion)
            && true !== $input->getOption('force')
        ) {
            $output->writeln(\sprintf('<info>A new major version of PHP CS Fixer is available</info> (<comment>%s</comment>)', $latestVersion));
            $output->writeln(\sprintf('<info>Before upgrading please read</info> https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/%s/UPGRADE-v%s.md', $latestVersion, $currentMajor + 1));
            $output->writeln('<info>If you are ready to upgrade run this command with</info> <comment>-f</comment>');
            $output->writeln('<info>Checking for new minor/patch version...</info>');

            if (1 !== $this->versionChecker->compareVersions($latestVersionOfCurrentMajor, $currentVersion)) {
                $output->writeln('<info>No minor update for PHP CS Fixer.</info>');

                return 0;
            }

            $remoteTag = $latestVersionOfCurrentMajor;
        }

        $localFilename = $_SERVER['argv'][0];
        $realPath = realpath($localFilename);
        if (false !== $realPath) {
            $localFilename = $realPath;
        }

        if (!is_writable($localFilename)) {
            $output->writeln(\sprintf('<error>No permission to update</error> "%s" <error>file.</error>', $localFilename));

            return 1;
        }

        $tempFilename = \dirname($localFilename).'/'.basename($localFilename, '.phar').'-tmp.phar';
        $remoteFilename = $this->toolInfo->getPharDownloadUri($remoteTag);

        if (false === @copy($remoteFilename, $tempFilename)) {
            $output->writeln(\sprintf('<error>Unable to download new version</error> %s <error>from the server.</error>', $remoteTag));

            return 1;
        }

        chmod($tempFilename, 0777 & ~umask());

        $pharInvalidityReason = $this->pharChecker->checkFileValidity($tempFilename);
        if (null !== $pharInvalidityReason) {
            unlink($tempFilename);
            $output->writeln(\sprintf('<error>The download of</error> %s <error>is corrupt (%s).</error>', $remoteTag, $pharInvalidityReason));
            $output->writeln('<error>Please re-run the "self-update" command to try again.</error>');

            return 1;
        }

        rename($tempFilename, $localFilename);

        $output->writeln(\sprintf('<info>PHP CS Fixer updated</info> (<comment>%s</comment> -> <comment>%s</comment>)', $currentVersion, $remoteTag));

        return 0;
    }
}
