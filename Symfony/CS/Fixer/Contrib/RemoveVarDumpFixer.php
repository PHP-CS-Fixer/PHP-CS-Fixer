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

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Andrew Kovalyov <andrew.kovalyoff@gmail.com>
 */
final class RemoveVarDumpFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        if (!$this->hasDump($content)) {
            return $content;
        }
        $tokens = Tokens::fromCode($content);

        $end = $tokens->count() - 1;

        foreach (['dump', 'var_dump'] as $function) {
            $currIndex = 0;
            while (null !== $currIndex) {
                $match = $tokens->findSequence(array(array(T_STRING, $function), '('), $currIndex, $end, false);

                // did we find a match?
                if (null === $match) {
                    break;
                }

                $match = array_keys($match);

                $funcStart = $tokens->getPrevTokenOfKind($match[1], array(';'));
                $funcEnd = $tokens->getNextTokenOfKind($match[1], array(';'));
                for ($i = $funcStart + 1; $i <= $funcEnd; ++$i) {
                    $tokens[$i]->clear();
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes var_dump/dump occurrences.';
    }

    /**
     * Quick search for dump/var_dump existence.
     *
     * @param string $content
     *
     * @return bool
     */
    private function hasDump($content)
    {
        return false !== stripos($content, 'dump');
    }
}