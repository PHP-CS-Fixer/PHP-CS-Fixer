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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Andrew Kovalyov <andrew.kovalyoff@gmail.com>
 */
final class RemoveVarDumpFixer extends AbstractFixer
{
    /**
     * @var array
     */
    private $functions = array('dump', 'var_dump');

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

        foreach ($this->functions as $function) {
            $currIndex = 0;
            while (null !== $currIndex) {
                $matches = $tokens->findSequence(array(array(T_STRING, $function), '('), $currIndex, $end, false);

                if (null === $matches) {
                    break;
                }
                $match = array_keys($matches);

                $funcStart = $tokens->getPrevNonWhitespace($match[0]);

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
