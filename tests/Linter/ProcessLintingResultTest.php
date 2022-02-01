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
    /**
     * @doesNotPerformAssertions
     */
    public function testCheckOK(): void
    {
        $process = $this->prophesize();
        $process->willExtend(Process::class);

        $process
            ->wait()
            ->willReturn(0)
        ;

        $process
            ->isSuccessful()
            ->willReturn(true)
        ;

        $result = new ProcessLintingResult($process->reveal());
        $result->check();
    }

    public function testCheckFail(): void
    {
        $process = $this->prophesize();
        $process->willExtend(Process::class);

        $process
            ->wait()
            ->willReturn(0)
        ;

        $process
            ->isSuccessful()
            ->willReturn(false)
        ;

        $process
            ->getErrorOutput()
            ->willReturn('PHP Parse error:  syntax error, unexpected end of file, expecting \'{\' in test.php on line 4')
        ;

        $process
            ->getExitCode()
            ->willReturn(123)
        ;

        $result = new ProcessLintingResult($process->reveal(), 'test.php');

        $this->expectException(LintingException::class);
        $this->expectExceptionMessage('Parse error: syntax error, unexpected end of file, expecting \'{\' on line 4.');
        $this->expectExceptionCode(123);

        $result->check();
    }
}
