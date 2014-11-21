<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 */
class AlignDoubleArrowFixer extends AbstractFixer
{
    const ALIGNABLE_DOUBLEARROW = "\x2 DOUBLEARROW%d \x3";
    const NEW_LINE = "\n";

    private $contextCounter;
    private $maxContextCounter;

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $this->contextCounter = 0;
        $this->maxContextCounter = -1;
        $tokens = Tokens::fromCode($content);

        $this->injectAlignmentPlaceholders($tokens);

        return $this->replacePlaceholder($tokens);
    }

    /**
     * Inject into the text placeholders of candidates of vertical alignment.
     *
     * @param array $tokens
     * @param int   $startAt
     * @param int   $endAt
     *
     * @return array($code, $context_counter)
     */
    private function injectAlignmentPlaceholders($tokens, $startAt = null, $endAt = null)
    {
        if (empty($startAt)) {
            $startAt = 0;
        }

        if (empty($endAt)) {
            $endAt = count($tokens);
        }

        for ($index = $startAt; $index < $endAt; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(array(T_FOREACH, T_FOR, T_WHILE, T_IF, T_SWITCH, T_CASE))) {
                $index = $tokens->getNextMeaningfulToken($index);
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }

            if ($token->isGivenKind(T_ARRAY)) {
                $from = $tokens->getNextMeaningfulToken($index);
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $from);
                $index = $until;

                ++$this->maxContextCounter;
                ++$this->contextCounter;
                $this->injectAlignmentPlaceholders($tokens, $from, $until);
                --$this->contextCounter;
                continue;
            }

            if ($token->equals('[')) {
                $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
                if ($prevToken->isGivenKind([T_STRING, T_VARIABLE])) {
                    continue;
                }

                $from = $index;
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $from);
                $index = $until;

                ++$this->maxContextCounter;
                ++$this->contextCounter;
                $this->injectAlignmentPlaceholders($tokens, $from + 1, $until - 1);
                --$this->contextCounter;
                continue;
            }

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $tokenContent = sprintf(self::ALIGNABLE_DOUBLEARROW, $this->contextCounter).$token->getContent();

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
                ++$this->maxContextCounter;
                ++$this->contextCounter;
                continue;
            }

            if ($token->equals(',')) {
                do {
                    ++$index;
                    $token = $tokens[$index];
                } while (false === strpos($token->getContent(), self::NEW_LINE));
            }
        }
    }

    /**
     * Look for group of placeholders, and provide vertical alignment.
     *
     * @param string $tokens
     *
     * @return string
     */
    private function replacePlaceholder($tokens)
    {
        $tmpCode = $tokens->generateCode();

        for ($j = 0; $j <= $this->maxContextCounter; ++$j) {
            $placeholder = sprintf(self::ALIGNABLE_DOUBLEARROW, $j);

            if (false === strpos($tmpCode, $placeholder)) {
                continue;
            }

            $lines = explode(self::NEW_LINE, $tmpCode);
            $linesWithPlaceholder = array();
            $blockSize = 0;

            $linesWithPlaceholder[$blockSize] = array();

            foreach ($lines as $index => $line) {
                if (substr_count($line, $placeholder) > 0) {
                    $linesWithPlaceholder[$blockSize][] = $index;
                } else {
                    ++$blockSize;
                    $linesWithPlaceholder[$blockSize] = array();
                }
            }

            $i = 0;
            foreach ($linesWithPlaceholder as $group) {
                if (1 === sizeof($group)) {
                    continue;
                }
                ++$i;
                $rightmostSymbol = 0;

                foreach ($group as $index) {
                    $rightmostSymbol = max($rightmostSymbol, strpos($lines[$index], $placeholder));
                }

                foreach ($group as $index) {
                    $line = $lines[$index];
                    $currentSymbol = strpos($line, $placeholder);
                    $delta = abs($rightmostSymbol - $currentSymbol);

                    if ($delta > 0) {
                        $line = str_replace($placeholder, str_repeat(' ', $delta).$placeholder, $line);
                        $lines[$index] = $line;
                    }
                }
            }

            $tmpCode = str_replace($placeholder, '', implode(self::NEW_LINE, $lines));
        }

        return $tmpCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Align double arrow symbols in consecutive lines.';
    }
}
