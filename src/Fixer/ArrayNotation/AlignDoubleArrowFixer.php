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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractAlignFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 */
final class AlignDoubleArrowFixer extends AbstractAlignFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOUBLE_ARROW);
    }

    /**
     * Level counter of the current nest level.
     * So one level alignments are not mixed with
     * other level ones.
     *
     * @var int
     */
    private $currentLevel;

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->currentLevel = 0;
        $this->deepestLevel = 0;

        // This fixer works partially on Tokens and partially on string representation of code.
        // During the process of fixing internal state of single Token may be affected by injecting ALIGNABLE_PLACEHOLDER to its content.
        // The placeholder will be resolved by `replacePlaceholder` method by removing placeholder or changing it into spaces.
        // That way of fixing the code causes disturbances in marking Token as changed - if code is perfectly valid then placeholder
        // still be injected and removed, which will cause the `changed` flag to be set.
        // To handle that unwanted behavior we work on clone of Tokens collection and then override original collection with fixed collection.
        $tokensClone = clone $tokens;

        $this->injectAlignmentPlaceholders($tokensClone, 0, count($tokens));
        $content = $this->replacePlaceholder($tokensClone);

        $tokens->setCode($content);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Align double arrow symbols in consecutive lines.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the BinaryOperatorSpacesFixer
        return -10;
    }

    /**
     * Inject into the text placeholders of candidates of vertical alignment.
     *
     * @param Tokens $tokens
     * @param int    $startAt
     * @param int    $endAt
     *
     * @return array($code, $context_counter)
     */
    private function injectAlignmentPlaceholders(Tokens $tokens, $startAt, $endAt)
    {
        for ($index = $startAt; $index < $endAt; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(array(T_FOREACH, T_FOR, T_WHILE, T_IF, T_SWITCH))) {
                $index = $tokens->getNextMeaningfulToken($index);
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }

            if ($token->isGivenKind(T_ARRAY)) { // don't use "$tokens->isArray()" here, short arrays are handled in the next case
                $from = $tokens->getNextMeaningfulToken($index);
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $from);
                $index = $until;

                $this->injectArrayAlignmentPlaceholders($tokens, $from, $until);
                continue;
            }

            if ($token->isGivenKind(CT_ARRAY_SQUARE_BRACE_OPEN)) {
                $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
                if ($prevToken->isGivenKind(array(T_STRING, T_VARIABLE))) {
                    continue;
                }

                $from = $index;
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $from);
                $index = $until;

                $this->injectArrayAlignmentPlaceholders($tokens, $from + 1, $until - 1);
                continue;
            }

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $tokenContent = sprintf(self::ALIGNABLE_PLACEHOLDER, $this->currentLevel).$token->getContent();

                $nextToken = $tokens[$index + 1];
                if (!$nextToken->isWhitespace()) {
                    $tokenContent .= ' ';
                } elseif ($nextToken->isWhitespace(" \t")) {
                    $nextToken->setContent(' ');
                }

                $token->setContent($tokenContent);
                continue;
            }

            if ($token->equals(';')) {
                ++$this->deepestLevel;
                ++$this->currentLevel;
                continue;
            }

            if ($token->equals(',')) {
                for ($i = $index; $i < $endAt - 1; ++$i) {
                    if (false !== strpos($tokens[$i - 1]->getContent(), "\n")) {
                        break;
                    }

                    if ($tokens[$i + 1]->isGivenKind(array(T_ARRAY, CT_ARRAY_SQUARE_BRACE_OPEN))) {
                        $arrayStartIndex = $tokens[$i + 1]->isGivenKind(T_ARRAY)
                            ? $tokens->getNextMeaningfulToken($i + 1)
                            : $i + 1
                        ;
                        $blockType = Tokens::detectBlockType($tokens[$arrayStartIndex]);
                        $arrayEndIndex = $tokens->findBlockEnd($blockType['type'], $arrayStartIndex);

                        if ($tokens->isPartialCodeMultiline($arrayStartIndex, $arrayEndIndex)) {
                            break;
                        }
                    }

                    ++$index;
                }
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $from
     * @param int    $until
     */
    private function injectArrayAlignmentPlaceholders(Tokens $tokens, $from, $until)
    {
        // Only inject placeholders for multi-line arrays
        if ($tokens->isPartialCodeMultiline($from, $until)) {
            ++$this->deepestLevel;
            ++$this->currentLevel;
            $this->injectAlignmentPlaceholders($tokens, $from, $until);
            --$this->currentLevel;
        }
    }
}
