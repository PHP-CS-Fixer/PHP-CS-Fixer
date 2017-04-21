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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class LowercaseConstantsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The PHP constants `true`, `false`, and `null` MUST be in lower case.',
            [new CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isNativeConstant()) {
                continue;
            }

            if (
                $this->isNeighbourAccepted($tokens, $tokens->getPrevMeaningfulToken($index)) &&
                $this->isNeighbourAccepted($tokens, $tokens->getNextMeaningfulToken($index))
            ) {
                $token->setContent(strtolower($token->getContent()));
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function isNeighbourAccepted(Tokens $tokens, $index)
    {
        static $forbiddenTokens = [
            T_AS,
            T_CLASS,
            T_CONST,
            T_EXTENDS,
            T_IMPLEMENTS,
            T_INSTANCEOF,
            T_INSTEADOF,
            T_INTERFACE,
            T_NEW,
            T_NS_SEPARATOR,
            T_PAAMAYIM_NEKUDOTAYIM,
            T_TRAIT,
            T_USE,
            CT::T_USE_TRAIT,
            CT::T_USE_LAMBDA,
        ];

        $token = $tokens[$index];

        if ($token->equalsAny(['{', '}'])) {
            return false;
        }

        return !$token->isGivenKind($forbiddenTokens);
    }
}
