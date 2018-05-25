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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\ProcessLintingResult;
use PhpCsFixer\Tests\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\ProcessLintingResult
 */
final class ProcessLintingResultTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testCheckOK()
    {
        $process = $this->prophesize();
        $process->willExtend(\Symfony\Component\Process\Process::class);

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

    public function testCheckFail()
    {
        $process = $this->prophesize();
        $process->willExtend(\Symfony\Component\Process\Process::class);

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
            ->willReturn('test')
        ;

        $process
            ->getExitCode()
            ->willReturn(123)
        ;

        $result = new ProcessLintingResult($process->reveal());

        $this->expectException(
            \PhpCsFixer\Linter\LintingException::class
        );
        $this->expectExceptionMessageRegExp(
            '#^test$#'
        );
        $this->expectExceptionCode(
            123
        );

        $result->check();
    }
}
