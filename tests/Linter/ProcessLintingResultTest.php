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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\ProcessLintingResult;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Process\Process;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Linter\ProcessLintingResult
 */
final class ProcessLintingResultTest extends TestCase
{
    public function testCheckOK(): void
    {
        $process = new class([]) extends Process {
            public function wait(callable $callback = null): int
            {
                return 0;
            }

            public function isSuccessful(): bool
            {
                return true;
            }
        };

        $result = new ProcessLintingResult($process);
        $result->check();

        $this->expectNotToPerformAssertions();
    }

    public function testCheckFail(): void
    {
        $process = new class([]) extends Process {
            public function wait(callable $callback = null): int
            {
                return 0;
            }

            public function isSuccessful(): bool
            {
                return false;
            }

            public function getErrorOutput(): string
            {
                return 'PHP Parse error:  syntax error, unexpected end of file, expecting \'{\' in test.php on line 4';
            }

            public function getExitCode(): int
            {
                return 123;
            }
        };

        $result = new ProcessLintingResult($process, 'test.php');

        $this->expectException(LintingException::class);
        $this->expectExceptionMessage('Parse error: syntax error, unexpected end of file, expecting \'{\' on line 4.');
        $this->expectExceptionCode(123);

        $result->check();
    }
}
