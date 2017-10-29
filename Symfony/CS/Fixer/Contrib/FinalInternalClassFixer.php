<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class FinalInternalClassFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CLASS);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_CLASS)) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

            // ignore class if it is abstract or already final
            if ($prevToken->isGivenKind(array(T_ABSTRACT, T_FINAL))) {
                continue;
            }

            $docToken = $tokens[$tokens->getPrevNonWhitespace($index)];

            // ignore class if it has no class-level docs
            if (!$docToken->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($docToken->getContent());

            // ignore class if it is has no @internal annotation
            if (empty($doc->getAnnotationsOfType('internal'))) {
                continue;
            }

            // make class final
            $tokens->insertAt(
                $index,
                array(
                    new Token(array(T_FINAL, 'final')),
                    new Token(array(T_WHITESPACE, ' '))
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All internal classes should be final except abstract ones. Warning! This could change code behavior.';
    }
}
