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
use PhpCsFixer\Preg;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @requires OS Linux|Darwin
 * @coversNothing
 * @group covers-nothing
 */
final class StdinTest extends AbstractSmokeTest
{
    public function testFixingStdin()
    {
        $cwd = __DIR__.'/../..';

        $command = 'php php-cs-fixer fix --rules=@PSR2 --dry-run --diff --using-cache=no';
        $inputFile = 'tests/Fixtures/Integration/set/@PSR2.test-in.php';

        $fileResult = CommandExecutor::create("{$command} {$inputFile}", $cwd)->getResult(false);
        $stdinResult = CommandExecutor::create("{$command} - < {$inputFile}", $cwd)->getResult(false);

        static::assertSame($fileResult->getCode(), $stdinResult->getCode());

        $expectedError = str_replace(
            'Paths from configuration file have been overridden by paths provided as command arguments.'."\n",
            '',
            $fileResult->getError()
        );
        static::assertSame($expectedError, $stdinResult->getError());

        $path = str_replace('/', \DIRECTORY_SEPARATOR, basename(realpath($cwd)).'/'.$inputFile);
        static::assertSame(
            Preg::replace(
                '#/?'.preg_quote($path, '#').'#',
                'php://stdin',
                $this->unifyFooter($fileResult->getOutput())
            ),
            $this->unifyFooter($stdinResult->getOutput())
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
