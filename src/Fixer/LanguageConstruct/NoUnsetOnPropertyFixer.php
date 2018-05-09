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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gert de Pagter <BackEndTea@gmail.com>
 */
final class NoUnsetOnPropertyFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Properties should be set to `null` instead of using `unset`.',
            [new CodeSample("<?php\nunset(\$this->a);\n")],
            null,
            'Changing variables to `null` instead of unsetting them will mean they still show up '.
            'when looping over class variables.'
        );
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
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_UNSET)
            && $tokens->isAnyTokenKindsFound([T_OBJECT_OPERATOR, T_PAAMAYIM_NEKUDOTAYIM]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before CombineConsecutiveUnsetsFixer
        return 25;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_UNSET)) {
                continue;
            }

            $unsetStart = $tokens->getNextTokenOfKind($index, ['(']);
            $unsetEnd = $tokens->findBlockEnd(
                Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
                $unsetStart
            );
            $closingSemiColon = $tokens->getNextTokenOfKind($unsetEnd, [';']);

            $unsetTokensSubset = $this->createTokensSubSet($tokens, $index, $unsetEnd);
            $unsetOpening = $unsetTokensSubset->getNextTokenOfKind(0, ['(']);

            //We want to remove the `unset` and the `(`
            $unsetTokensSubset->clearAt(0);
            $unsetTokensSubset->clearAt($unsetOpening);
            $unsetTokensSubset->clearEmptyTokens();

            if (!$this->doTokensInvolveProperty($unsetTokensSubset)) {
                continue;
            }

            $this->updateUnset($unsetTokensSubset);

            $tokens->overrideRange($index, $closingSemiColon, $unsetTokensSubset);
        }
    }

    /**
     * Attempts to change unset into is null where possible.
     *
     * @param Tokens $tokens
     */
    private function updateUnset(Tokens $tokens)
    {
        $index = 0;
        $atLastUnset = false;
        do {
            $next = $tokens->getNextTokenOfKind($index, [',']);
            if (null === $next) {
                $atLastUnset = true;
                $next = $tokens->count() - 1;
                $nextTokenSet = $this->createTokensSubSet($tokens, $index, $next + 1);
            } else {
                $nextTokenSet = $this->createTokensSubSet($tokens, $index, $next);
            }

            if ($this->doTokensInvolveProperty($nextTokenSet)
                && !$this->doTokensInvolveArrayAccess($nextTokenSet)
            ) {
                $replacement = $this->createReplacementIsNull($nextTokenSet);
            } else {
                $replacement = $this->createReplacementUnset($nextTokenSet);
            }

            $tokens->overrideRange($index, $next, $replacement);
            $index = $tokens->getNextTokenOfKind($index, [';']) + 1;
        } while (!$atLastUnset);
    }

    /**
     * @param Tokens $filteredTokens
     *
     * @return Token[]
     */
    private function createReplacementIsNull(Tokens $filteredTokens)
    {
        return array_merge(
            $filteredTokens->toArray(),
            [
                new Token([T_WHITESPACE, ' ']),
                new Token('='),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, 'null']),
                new Token(';'),
            ]
        );
    }

    /**
     * @param Tokens $filteredTokens
     *
     * @return Token[]
     */
    private function createReplacementUnset(Tokens $filteredTokens)
    {
        if ($filteredTokens[0]->isWhitespace()) {
            $filteredTokens->clearAt(0);
        }

        $unsetOpeningTokens = [];
        if ($filteredTokens[0]->isWhitespace()) {
            $unsetOpeningTokens[] = new Token([T_WHITESPACE, ' ']);
        }

        array_push($unsetOpeningTokens, new Token([T_UNSET, 'unset']), new Token('('));

        return array_merge(
            $unsetOpeningTokens,
            $filteredTokens->toArray(),
            [
                new Token(')'),
                new Token(';'),
            ]
        );
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     *
     * @return Tokens
     */
    private function createTokensSubSet(Tokens $tokens, $startIndex, $endIndex)
    {
        $array = $tokens->toArray();
        $toAnalyze = array_splice($array, $startIndex, $endIndex - $startIndex);

        return Tokens::fromArray($toAnalyze);
    }

    /**
     * @param Tokens $tokens
     *
     * @return bool
     */
    private function doTokensInvolveProperty(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_PAAMAYIM_NEKUDOTAYIM, T_OBJECT_OPERATOR]);
    }

    /**
     * @param Tokens $tokens
     *
     * @return bool
     */
    private function doTokensInvolveArrayAccess(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(['[', CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN]);
    }
}
