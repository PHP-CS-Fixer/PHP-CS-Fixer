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
 */
class AlignDoubleArrowFixer extends AbstractFixer
{
    const ALIGNABLE_DOUBLEARROW = "\x2 DOUBLEARROW%d \x3";
    const NEW_LINE = "\n";

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        list($tmpCode, $contextCounter) = $this->injectAlignmentPlaceholders($content);

        return $this->replacePlaceholder($tmpCode, $contextCounter);
    }

    /**
     * Inject into the text placeholders of candidates of vertical alignment.
     *
     * @param  string       $content
     * @return array($code, $context_counter)
     */
    private function injectAlignmentPlaceholders($content)
    {
        $contextCounter = 0;
        $code = '';
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $idx => $token) {
            $tokenContent = $token->getContent();

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $code .= sprintf(self::ALIGNABLE_DOUBLEARROW, $contextCounter).$tokenContent;

                $nextToken = $tokens[$idx + 1];

                if (!$nextToken->isWhitespace()) {
                    // if there is no whitespaces after T_DOUBLE_ARROW add it
                    $code .= ' ';
                } elseif ($nextToken->isWhitespace(array('whitespaces' => " \t"))) {
                    // if there is single line whitespaces after T_DOUBLE_ARROW normalize it with single space
                    $nextToken->setContent(' ');
                }

                continue;
            }

            if ($token->equals(';') || $token->isGivenKind(array(T_FOREACH, T_ARRAY))) {
                ++$contextCounter;
                $code .= $tokenContent;
                continue;
            }

            if ($token->equals('[')) {
                $prevToken = $tokens[$tokens->getPrevNonWhitespace($idx)];

                if ($prevToken->isGivenKind(T_DOUBLE_ARROW)) {
                    ++$contextCounter;
                }
            }

            $code .= $tokenContent;
        }

        return array($code, $contextCounter);
    }

    /**
     * Look for group of placeholders, and provide vertical alignment.
     *
     * @param  string $tmpCode
     * @param  int    $contextCounter
     * @return string
     */
    private function replacePlaceholder($tmpCode, $contextCounter)
    {
        for ($j = 0; $j <= $contextCounter; ++$j) {
            $placeholder = sprintf(self::ALIGNABLE_DOUBLEARROW, $j);

            if (false === strpos($tmpCode, $placeholder)) {
                continue;
            }

            $lines = explode(self::NEW_LINE, $tmpCode);
            $linesWithPlaceholder = array();
            $blockSize = 0;

            $linesWithPlaceholder[$blockSize] = array();

            foreach ($lines as $idx => $line) {
                if (substr_count($line, $placeholder) > 0) {
                    $linesWithPlaceholder[$blockSize][] = $idx;
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

                foreach ($group as $idx) {
                    $rightmostSymbol = max($rightmostSymbol, strpos($lines[$idx], $placeholder));
                }

                foreach ($group as $idx) {
                    $line = $lines[$idx];
                    $currentSymbol = strpos($line, $placeholder);
                    $delta = abs($rightmostSymbol - $currentSymbol);

                    if ($delta > 0) {
                        $line = str_replace($placeholder, str_repeat(' ', $delta).$placeholder, $line);
                        $lines[$idx] = $line;
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
