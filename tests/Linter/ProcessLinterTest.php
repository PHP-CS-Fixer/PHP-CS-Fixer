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

use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\ProcessLinter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\ProcessLinter
 * @covers \PhpCsFixer\Linter\ProcessLintingResult
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ProcessLinterTest extends AbstractLinterTestCase
{
    public function testIsAsync(): void
    {
        self::assertTrue($this->createLinter()->isAsync());
    }

    public function testSleep(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot serialize PhpCsFixer\Linter');

        $linter = new ProcessLinter();
        $linter->__sleep();
    }

    public function testWakeup(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot unserialize PhpCsFixer\Linter');

        $linter = new ProcessLinter();
        $linter->__wakeup();
    }

    protected function createLinter(): LinterInterface
    {
        return new ProcessLinter();
    }
}
