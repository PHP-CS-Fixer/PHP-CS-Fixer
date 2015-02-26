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

use Symfony\CS\AbstractAlignFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 */
class AlignDoubleArrowFixer extends AbstractAlignFixer
{
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

        $this->injectAlignmentPlaceholders($tokensClone);
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
     * Inject into the text placeholders of candidates of vertical alignment.
     *
     * @param Tokens $tokens
     * @param int    $startAt
     * @param int    $endAt
     *
     * @return array($code, $context_counter)
     */
    private function injectAlignmentPlaceholders(Tokens $tokens, $startAt = null, $endAt = null)
    {
        if (empty($startAt)) {
            $startAt = 0;
        }

        if (empty($endAt)) {
            $endAt = count($tokens);
        }

        for ($index = $startAt; $index < $endAt; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(array(T_FOREACH, T_FOR, T_WHILE, T_IF, T_SWITCH))) {
                $index = $tokens->getNextMeaningfulToken($index);
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }

            if ($token->isGivenKind(T_ARRAY)) {
                $from = $tokens->getNextMeaningfulToken($index);
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $from);
                $index = $until;

                ++$this->deepestLevel;
                ++$this->currentLevel;
                $this->injectAlignmentPlaceholders($tokens, $from, $until);
                --$this->currentLevel;
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

                ++$this->deepestLevel;
                ++$this->currentLevel;
                $this->injectAlignmentPlaceholders($tokens, $from + 1, $until - 1);
                --$this->currentLevel;
                continue;
            }

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $tokenContent = sprintf(self::ALIGNABLE_PLACEHOLDER, $this->currentLevel).$token->getContent();

                $nextToken = $tokens[$index + 1];
                if (!$nextToken->isWhitespace()) {
                    $tokenContent .= ' ';
                } elseif ($nextToken->isWhitespace(array('whitespaces' => " \t"))) {
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
                do {
                    ++$index;
                    $token = $tokens[$index];
                } while (false === strpos($token->getContent(), "\n"));
            }
        }
    }
}
