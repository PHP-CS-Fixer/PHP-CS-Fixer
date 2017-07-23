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
use PhpCsFixer\Tokenizer\CT;
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
            [new CodeSample("<?php\n\$user->setEmail('voff.web@gmail.com')\n         ->setPassword('233434');"),
        ]
        );
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

        for ($index = 1, $count = count($tokens); $index < $count; ++$index) {
            if ($tokens[$index]->isGivenKind(T_OBJECT_OPERATOR)) {
                if ($this->needLineBreak($index - 1, $tokens)) {
                    $tokens[$index - 1] = new Token([T_WHITESPACE, $tokens[$index - 1]->getContent().$lineEnding]);
                    --$index;
                    continue;
                }

                $prevIndex = $index - 1;
                $prevToken = $tokens[$prevIndex];
                $currentWhitespaces = $this->getCurrentWhitespaces($prevToken->getContent());

                if (null !== $currentWhitespaces) {
                    $prevMeaningIndex = $tokens->getPrevMeaningfulToken($index);
                    $rightWhitespaces = $this->getRightIndents($prevMeaningIndex, $tokens);

                    if ($currentWhitespaces !== $rightWhitespaces) {
                        $tokens[$prevIndex] = new Token([T_WHITESPACE, $lineEnding.$rightWhitespaces]);
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
            if ($i > 0) {
                $codeToFindIndents = $tokens->generatePartialCode($i - 1, $i);
            } else {
                $codeToFindIndents = $tokens[$i]->getContent();
            }

            $currentWhitespaces = $this->getCurrentWhitespaces($codeToFindIndents);

            if (null !== $currentWhitespaces) {
                if ($tokens[$i + 1]->isGivenKind(T_OBJECT_OPERATOR) || $this->isMultiLineMethod($i, $index, $tokens)) {
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
            if ($tokens[$i]->isGivenKind(T_OBJECT_OPERATOR) || null !== $this->getCurrentWhitespaces($tokens[$i]->getContent())) {
                return $isComment;
            }

            if ($tokens[$i]->isComment()) {
                $isComment = true;
            }
        }

        return false;
    }

    /**
     * @param string $content
     *
     * @return string|null
     */
    private function getCurrentWhitespaces($content)
    {
        if (1 === preg_match('/\R{1}([ \t]*)$/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param int    $start
     * @param int    $end
     * @param Tokens $tokens
     *
     * @return bool
     */
    private function isMultiLineMethod($start, $end, Tokens $tokens)
    {
        if ($tokens[$end]->equalsAny([')', [CT::T_BRACE_CLASS_INSTANTIATION_CLOSE]])) {
            if ($tokens[$end]->isGivenKind(CT::T_BRACE_CLASS_INSTANTIATION_CLOSE)) {
                // src/Tokenizer/Transformer/BraceClassInstantiationTransformer.php
                if ($tokens->findGivenKind(CT::T_BRACE_CLASS_INSTANTIATION_OPEN, $start, $end)) {
                    return false;
                }

                return true;
            }

            $methodStart = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $end, false);
            if ($methodStart < $start) {
                return true;
            }
        }

        return false;
    }
}
