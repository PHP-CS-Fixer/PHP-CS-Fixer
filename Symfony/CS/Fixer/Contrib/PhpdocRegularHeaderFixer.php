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
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Ensures the header of a file is not using phpdoc notation while not containing annotations.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class PhpdocRegularHeaderFixer extends AbstractFixer
{
    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if (!$tokens->isMonolithicPhp()) {
            return $content;
        }
        
        // Find first non-whitespace element and check if it's a docblock
        $index = $tokens->getNextNonWhitespace(0);
        if (null !== $index && $tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
            $token = $tokens[$index];
            $doc = new DocBlock($token->getContent());

            // If there are no annotations change it to a regular comment
            if (0 === count($doc->getAnnotations())) {
                $tokens->overrideAt($index, array(T_COMMENT, '/*'.ltrim($token->getContent(), '/*'), $token->getLine()));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return 'Ensures the header of a file is not using phpdoc notation while not containing annotations.';
    }
}