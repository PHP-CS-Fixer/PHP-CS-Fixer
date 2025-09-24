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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Linter\ProcessLinter
 * @covers \PhpCsFixer\Linter\ProcessLintingResult
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ProcessLinterTest extends AbstractLinterTestCase
{
    public function testIsAsync(): void
    {
        self::assertTrue($this->createLinter()->isAsync());
    }

    public function testSerialize(): void
    {
        $linter = new ProcessLinter();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot serialize '.ProcessLinter::class);

        serialize($linter);
    }

    public function testUnserialize(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot unserialize '.ProcessLinter::class);

        unserialize(self::createSerializedStringOfClassName(ProcessLinter::class));
    }

    protected function createLinter(): LinterInterface
    {
        return new ProcessLinter();
    }
}
