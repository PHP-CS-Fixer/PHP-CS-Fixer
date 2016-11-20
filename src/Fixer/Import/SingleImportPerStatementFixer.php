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

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\WhitespacesFixerConfigAwareInterface;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class SingleImportPerStatementFixer extends AbstractFixer implements WhitespacesFixerConfigAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_USE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $uses = array_reverse($tokensAnalyzer->getImportUseIndexes());

        foreach ($uses as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG)));
            $groupClose = $tokens->getPrevMeaningfulToken($endIndex);

            if ($tokens[$groupClose]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_CLOSE)) {
                $this->fixGroupUse($tokens, $index, $endIndex);
            } else {
                $this->fixMultipleUse($tokens, $index, $endIndex);
            }
        }
    }

    public function getPriority()
    {
        // must be run before NoLeadingImportSlashFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, SpaceAfterSemicolonFixer, NoMultilineWhitespaceBeforeSemicolonsFixer, NoLeadingImportSlashFixer.
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'There MUST be one use keyword per declaration.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return string
     */
    private function detectIndent(Tokens $tokens, $index)
    {
        if (!$tokens[$index - 1]->isWhitespace()) {
            return ''; // cannot detect indent
        }

        $explodedContent = explode("\n", $tokens[$index - 1]->getContent());

        return end($explodedContent);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return array
     */
    private function getGroupDeclaration(Tokens $tokens, $index)
    {
        $groupPrefix = 'use';
        $comment = '';
        for ($i = $index + 1; ; ++$i) {
            if ($tokens[$i]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_OPEN)) {
                $groupOpenIndex = $i;

                break;
            }

            if ($tokens[$i]->isComment()) {
                $comment .= $tokens[$i]->getContent();
                if (!$tokens[$i - 1]->isWhitespace() && !$tokens[$i + 1]->isWhitespace()) {
                    $groupPrefix .= ' ';
                }

                continue;
            }

            if ($tokens[$i]->isWhitespace()) {
                $groupPrefix .= ' ';

                continue;
            }

            $groupPrefix .= $tokens[$i]->getContent();
        }

        return array(
            $groupPrefix,
            $groupOpenIndex,
            $tokens->findBlockEnd(Tokens::BLOCK_TYPE_GROUP_IMPORT_BRACE, $groupOpenIndex),
            $comment,
        );
    }

    /**
     * @param Tokens $tokens
     * @param string $groupPrefix
     * @param int    $groupOpenIndex
     * @param int    $groupCloseIndex
     * @param string $comment
     *
     * @return string[]
     */
    private function getGroupStatements(Tokens $tokens, $groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment)
    {
        $statements = array();
        $statement = $groupPrefix;

        for ($i = $groupOpenIndex + 1; $i <= $groupCloseIndex; ++$i) {
            $token = $tokens[$i];

            if ($token->equalsAny(array(',', array(CT::T_GROUP_IMPORT_BRACE_CLOSE)))) {
                $statements[] = $statement.';';
                $statement = $groupPrefix;

                continue;
            }

            if ($token->isWhitespace()) {
                $j = $tokens->getNextMeaningfulToken($i);
                if ($tokens[$j]->equals(array(T_AS))) {
                    $statement .= ' as ';
                    $i += 2;
                }

                if ($token->isWhitespace(" \t") || '//' !== substr($tokens[$i - 1]->getContent(), 0, 2)) {
                    continue;
                }
            }

            $statement .= $token->getContent();
        }

        if ('' !== $comment) {
            $statements[0] .= ' '.$comment;
        }

        return $statements;
    }

    private function fixGroupUse(Tokens $tokens, $index, $endIndex)
    {
        list($groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment) = $this->getGroupDeclaration($tokens, $index);
        $statements = $this->getGroupStatements($tokens, $groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment);

        if (count($statements) < 2) {
            return;
        }

        $tokens->clearRange($index, $groupCloseIndex);
        if ($tokens[$endIndex]->equals(';')) {
            $tokens[$endIndex]->clear();
        }

        $ending = $this->whitespacesConfig->getLineEnding();
        $importTokens = Tokens::fromCode('<?php '.implode($ending, $statements));
        $importTokens[0]->clear();
        $importTokens->clearEmptyTokens();

        $tokens->insertAt($index, $importTokens);
    }

    private function fixMultipleUse(Tokens $tokens, $index, $endIndex)
    {
        $ending = $this->whitespacesConfig->getLineEnding();

        for ($i = $endIndex - 1; $i > $index; --$i) {
            if (!$tokens[$i]->equals(',')) {
                continue;
            }

            $tokens->overrideAt($i, new Token(';'));
            $i = $tokens->getNextMeaningfulToken($i);
            $tokens->insertAt($i, new Token(array(T_USE, 'use')));
            $tokens->insertAt($i + 1, new Token(array(T_WHITESPACE, ' ')));

            $indent = $this->detectIndent($tokens, $index);
            if ($tokens[$i - 1]->isWhitespace()) {
                $tokens[$i - 1]->setContent($ending.$indent);

                continue;
            }

            if (false === strpos($tokens[$i - 1]->getContent(), "\n")) {
                $tokens->insertAt($i, new Token(array(T_WHITESPACE, $ending.$indent)));
            }
        }
    }
}
