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

namespace PhpCsFixer\Tests\Smoke;

use Keradus\CliExecutor\CommandExecutor;
use PhpCsFixer\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 *
 * @large
 */
final class InstallViaComposerTest extends AbstractSmokeTest
{
    /**
     * @var string[]
     */
    private array $stepsToVerifyInstallation = [
        // Confirm we can install.
        'composer install -q',
        // Ensure that autoloader works.
        'composer dump-autoload --optimize',
        'php vendor/autoload.php',
        // Ensure basic commands work.
        'vendor/bin/php-cs-fixer --version',
        'vendor/bin/php-cs-fixer fix --help',
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if ('\\' === \DIRECTORY_SEPARATOR) {
            static::markTestIncomplete('This test is broken on Windows');
        }

        try {
            CommandExecutor::create('php --version', __DIR__)->getResult();
        } catch (\RuntimeException $e) {
            static::markTestSkippedOrFail('Missing `php` env script. Details:'."\n".$e->getMessage());
        }

        try {
            CommandExecutor::create('composer --version', __DIR__)->getResult();
        } catch (\RuntimeException $e) {
            static::markTestSkippedOrFail('Missing `composer` env script. Details:'."\n".$e->getMessage());
        }

        try {
            CommandExecutor::create('composer check', __DIR__.'/../..')->getResult();
        } catch (\RuntimeException $e) {
            static::markTestSkippedOrFail('Composer check failed. Details:'."\n".$e->getMessage());
        }
    }

    public function testInstallationViaPathIsPossible(): void
    {
        $fs = new Filesystem();

        $tmpPath = tempnam(sys_get_temp_dir(), 'cs_fixer_tmp_');
        unlink($tmpPath);
        $fs->mkdir($tmpPath);

        $initialComposerFileState = [
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => __DIR__.'/../..',
                ],
            ],
            'require' => [
                'friendsofphp/php-cs-fixer' => '*@dev',
            ],
        ];

        file_put_contents(
            $tmpPath.'/composer.json',
            json_encode($initialComposerFileState, JSON_PRETTY_PRINT)
        );

        static::assertCommandsWork($this->stepsToVerifyInstallation, $tmpPath);

        $fs->remove($tmpPath);
    }

    // test that respects `export-ignore` from `.gitattributes` file
    public function testInstallationViaArtifactIsPossible(): void
    {
        // Composer Artifact Repository requires `zip` extension
        if (!\extension_loaded('zip')) {
            static::markTestSkippedOrFail('No zip extension available.');
        }

        $fs = new Filesystem();

        $tmpPath = tempnam(sys_get_temp_dir(), 'cs_fixer_tmp_');
        unlink($tmpPath);
        $fs->mkdir($tmpPath);

        $tmpArtifactPath = tempnam(sys_get_temp_dir(), 'cs_fixer_tmp_');
        unlink($tmpArtifactPath);
        $fs->mkdir($tmpArtifactPath);

        $fakeVersion = preg_replace('/\\-.+/', '', Application::VERSION, 1).'-alpha987654321';

        $initialComposerFileState = [
            'repositories' => [
                [
                    'type' => 'artifact',
                    'url' => $tmpArtifactPath,
                ],
            ],
            'require' => [
                'friendsofphp/php-cs-fixer' => $fakeVersion,
            ],
        ];

        file_put_contents(
            $tmpPath.'/composer.json',
            json_encode($initialComposerFileState, JSON_PRETTY_PRINT)
        );

        $cwd = __DIR__.'/../..';

        $stepsToInitializeArtifact = [
            // Clone current version of project to new location, as we are going to modify it.
            // Warning! Only already committed changes will be cloned!
            "git clone --depth=1 . {$tmpArtifactPath}",
        ];

        $stepsToPrepareArtifact = [
            // Configure git user for new repo to not use global git user.
            // We need this, as global git user may not be set!
            'git config user.name test && git config user.email test',
            // Adjust cloned project to expose version in `composer.json`.
            // Without that, it would not be possible to use it as Composer Artifact.
            "composer config version {$fakeVersion} && git add . && git commit --no-gpg-sign -m 'provide version'",
            // Create repo archive that will serve as Composer Artifact.
            'git archive HEAD --format=zip -o archive.zip',
            // Drop the repo, keep the archive
            'git rm -r . && rm -rf .git',
        ];

        static::assertCommandsWork($stepsToInitializeArtifact, $cwd);
        static::assertCommandsWork($stepsToPrepareArtifact, $tmpArtifactPath);
        static::assertCommandsWork($this->stepsToVerifyInstallation, $tmpPath);

        $fs->remove($tmpPath);
        $fs->remove($tmpArtifactPath);
    }

    /**
     * @param list<string> $commands
     */
    private static function assertCommandsWork(array $commands, string $cwd): void
    {
        foreach ($commands as $command) {
            static::assertSame(0, CommandExecutor::create($command, $cwd)->getResult()->getCode());
        }
    }
}
