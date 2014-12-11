<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocSeparationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $index => $token) {
            $tokens[$index]->setContent($this->fixDocBlock($token->getContent()));
        }

        return $tokens->generateCode();
    }

    private function fixDocBlock($content)
    {
        $lines = explode("\n", str_replace(array("\r\n", "\r"), "\n", $content));

        $l = count($lines);

        for ($i = 0; $i < $l; ++$i) {
            $line = $lines[$i];
            if ($this->lineIsAnAnnotation($line)) {
                $type = $this->getAnnotationType($line);
                $next = $i + 1;
                while (true) {
                    if ($next > $l - 1) {
                        break;
                    }

                    $nextLine = $lines[$next];

                    if (!$this->lineHasContent($nextLine) || $this->lineIsATerminator($nextLine)) {
                        break;
                    }

                    if ($this->lineIsAnAnnotation($nextLine)) {
                        if ($type !== $this->getAnnotationType($nextLine)) {
                            $new = str_pad("*", strlen($line) - strlen(ltrim($line)) + 1, ' ', STR_PAD_LEFT);
                            array_splice($lines, $next, 0, array($new));
                        }

                        break;
                    }

                    ++$next;
                }
            } elseif ($this->lineHasContent($line)) {
                $next = $i + 1;
                while (true) {
                    if ($next > $l - 1) {
                        break;
                    }

                    $nextLine = $lines[$next];

                    if (!$this->lineHasContent($nextLine) || $this->lineIsATerminator($nextLine)) {
                        break;
                    }

                    if ($this->lineIsAnAnnotation($nextLine)) {
                        $new = str_pad("*", strlen($line) - strlen(ltrim($line)) + 1, ' ', STR_PAD_LEFT);
                        array_splice($lines, $next, 0, array($new));

                        break;
                    }

                    ++$next;
                }

                // update the line count
                $l = count($lines);
            }
        }

        return implode("\n", $lines);
    }

    private function lineHasContent($line)
    {
        return 0 !== preg_match('/\\*\s+\S+/', $line);
    }

    private function lineIsAnAnnotation($line)
    {
        return 0 !== preg_match('/\\*\s+@/', $line);
    }

    private function getAnnotationType($line)
    {
        if (preg_match('/\\*\s+@param/', $line)) {
            return 'param';
        }

        if (preg_match('/\\*\s+@throws/', $line)) {
            return 'throws';
        }

        if (preg_match('/\\*\s+@return/', $line)) {
            return 'return';
        }
    }

    private function lineIsATerminator($line)
    {
        return 0 !== preg_match('/\\*\//', $line);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Annotations in phpdocs should be separated correctly.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the PhpdocParamsFixer
        return 10;
    }
}
