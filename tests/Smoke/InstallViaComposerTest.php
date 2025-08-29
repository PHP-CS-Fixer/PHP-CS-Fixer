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
use PhpCsFixer\Preg;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 *
 * @large
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class InstallViaComposerTest extends AbstractSmokeTestCase
{
    private ?Filesystem $fs;

    /** @var array<string, mixed> */
    private array $currentCodeAsComposerDependency = [
        'repositories' => [
            [
                'type' => 'path',
                'url' => __DIR__.'/../..',
                'options' => [
                    'symlink' => false,
                ],
            ],
        ],
        'require' => [
            'friendsofphp/php-cs-fixer' => '*@dev',
        ],
    ];

    /**
     * @var list<string>
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
            self::markTestIncomplete('This test is broken on Windows');
        }

        try {
            CommandExecutor::create('php --version', __DIR__)->getResult();
        } catch (\RuntimeException $e) {
            self::fail('Missing `php` env script. Details:'."\n".$e->getMessage());
        }

        try {
            CommandExecutor::create('composer --version', __DIR__)->getResult();
        } catch (\RuntimeException $e) {
            self::fail('Missing `composer` env script. Details:'."\n".$e->getMessage());
        }

        try {
            CommandExecutor::create('composer check', __DIR__.'/../..')->getResult();
        } catch (\RuntimeException $e) {
            self::fail('Composer check failed. Details:'."\n".$e->getMessage());
        }
    }

    protected function setUp(): void
    {
        $this->fs = new Filesystem();
    }

    protected function tearDown(): void
    {
        $this->fs = null;

        parent::tearDown();
    }

    public function testInstallationViaPathIsPossible(): void
    {
        $tmpPath = $this->createFakeComposerProject($this->currentCodeAsComposerDependency);

        self::assertCommandsWork($this->stepsToVerifyInstallation, $tmpPath);

        $this->fs->remove($tmpPath);
    }

    // test that respects `export-ignore` from `.gitattributes` file
    public function testInstallationViaArtifactIsPossible(): void
    {
        // Composer Artifact Repository requires `zip` extension
        if (!\extension_loaded('zip')) {
            // We do not want to mark test as skipped, because we explicitly want to test this and `zip` is required
            self::fail('No zip extension available.');
        }

        $tmpArtifactPath = tempnam(sys_get_temp_dir(), 'cs_fixer_tmp_');
        unlink($tmpArtifactPath);
        $this->fs->mkdir($tmpArtifactPath);

        $fakeVersion = Preg::replace('/\-.+/', '', Application::VERSION, 1).'-alpha987654321';

        $tmpPath = $this->createFakeComposerProject([
            'repositories' => [
                [
                    'type' => 'artifact',
                    'url' => $tmpArtifactPath,
                ],
            ],
            'require' => [
                'friendsofphp/php-cs-fixer' => $fakeVersion,
            ],
        ]);

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

        self::assertCommandsWork($stepsToInitializeArtifact, $cwd);
        self::assertCommandsWork($stepsToPrepareArtifact, $tmpArtifactPath);
        self::assertCommandsWork($this->stepsToVerifyInstallation, $tmpPath);

        $this->fs->remove($tmpPath);
        $this->fs->remove($tmpArtifactPath);
    }

    /**
     * @param list<string> $commands
     */
    private static function assertCommandsWork(array $commands, string $cwd): void
    {
        foreach ($commands as $command) {
            self::assertSame(0, CommandExecutor::create($command, $cwd)->getResult()->getCode());
        }
    }

    /**
     * @param array<string, mixed> $initialComposerFileState
     *
     * @return string Path to temporary directory containing Composer project
     */
    private function createFakeComposerProject(array $initialComposerFileState): string
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'cs_fixer_tmp_');

        if (false === $tmpPath) {
            throw new \RuntimeException('Creating directory for fake Composer project has failed.');
        }

        unlink($tmpPath);
        $this->fs->mkdir($tmpPath);

        try {
            file_put_contents(
                $tmpPath.'/composer.json',
                json_encode($initialComposerFileState, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT)
            );
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException(
                'Initial Composer file state could not be saved as composer.json',
                $e->getCode(),
                $e
            );
        }

        return $tmpPath;
    }
}
