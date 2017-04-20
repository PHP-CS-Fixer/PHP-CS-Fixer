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

        for ($index = 1, $count = count($tokens);  $index < $count; ++$index) {
            if ($tokens[$index]->isGivenKind(T_OBJECT_OPERATOR)) {
                if ($this->needLineBreak($index - 1, $tokens)) {
                    $tokens[$index - 1]->setContent($tokens[$index - 1]->getContent().$lineEnding);
                    --$index;
                    continue;
                }

                $prev = $tokens[$index - 1];
                $currentWhitespaces = $this->getLineBreak($prev);

                if (!is_null($currentWhitespaces)) {
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
            $currentWhitespaces = $this->getLineBreak($tokens[$i]);

            if (!is_null($currentWhitespaces)) {
                if ($tokens[$i + 1]->isGivenKind(T_OBJECT_OPERATOR)) {
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
            if ($tokens[$i]->isGivenKind(T_OBJECT_OPERATOR) || !is_null($this->getLineBreak($tokens[$i]))) {
                return $isComment;
            }

            if ($tokens[$i]->isComment()) {
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
    private function getLineBreak(Token $token)
    {
        if (preg_match('/\R(\s*)/', $token->getContent(), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
