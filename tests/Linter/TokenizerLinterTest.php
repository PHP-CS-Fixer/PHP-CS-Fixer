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
use PhpCsFixer\Linter\TokenizerLinter;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Linter\TokenizerLinter
 * @covers \PhpCsFixer\Linter\TokenizerLintingResult
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class TokenizerLinterTest extends AbstractLinterTestCase
{
    public function testIsAsync(): void
    {
        self::assertFalse($this->createLinter()->isAsync());
    }

    protected function createLinter(): LinterInterface
    {
        return new TokenizerLinter();
    }
}
