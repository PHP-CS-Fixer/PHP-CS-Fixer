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
use PhpCsFixer\Preg;

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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class StdinTest extends AbstractSmokeTestCase
{
    public function testFixingStdin(): void
    {
        $cwd = __DIR__.'/../..';

        $command = 'php php-cs-fixer fix --sequential --rules=@PSR2 --dry-run --diff --using-cache=no';
        $inputFile = 'tests/Fixtures/Integration/set/@PSR2.test-in.php';

        $fileResult = CommandExecutor::create("{$command} {$inputFile}", $cwd)->getResult(false);
        $stdinResult = CommandExecutor::create("{$command} - < {$inputFile}", $cwd)->getResult(false);

        self::assertSame($fileResult->getCode(), $stdinResult->getCode());

        $expectedError = str_replace(
            'Paths from configuration have been overridden by paths provided as command arguments.'."\n",
            '',
            $fileResult->getError()
        );

        self::assertSame($expectedError, $stdinResult->getError());

        $fileResult = $this->unifyFooter($fileResult->getOutput());

        $file = realpath($cwd).'/'.$inputFile;
        $path = str_replace('/', \DIRECTORY_SEPARATOR, $file);
        $fileResult = str_replace("\n--- ".$path."\n", "\n--- php://stdin\n", $fileResult);
        $fileResult = str_replace("\n+++ ".$path."\n", "\n+++ php://stdin\n", $fileResult);

        $fileResult = Preg::replace(
            '#/?'.preg_quote($inputFile, '#').'#',
            'php://stdin',
            $fileResult
        );

        self::assertSame(
            $fileResult,
            $this->unifyFooter($stdinResult->getOutput())
        );
    }

    private function unifyFooter(string $output): string
    {
        return Preg::replace(
            '/Found \d+ of \d+ files that can be fixed in \d+\.\d+ seconds, \d+\.\d+ MB memory used/',
            'Footer',
            $output
        );
    }
}
