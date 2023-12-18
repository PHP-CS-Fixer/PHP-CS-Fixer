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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Frank Prins
 */
final class PhpdocCompactSingleLineFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHPDoc blocks should be single line when only one line of content is present',
            [new CodeSample("<?php /**\n * This is only one line of content\n  */\n")]
        );
    }

    public function getPriority(): int
    {
        return 4;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            if (Preg::match('#^/\*\*\n*\s*\*.*\n\s*\*/$#', $token->getContent())) {
                $tokens[$index] = new Token([T_DOC_COMMENT, Preg::replace('#^/\*\*\n*\s*\*\s*(.*)\n\s*\*/$#', '/** $1 */', $token->getContent())]);
            }
        }
    }
}
