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

use PhpCsFixer\Linter\TokenizerLintingResult;
use PhpCsFixer\Tests\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\TokenizerLintingResult
 */
final class TokenizerLintingResultTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testTokenizerLintingResultOK()
    {
        $result = new TokenizerLintingResult();
        $result->check();
    }

    public function testTokenizerLintingResultFail()
    {
        $error = new \ParseError('PHPUnit', 567);
        $line = __LINE__ - 1;

        $result = new TokenizerLintingResult($error);

        $this->expectException(
            \PhpCsFixer\Linter\LintingException::class
        );

        $this->expectExceptionMessageRegExp(
            sprintf('#^%s: PHPUnit on line %d\.#', preg_quote(\get_class($error), '#'), $line)
        );

        $this->expectExceptionCode(
            567
        );

        $result->check();
    }
}
