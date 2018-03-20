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

namespace PhpCsFixer\Tests\Smoke;

use Keradus\CliExecutor\BashScriptExecutor;
use Keradus\CliExecutor\CommandExecutor;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @requires OS Linux|Darwin
 * @coversNothing
 * @group covers-nothing
 * @large
 */
final class CiIntegrationTest extends TestCase
{
    public static $fixtureDir;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$fixtureDir = __DIR__.'/../Fixtures/ci-integration';

        try {
            self::executeScript(array(
                'rm -rf .git',
                'git init -q',
                'git config user.name test',
                'git config user.email test',
                'git add .',
                'git commit -m "init" -q',
            ));
        } catch (\RuntimeException $e) {
            self::markTestSkipped($e->getMessage());
        }
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::executeCommand('rm -rf .git');
    }

    public function tearDown()
    {
        parent::tearDown();

        self::executeScript(array(
            'git reset . -q',
            'git checkout . -q',
            'git clean -fdq',
            'git checkout master -q',
        ));
    }

    /**
     * @param string   $branchName
     * @param string[] $caseCommands
     * @param string[] $expectedResult1Lines
     * @param string[] $expectedResult2Lines
     * @param string   $expectedResult3Files
     *
     * @dataProvider provideIntegrationCases
     */
    public function testIntegration(
        $branchName,
        array $caseCommands,
        array $expectedResult1Lines,
        array $expectedResult2Lines,
        $expectedResult3Files
    ) {
        self::executeScript(array_merge(
            array(
                "git checkout -b ${branchName} -q",
            ),
            $caseCommands
        ));

        $integrationScript = explode("\n", str_replace('vendor/bin/', './../../../', file_get_contents(__DIR__.'/../../dev-tools/ci-integration.sh')));
        $steps = array(
            "COMMIT_RANGE=\"master..${branchName}\"",
            "{$integrationScript[3]}\n{$integrationScript[4]}",
            $integrationScript[5],
            $integrationScript[6],
            $integrationScript[7],
        );

        $result1 = self::executeScript(array(
            $steps[0],
            $steps[1],
            $steps[2],
            'echo "$CHANGED_FILES"',
        ));

        $this->assertSame(implode("\n", $expectedResult1Lines)."\n", $result1->getOutput());

        $result2 = self::executeScript(array(
            $steps[0],
            $steps[1],
            $steps[2],
            $steps[3],
            'echo "${EXTRA_ARGS}"',
        ));

        $this->assertSame(implode("\n", $expectedResult2Lines), $result2->getOutput());

        $result3 = self::executeScript(array(
            $steps[0],
            $steps[1],
            $steps[2],
            $steps[3],
            $steps[4],
        ));

        $optionalIncompatibilityWarning = 'PHP needs to be a minimum version of PHP 5.3.6 and maximum version of PHP 7.2.*.
Ignoring environment requirements because `PHP_CS_FIXER_IGNORE_ENV` is set. Execution may be unstable.
';

        $optionalXdebugWarning = 'You are running PHP CS Fixer with xdebug enabled. This has a major impact on runtime performance.
If you need help while solving warnings, ask at https://gitter.im/PHP-CS-Fixer, we will help you!
';

        $executionDetails = "Loaded config default from \".php_cs.dist\".
${expectedResult3Files}
Legend: ?-unknown, I-invalid file syntax, file ignored, S-Skipped, .-no changes, F-fixed, E-error";

        $this->assertRegExp(
            sprintf(
                '/^(%s)?(%s)?%s$/',
                preg_quote($optionalIncompatibilityWarning, '/'),
                preg_quote($optionalXdebugWarning, '/'),
                preg_quote($executionDetails, '/')
            ),
            $result3->getError()
        );

        $this->assertRegExp(
            '/^\s*Checked all files in \d+\.\d+ seconds, \d+\.\d+ MB memory used\s*$/',
            $result3->getOutput()
        );
    }

    public function provideIntegrationCases()
    {
        return array(
            array(
                'random-changes',
                array(
                    'touch dir\ a/file.php',
                    'rm -r dir\ c',
                    'echo "" >> dir\ b/file\ b.php',
                    'echo "echo 1;" >> dir\ b/file\ b.php',
                    'git add .',
                    'git commit -m "Random changes" -q',
                ),
                array(
                    'dir a/file.php',
                    'dir b/file b.php',
                ),
                array(
                    '--path-mode=intersection',
                    '--',
                    'dir a/file.php',
                    'dir b/file b.php',
                    '',
                ),
                'S.',
            ),
            array(
                'changes-including-dist-config-file',
                array(
                    'echo "" >> dir\ b/file\ b.php',
                    'echo "echo 1;" >> dir\ b/file\ b.php',
                    // `sed -i ...` is not handled the same on Linux and macOS
                    'sed -e \'s/@Symfony/@PSR2/\' .php_cs.dist > .php_cs.dist.new',
                    'mv .php_cs.dist.new .php_cs.dist',
                    'git add .',
                    'git commit -m "Random changes including config file" -q',
                ),
                array(
                    '.php_cs.dist',
                    'dir b/file b.php',
                ),
                array(
                    '',
                    '',
                ),
                '...',
            ),
            array(
                'changes-including-custom-config-file-creation',
                array(
                    'echo "" >> dir\ b/file\ b.php',
                    'echo "echo 1;" >> dir\ b/file\ b.php',
                    'sed -e \'s/@Symfony/@PSR2/\' .php_cs.dist > .php_cs',
                    'git add .',
                    'git commit -m "Random changes including custom config file creation" -q',
                ),
                array(
                    '.php_cs',
                    'dir b/file b.php',
                ),
                array(
                    '',
                    '',
                ),
                '...',
            ),
            array(
                'changes-including-composer-lock',
                array(
                    'echo "" >> dir\ b/file\ b.php',
                    'echo "echo 1;" >> dir\ b/file\ b.php',
                    'touch composer.lock',
                    'git add .',
                    'git commit -m "Random changes including composer.lock" -q',
                ),
                array(
                    'composer.lock',
                    'dir b/file b.php',
                ),
                array(
                    '',
                    '',
                ),
                '...',
            ),
        );
    }

    private static function executeCommand($command)
    {
        return CommandExecutor::create($command, self::$fixtureDir)->getResult();
    }

    private static function executeScript(array $scriptParts)
    {
        return BashScriptExecutor::create($scriptParts, self::$fixtureDir)->getResult();
    }
}
