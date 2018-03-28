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

use Keradus\CliExecutor\CommandExecutor;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @requires OS Linux|Darwin
 * @coversNothing
 * @group covers-nothing
 */
final class StdinTest extends TestCase
{
    private static $cwd;

    public static function setUpBeforeClass()
    {
        self::$cwd = __DIR__.'/../..';
    }

    public function testFixingStdin()
    {
        $command = 'php php-cs-fixer fix --rules=@PSR2 --dry-run --diff --using-cache=no';
        $inputFile = 'tests/Fixtures/Integration/set/@PSR2.test-in.php';

        $fileResult = CommandExecutor::create("${command} ${inputFile}", self::$cwd)->getResult(false);
        $stdinResult = CommandExecutor::create("${command} - < ${inputFile}", self::$cwd)->getResult(false);

        $this->assertSame(
            [
                'code' => $fileResult->getCode(),
                'error' => str_replace(
                    'Paths from configuration file have been overridden by paths provided as command arguments.'."\n",
                    '',
                    $fileResult->getError()
                ),
                'output' => str_ireplace(
                    str_replace('/', DIRECTORY_SEPARATOR, 'PHP-CS-Fixer/tests/Fixtures/Integration/set/@PSR2.test-in.php'),
                    'php://stdin',
                    $this->unifyFooter($fileResult->getOutput())
                ),
            ],
            [
                'code' => $stdinResult->getCode(),
                'error' => $stdinResult->getError(),
                'output' => $this->unifyFooter($stdinResult->getOutput()),
            ]
        );
    }

    /**
     * @param string $output
     *
     * @return string
     */
    private function unifyFooter($output)
    {
        return preg_replace(
            '/Checked all files in \d+\.\d+ seconds, \d+\.\d+ MB memory used/',
            'Footer',
            $output
        );
    }
}
