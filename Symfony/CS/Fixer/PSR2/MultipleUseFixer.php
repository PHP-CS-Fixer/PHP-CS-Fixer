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

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
class MultipleUseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $uses = array_reverse($tokens->getImportUseIndexes());

        foreach ($uses as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG)));
            $groupClose = $tokens->getPrevMeaningfulToken($endIndex);
            $tokens[$groupClose]->equals('}') ?
                $this->fixGroupUse($tokens, $index, $endIndex) :
                $this->fixMultipleUse($tokens, $index, $endIndex)
            ;
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST be one use keyword per declaration.';
    }

    public function getPriority()
    {
        // must be run before UnusedUseFixer, OrderedUseFixer, SpacesBeforeSemicolonFixer, SpacesAfterSemicolonFixer and MultilineSpacesBeforeSemicolonFixer

        return 1;
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
            if ($tokens[$i]->equals('{')) {
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
            $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $groupOpenIndex),
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
            if ($tokens[$i]->equalsAny(array(',', '}'))) {
                $statements[] = $statement.';';
                $statement = $groupPrefix;

                continue;
            }

            if ($tokens[$i]->isWhitespace()) {
                $j = $tokens->getNextMeaningfulToken($i);
                if ($tokens[$j]->equals(array(T_AS))) {
                    $statement .= ' as ';
                    $i += 2;
                }

                continue;
            }

            $statement .= $tokens[$i]->getContent();
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

        $importTokens = Tokens::fromCode('<?php '.implode("\n", $statements));
        $importTokens[0]->clear();

        $tokens->insertAt($index, $importTokens);
    }

    private function fixMultipleUse(Tokens $tokens, $index, $endIndex)
    {
        for ($i = $endIndex - 1; $i > $index; --$i) {
            if (!$tokens[$i]->equals(',')) {
                continue;
            }

            $tokens[$i]->setContent(';');
            $i = $tokens->getNextMeaningfulToken($i);
            $tokens->insertAt($i, new Token(array(T_USE, 'use')));
            $tokens->insertAt($i + 1, new Token(array(T_WHITESPACE, ' ')));

            $indent = $this->detectIndent($tokens, $index);
            if ($tokens[$i - 1]->isWhitespace()) {
                $tokens[$i - 1]->setContent("\n".$indent);

                continue;
            }

            if (false === strpos($tokens[$i - 1]->getContent(), "\n")) {
                $tokens->insertAt($i, new Token(array(T_WHITESPACE, "\n".$indent)));
            }
        }
    }
}
