<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Util\Tokenizer;

/**
 * This fixer aligns code bodies properly given the depth of the code block.
 *
 * @author Evan Villemez <evillemez@gmail.com>
 */
class AlignmentFixer implements FixerInterface
{

    public function fix(\SplFileInfo $file, $content)
    {
        $this->tokenizer = new Tokenizer;

        $content = $this->fixWhitespace($content);

        return $content;
    }

    /**
     * Make sure that code block bodies are aligned properly given their level
     * of depth in nested code blocks.
     *
     * @param  string $content
     * @return string
     */
    protected function fixWhitespace($content)
    {
        $final = '';
        $tokens = $this->tokenizer->getTokens($content);
        $depth = 0;

        for ($i = 0; $i < count($tokens); $i++) {
            $t = $tokens[$i];
            $p = isset($tokens[$i - 1]) ? $tokens[$i - 1] : false;
            $n = isset($tokens[$i + 1]) ? $tokens[$i + 1] : false;

            //check for depth changes based on opening/closing braces
            if (T_CURLY_BRACE_OPEN === $t[0]) {
                $depth += 1;
            }

            if (T_CURLY_BRACE_CLOSE === $t[0]) {
                $depth -= 1;
            }

            //if this is the beginning of a new line (as near as we can tell)
            if ($p && $t[2] > $p[2]) {
                //replace whitespace at beginning of line with proper length
                if (T_WHITESPACE === $t[0]) {
                    //if the next token is a closing brace, decrease whitespace length
                    if ($n && T_CURLY_BRACE_CLOSE === $n[0]) {
                        $final .= $this->getWhitespaceForDepth($depth - 1);
                    } else {
                        $final .= $this->getWhitespaceForDepth($depth);
                    }
                }
                //check for potential multiline strings, if new lines are present
                elseif (in_array($p[0], array(T_NUM_STRING, T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE, T_STRING_VARNAME, T_STRING))) {
                    $v = str_replace("\r\n", "\n", $p[1]);

                    //if new lines present then do NOT modify, can do unexpected things
                    if (false === strpos($v, "\n")) {
                        $final .= $this->getWhitespaceForDepth($depth).$t[1];
                    } else {
                        $final .= $t[1];
                    }
                }

                //NEW CONDITIONS HERE

                else {
                    $final .= $this->getWhitespaceForDepth($depth).$t[1];
                }
            } else {
                $final .= $t[1];
            }
        }

        return $final;
    }

    protected function getWhitespaceForDepth($depth)
    {
        $whitespace = '';

        for ($i = 0; $i < $depth; $i++) {
            $whitespace .= '    ';
        }

        return $whitespace;
    }

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'alignment';
    }

    public function getDescription()
    {
        return 'Class, method, function and control statement bodies should be consistently aligned.';
    }
}
