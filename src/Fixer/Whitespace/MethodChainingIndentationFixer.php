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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vladimir Boliev <voff.web@gmail.com>
 */
final class MethodChainingIndentationFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Method chaining MUST be properly indented.',
            array(new CodeSample("<?php\n\$user->setEmail('voff.web@gmail.com')\n         ->setPassword('233434');"),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_OBJECT_OPERATOR);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index = 1; $index < count($tokens); ++$index) {
            if ($tokens[$index]->equals(array(T_OBJECT_OPERATOR))) {
                if ($this->needLineBreak($index - 1, $tokens)) {
                    $tokens[$index - 1]->setContent($tokens[$index - 1]->getContent().$lineEnding);
                    --$index;
                    continue;
                }

                $prev = $tokens[$index - 1];
                $currentWhitespaces = $this->isLineBreak($prev);

                if (false !== $currentWhitespaces) {
                    $prevMeaningIndex = $tokens->getPrevMeaningfulToken($index);
                    $rightWhitespaces = $this->getRightIndents($prevMeaningIndex, $tokens);

                    if ($currentWhitespaces !== $rightWhitespaces) {
                        $prev->setContent($lineEnding.$rightWhitespaces);
                    }
                }
            }
        }
    }

    /**
     * @param int    $index
     * @param Tokens $tokens
     *
     * @return string
     */
    private function getRightIndents($index, Tokens $tokens)
    {
        $indent = $this->whitespacesConfig->getIndent();

        for ($i = $index; $i >= 0; --$i) {
            $currentWhitespaces = $this->isLineBreak($tokens[$i]);

            if ($currentWhitespaces !== false) {
                if ($tokens[$i + 1]->equals(array(T_OBJECT_OPERATOR))) {
                    return $currentWhitespaces;
                }

                return $currentWhitespaces.$indent;
            }
        }

        return $indent;
    }

    /**
     * @param int    $index
     * @param Tokens $tokens
     *
     * @return bool
     */
    private function needLineBreak($index, Tokens $tokens)
    {
        $prevMeaningful = $tokens->getPrevMeaningfulToken($index);
        $isComment = false;

        for ($i = $index; $i > $prevMeaningful; --$i) {
            if ($tokens[$i]->equals(array(T_OBJECT_OPERATOR)) || false !== $this->isLineBreak($tokens[$i])) {
                return $isComment;
            }

            if ($tokens[$i]->equalsAny(array(array(T_COMMENT), array(T_DOC_COMMENT), array(T_START_HEREDOC)))) {
                $isComment = true;
            }
        }

        return false;
    }

    /**
     * @param Token $token
     *
     * @return string|bool
     */
    private function isLineBreak(Token $token)
    {
        $matches = array();
        $content = $token->getContent();

        if (preg_match('/\R(\s*)/', $content, $matches)) {
            return $matches[1];
        }

        return false;
    }
}
