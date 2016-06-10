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
final class SingleLineFunctionDeclarationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    final public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_FUNCTION) as $index => $token) {
            $beginIndex = $this->getBeginClassIndex($tokens, $index);
            $endDeclarationIndex = $tokens->getNextTokenOfKind($beginIndex, array('{', ';'));
            $endIndex = $tokens[$endDeclarationIndex]->equals(';')
                ? $endDeclarationIndex
                : $tokens->getPrevMeaningfulToken($endDeclarationIndex);

            $this->inlineTokens(
                Tokens::fromArray(array_slice($tokens->toArray(), $beginIndex, $endIndex - $beginIndex + 1))
            );
        }

        return $tokens->generateCode();
    }

    private function getBeginClassIndex(Tokens $tokens, $index)
    {
        $validKinds = array(T_FINAL, T_ABSTRACT, T_PUBLIC, T_PROTECTED, T_PRIVATE);
        if ($tokens->getPrevMeaningfulToken($index)
            && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind($validKinds)) {
            // Go back while one of the valid kinds is present.
            // Have to do this for code like `final public function(...)` (should go back on final key).
            do {
                $index = $tokens->getPrevMeaningfulToken($index);
            } while ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind($validKinds));

            return $index;
        }

        return $index;
    }

    private function inlineTokens(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_WHITESPACE) as $index => $token) {
            // Do not put space at all between parenthesis.
            if (isset($tokens[$index - 1]) && $tokens[$index - 1]->equals('(')
                || isset($tokens[$index + 1]) && $tokens[$index + 1]->equalsAny(array(')', ';'))) {
                $tokens[$index]->clear();
                continue;
            }
            $tokens[$index]->setContent(' ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Declare all the arguments on the same line as the method/function name, no matter how many arguments there are.';
    }
}
