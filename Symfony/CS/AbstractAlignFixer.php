<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
abstract class AbstractAlignFixer extends AbstractFixer
{
    /**
     * @const Placeholder used as anchor for right alignment.
     */
    const ALIGNABLE_PLACEHOLDER = "\x2 ALIGNABLE%d \x3";

    /**
     * Look for group of placeholders, and provide vertical alignment.
     *
     * @param string $tokens
     * @param int    $deepestLevel
     *
     * @return string
     */
    protected function replacePlaceholder($tokens, $deepestLevel)
    {
        $tmpCode = $tokens->generateCode();

        for ($j = 0; $j <= $deepestLevel; ++$j) {
            $placeholder = sprintf(self::ALIGNABLE_PLACEHOLDER, $j);

            if (false === strpos($tmpCode, $placeholder)) {
                continue;
            }

            $lines = explode("\n", $tmpCode);
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

            $tmpCode = str_replace($placeholder, '', implode("\n", $lines));
        }

        return $tmpCode;
    }
}
