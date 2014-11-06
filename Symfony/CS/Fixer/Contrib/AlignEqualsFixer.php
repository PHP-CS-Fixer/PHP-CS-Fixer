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
 */
class AlignEqualsFixer extends AbstractFixer
{
    const ALIGNABLE_EQUAL = "\x2 EQUAL%d \x3";
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
     * @param string $content
     *
     * @return array($code, $context_counter)
     */
    private function injectAlignmentPlaceholders($content)
    {
        $contextCounter = 0;
        $parenCount = 0;
        $bracketCount = 0;
        $code = '';
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            $tokenContent = $token->getContent();

            if ($token->equals('=')
                && 0 === $parenCount && 0 === $bracketCount) {
                $code .= sprintf(self::ALIGNABLE_EQUAL, $contextCounter).$tokenContent;
                continue;
            }

            if ($token->isGivenKind(T_FUNCTION)) {
                ++$contextCounter;
            } elseif ($token->equals('(')) {
                ++$parenCount;
            } elseif ($token->equals(')')) {
                --$parenCount;
            } elseif ($token->equals('[')) {
                ++$bracketCount;
            } elseif ($token->equals(']')) {
                --$bracketCount;
            }

            $code .= $tokenContent;
        }

        return array($code, $contextCounter);
    }

    /**
     * Look for group of placeholders, and provide vertical alignment.
     *
     * @param string $tmpCode
     * @param int    $contextCounter
     *
     * @return string
     */
    private function replacePlaceholder($tmpCode, $contextCounter)
    {
        for ($j = 0; $j <= $contextCounter; ++$j) {
            $placeholder = sprintf(self::ALIGNABLE_EQUAL, $j);

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
        return 'Align equals symbols in consecutive lines.';
    }
}
