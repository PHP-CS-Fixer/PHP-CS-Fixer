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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class ClassDefinitionWhitespacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->getSize() - 1; $index > 0;--$index) {
            if (!$tokens[$index]->isClassy()) {
                continue;
            }
            $classyStart = $tokens->getNextTokenOfKind($index, array('{'));
            for ($j = $index + 1; $j < $classyStart; ++$j) {
                if ($tokens[$j]->isWhitespace()) {
                    $content = $tokens[$j]->getContent();
                    $breakAt = strpos($content, "\n");
                    if (false === $breakAt) {
                        if ($tokens[$j + 1]->equals(',')) {
                            $tokens[$j]->clear();
                        } elseif (' ' !== $content) {
                            $tokens[$j]->setContent(' ');
                        }
                    } else {
                        $tokens[$j]->setContent(substr($content, $breakAt));
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'White space around the key words of a class, trait or interfaces definition should be one space.';
    }
}
