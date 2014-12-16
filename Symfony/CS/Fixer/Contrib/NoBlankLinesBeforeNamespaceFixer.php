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
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class NoBlankLinesBeforeNamespaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->needsToBeFixed($tokens)) {
            $this->fixTokens($tokens);
        }

        return $tokens->generateCode();
    }

    /**
     * @param Tokens $tokens
     *
     * @return bool
     */
    private function needsToBeFixed(Tokens $tokens)
    {
        $firstTokenIsOpenTag = $this->isOpenTag($tokens[0]);
        $indexOfFirstNamespaceToken = $this->getIndexOfFirstNamespaceToken($tokens);
        $thereIsANamespaceToken = $indexOfFirstNamespaceToken !== null;
        $onlyNewlinesInBetween = $this->onlyNewlinesBetweenIndices($tokens, 1, $indexOfFirstNamespaceToken);

        return
            $firstTokenIsOpenTag &&
            $thereIsANamespaceToken &&
            $onlyNewlinesInBetween;
    }

    /**
     * @param Tokens $tokens
     */
    private function fixTokens(Tokens $tokens)
    {
        $indexOfFirstNamespaceToken = $this->getIndexOfFirstNamespaceToken($tokens);

        for ($tokenIndex = 1; $tokenIndex < $indexOfFirstNamespaceToken; ++$tokenIndex) {
            $tokens[$tokenIndex]->setContent('');
        }
    }

    /**
     * @param Tokens $tokens
     *
     * @return int|null
     */
    private function getIndexOfFirstNamespaceToken(Tokens $tokens)
    {
        foreach ($tokens as $tokenNumber => $token) {
            if ($token->isGivenKind(T_NAMESPACE)) {
                return $tokenNumber;
            }
        }

        return;
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    private function isOpenTag(Token $token)
    {
        return $token->isGivenKind(T_OPEN_TAG);
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     *
     * @return bool
     */
    private function onlyNewlinesBetweenIndices(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($tokenIndex = $startIndex; $tokenIndex < $endIndex; ++$tokenIndex) {
            if (!preg_match('/^[\n\r]+$/', $tokens[$tokenIndex]->getContent())) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'No blank lines before namespace - i.e. remove a blank line between open tag and namespace';
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
