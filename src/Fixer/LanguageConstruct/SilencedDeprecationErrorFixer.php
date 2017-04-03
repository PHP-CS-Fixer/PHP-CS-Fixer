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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jules Pietri <jules@heahprod.com>
 */
final class SilencedDeprecationErrorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Ensures deprecation notices are silenced.',
            array(new CodeSample("<?php\ntrigger_error('Warning.', E_USER_DEPRECATED);")),
            null,
            'Silencing of deprecation errors might cause changes to code behaviour.'
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
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->equals(array(T_STRING, 'trigger_error'), false)) {
                continue;
            }

            $start = $index;
            $prev = $tokens->getPrevMeaningfulToken($start);
            if ($tokens[$prev]->isGivenKind(T_NS_SEPARATOR)) {
                $start = $prev;
                $prev = $tokens->getPrevMeaningfulToken($start);
            }

            if ($tokens[$prev]->isGivenKind(T_STRING) || $tokens[$prev]->equals('@')) {
                continue;
            }

            $end = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $tokens->getNextTokenOfKind($index, array(T_STRING, '(')));
            if ($tokens[$tokens->getPrevMeaningfulToken($end)]->equals(array(T_STRING, 'E_USER_DEPRECATED'))) {
                $tokens->insertAt($start, new Token('@'));
            }
        }
    }
}
