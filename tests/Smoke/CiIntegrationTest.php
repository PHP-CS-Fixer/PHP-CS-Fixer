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

use Keradus\CliExecutor\CliResult;
use Keradus\CliExecutor\CommandExecutor;
use Keradus\CliExecutor\ScriptExecutor;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Preg;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @requires OS Linux|Darwin
 *
 * @coversNothing
 *
 * @group covers-nothing
 *
 * @large
 */
final class CiIntegrationTest extends AbstractSmokeTestCase
{
    public static string $fixtureDir;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$fixtureDir = __DIR__.'/../Fixtures/ci-integration';

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

        try {
            self::executeScript([
                'rm -rf .git',
                'git init --initial-branch=master -q',
                'git config user.name test',
                'git config user.email test',
                'git add .',
                'git commit -m "init" -q',
            ]);
        } catch (\RuntimeException $e) {
            self::fail($e->getMessage());
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::executeCommand('rm -rf .git');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        self::executeScript([
            'git reset . -q',
            'git checkout . -q',
            'git clean -fdq',
            'git checkout master -q',
        ]);
    }

    /**
     * @param list<string> $caseCommands
     * @param list<string> $expectedResult1Lines
     * @param list<string> $expectedResult2Lines
     *
     * @dataProvider provideIntegrationCases
     */
    public function testIntegration(
        string $branchName,
        array $caseCommands,
        array $expectedResult1Lines,
        array $expectedResult2Lines,
        string $expectedResult3FilesLine
    ): void {
        self::executeScript(array_merge(
            [
                "git checkout -b {$branchName} -q",
            ],
            $caseCommands
        ));

        $integrationScript = explode("\n", str_replace('vendor/bin/', './../../../', (string) file_get_contents(__DIR__.'/../../ci-integration.sh')));

        self::assertArrayHasKey(3, $integrationScript);
        self::assertArrayHasKey(4, $integrationScript);
        self::assertArrayHasKey(5, $integrationScript);
        self::assertArrayHasKey(6, $integrationScript);
        self::assertArrayHasKey(7, $integrationScript);

        $steps = [
            "COMMIT_RANGE=\"master..{$branchName}\"",
            "{$integrationScript[3]}\n{$integrationScript[4]}",
            $integrationScript[5],
            $integrationScript[6],
            $integrationScript[7],
        ];

        $result1 = self::executeScript([
            $steps[0],
            $steps[1],
            $steps[2],
            'echo "$CHANGED_FILES"',
        ]);

        self::assertSame(implode("\n", $expectedResult1Lines)."\n", $result1->getOutput());

        $result2 = self::executeScript([
            $steps[0],
            $steps[1],
            $steps[2],
            $steps[3],
            'echo "${EXTRA_ARGS}"',
        ]);

        self::assertSame(implode("\n", $expectedResult2Lines), $result2->getOutput());

        $result3 = self::executeScript([
            $steps[0],
            $steps[1],
            $steps[2],
            $steps[3],
            $steps[4],
        ]);

        $optionalDeprecatedVersionWarning = 'You are running PHP CS Fixer v3, which is not maintained anymore. Please update to v4.
You may find an UPGRADE guide at https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/v4.0.0/UPGRADE-v4.md .
';

        $optionalIncompatibilityWarning = 'PHP needs to be a minimum version of PHP 7.4.0 and maximum version of PHP 8.2.*.
Current PHP version: '.\PHP_VERSION.'.
Ignoring environment requirements because `PHP_CS_FIXER_IGNORE_ENV` is set. Execution may be unstable.
';

        $optionalXdebugWarning = 'You are running PHP CS Fixer with xdebug enabled. This has a major impact on runtime performance.
';

        $optionalWarningsHelp = 'If you need help while solving warnings, ask at https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/discussions/, we will help you!

';

        $expectedResult3FilesLineAfterDotsIndex = strpos($expectedResult3FilesLine, ' ');
        self::assertIsInt($expectedResult3FilesLineAfterDotsIndex);

        $expectedResult3FilesDots = substr($expectedResult3FilesLine, 0, $expectedResult3FilesLineAfterDotsIndex);
        $expectedResult3FilesPercentage = substr($expectedResult3FilesLine, $expectedResult3FilesLineAfterDotsIndex);

        /** @phpstan-ignore-next-line to avoid `Ternary operator condition is always true|false.` */
        $aboutSubpattern = Application::VERSION_CODENAME
            ? 'PHP CS Fixer '.preg_quote(Application::VERSION, '/').' '.preg_quote(Application::VERSION_CODENAME, '/')." by Fabien Potencier, Dariusz Ruminski and contributors.\nPHP runtime: ".\PHP_VERSION
            : 'PHP CS Fixer '.preg_quote(Application::VERSION, '/')." by Fabien Potencier, Dariusz Ruminski and contributors.\nPHP runtime: ".\PHP_VERSION;

        $availableMaxProcesses = ParallelConfigFactory::detect()->getMaxProcesses();

        $pattern = \sprintf(
            '/^(?:%s)?(?:%s)?(?:%s)?(?:%s)?%s\n%s\n%s\n%s\n([\.S]{%d})%s\n%s$/',
            preg_quote($optionalDeprecatedVersionWarning, '/'),
            preg_quote($optionalIncompatibilityWarning, '/'),
            preg_quote($optionalXdebugWarning, '/'),
            preg_quote($optionalWarningsHelp, '/'),
            $aboutSubpattern,
            'Running analysis on \d+ core(?: sequentially|s with \d+ files? per process)+\.',
            $availableMaxProcesses > 1 ? preg_quote('You can enable parallel runner and speed up the analysis! Please see https://cs.symfony.com/doc/usage.html for more information.', '/') : '',
            preg_quote('Loaded config default from ".php-cs-fixer.dist.php".', '/'),
            \strlen($expectedResult3FilesDots),
            preg_quote($expectedResult3FilesPercentage, '/'),
            preg_quote('Legend: .-no changes, F-fixed, S-skipped (cached or empty file), I-invalid file syntax (file ignored), E-error', '/')
        );

        self::assertMatchesRegularExpression($pattern, $result3->getError());

        Preg::match($pattern, $result3->getError(), $matches);

        self::assertArrayHasKey(1, $matches);
        self::assertSame(substr_count($expectedResult3FilesDots, '.'), substr_count($matches[1], '.'));
        self::assertSame(substr_count($expectedResult3FilesDots, 'S'), substr_count($matches[1], 'S'));

        self::assertMatchesRegularExpression(
            '/^\s*Found \d+ of \d+ files that can be fixed in \d+\.\d+ seconds, \d+\.\d+ MB memory used\s*$/',
            $result3->getOutput()
        );
    }

    /**
     * @return iterable<string, array{string, list<string>, list<string>, list<string>, string}>
     */
    public static function provideIntegrationCases(): iterable
    {
        yield 'random-changes' => [
            'random-changes',
            [
                'touch dir\ a/file.php',
                'rm -r dir\ c',
                'echo "" >> dir\ b/file\ b.php',
                'echo "echo 1;" >> dir\ b/file\ b.php',
                'git add .',
                'git commit -m "Random changes" -q',
            ],
            [
                'dir a/file.php',
                'dir b/file b.php',
            ],
            [
                '--path-mode=intersection',
                '--',
                'dir a/file.php',
                'dir b/file b.php',
                '',
            ],
            'S.                                                                  2 / 2 (100%)',
        ];

        yield 'changes-including-dist-config-file' => [
            'changes-including-dist-config-file',
            [
                'echo "" >> dir\ b/file\ b.php',
                'echo "echo 1;" >> dir\ b/file\ b.php',
                // `sed -i ...` is not handled the same on Linux and macOS
                'sed -e \'s/@Symfony/@PSR2/\' .php-cs-fixer.dist.php > .php-cs-fixer.dist.php.new',
                'mv .php-cs-fixer.dist.php.new .php-cs-fixer.dist.php',
                'git add .',
                'git commit -m "Random changes including config file" -q',
            ],
            [
                '.php-cs-fixer.dist.php',
                'dir b/file b.php',
            ],
            [
                '',
                '',
            ],
            '...                                                                 3 / 3 (100%)',
        ];

        yield 'changes-including-custom-config-file-creation' => [
            'changes-including-custom-config-file-creation',
            [
                'echo "" >> dir\ b/file\ b.php',
                'echo "echo 1;" >> dir\ b/file\ b.php',
                'sed -e \'s/@Symfony/@PSR2/\' .php-cs-fixer.dist.php > .php-cs-fixer.php',
                'git add .',
                'git commit -m "Random changes including custom config file creation" -q',
            ],
            [
                '.php-cs-fixer.php',
                'dir b/file b.php',
            ],
            [
                '',
                '',
            ],
            '...                                                                 3 / 3 (100%)',
        ];

        yield 'changes-including-composer-lock' => [
            'changes-including-composer-lock',
            [
                'echo "" >> dir\ b/file\ b.php',
                'echo "echo 1;" >> dir\ b/file\ b.php',
                'touch composer.lock',
                'git add .',
                'git commit -m "Random changes including composer.lock" -q',
            ],
            [
                'composer.lock',
                'dir b/file b.php',
            ],
            [
                '',
                '',
            ],
            '...                                                                 3 / 3 (100%)',
        ];
    }

    public function testWithUsingNonExistingFile(): void
    {
        $output = ScriptExecutor::create(
            ['php php-cs-fixer check --config=tests/Fixtures/.php-cs-fixer.append-non-existing-file.php --show-progress=dots'],
            __DIR__.'/../..'
        )->getResult();

        self::assertSame(0, $output->getCode());
        self::assertStringContainsString(' (100%)', $output->getError());
    }

    private static function executeCommand(string $command): CliResult
    {
        return CommandExecutor::create($command, self::$fixtureDir)->getResult();
    }

    /**
     * @param list<string> $scriptParts
     */
    private static function executeScript(array $scriptParts): CliResult
    {
        return ScriptExecutor::create($scriptParts, self::$fixtureDir)->getResult();
    }
}
