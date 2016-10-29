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

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class SingleLineClassDefinitionFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $relevantTokenKinds = array(T_CLASS, T_INTERFACE);
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $relevantTokenKinds[] = T_TRAIT;
        }

        foreach ($tokens->findGivenKind($relevantTokenKinds) as $givenTokens) {
            foreach ($givenTokens as $index => $token) {
                $beginIndex = $this->getBeginClassIndex($tokens, $index);
                $braceIndex = $tokens->getNextTokenOfKind($beginIndex, array('{'));
                $endIndex = $tokens->getPrevMeaningfulToken($braceIndex);

                $this->inlineTokens(
                    Tokens::fromArray(array_slice($tokens->toArray(), $beginIndex, $endIndex - $beginIndex + 1))
                );
            }
        }

        return $tokens->generateCode();
    }

    private function getBeginClassIndex(Tokens $tokens, $index)
    {
        if ($tokens->getPrevMeaningfulToken($index)
            && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(array(T_FINAL, T_ABSTRACT))) {
            return $tokens->getPrevMeaningfulToken($index);
        }

        return $index;
    }

    private function inlineTokens(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_WHITESPACE) as $index => $token) {
            $tokens[$index]->setContent(' ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Declare the class inheritance and all the implemented interfaces on the same line as the class name.';
    }
}
