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
use PhpCsFixer\Linter\TokenizerLintingResult;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Linter\TokenizerLintingResult
 */
final class TokenizerLintingResultTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testTokenizerLintingResultOK(): void
    {
        $result = new TokenizerLintingResult();
        $result->check();
    }

    public function testTokenizerLintingResultFailParseError(): void
    {
        $error = new \ParseError('syntax error, unexpected end of file, expecting \'{\'', 567);
        $line = __LINE__ - 1;

        $result = new TokenizerLintingResult($error);

        $this->expectException(
            LintingException::class
        );

        $this->expectExceptionMessage(
            sprintf('Parse error: syntax error, unexpected end of file, expecting \'{\' on line %d.', $line)
        );

        $this->expectExceptionCode(
            567
        );

        $result->check();
    }

    public function testTokenizerLintingResultFailCompileError(): void
    {
        $error = new \CompileError('Multiple access type modifiers are not allowed');
        $line = __LINE__ - 1;

        $result = new TokenizerLintingResult($error);

        $this->expectException(
            LintingException::class
        );

        $this->expectExceptionMessage(
            sprintf('Fatal error: Multiple access type modifiers are not allowed on line %d.', $line)
        );

        $result->check();
    }
}
