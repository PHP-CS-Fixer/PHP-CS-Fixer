<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class PhpClosingTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if (!$tokens->isMonolithicPhp()) {
            return $content;
        }

        $closeTags = $tokens->findGivenKind(T_CLOSE_TAG);

        if (empty($closeTags)) {
            return $content;
        }

        list($index, $token) = each($closeTags);

        $tokens->removeLeadingWhitespace($index);
        $token->clear();

        $prevIndex = $tokens->getPrevNonWhitespace($index);
        $prevToken = $tokens[$prevIndex];

        if (!$prevToken->equalsAny(array(';', '}'))) {
            $tokens->insertAt($prevIndex + 1, new Token(';'));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The closing ?> tag MUST be omitted from files containing only PHP.';
    }
}
