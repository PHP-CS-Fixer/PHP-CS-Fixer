<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LowercaseConstantsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isNativeConstant()) {
                continue;
            }

            if (
                $this->isNeighbourAccepted($tokens, $tokens->getPrevNonWhitespace($index))
                &&
                $this->isNeighbourAccepted($tokens, $tokens->getNextNonWhitespace($index))
            ) {
                $token->setContent(strtolower($token->getContent()));
            }
        }

        return $tokens->generateCode();
    }

    private function isNeighbourAccepted(Tokens $tokens, $index)
    {
        static $forbiddenTokens = null;

        if (null === $forbiddenTokens) {
            $forbiddenTokens = array(
                T_AS,
                T_CLASS,
                T_CONST,
                T_EXTENDS,
                T_IMPLEMENTS,
                T_INSTANCEOF,
                T_INTERFACE,
                T_NEW,
                T_NS_SEPARATOR,
                T_PAAMAYIM_NEKUDOTAYIM,
                T_USE,
            );

            if (defined('T_TRAIT')) {
                $forbiddenTokens[] = T_TRAIT;
            }

            if (defined('T_INSTEADOF')) {
                $forbiddenTokens[] = T_INSTEADOF;
            }
        }

        if (null === $index) {
            return true;
        }

        $token = $tokens[$index];

        if ($token->equalsAny(array('{', '}'))) {
            return false;
        }

        return !$token->isGivenKind($forbiddenTokens);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The PHP constants true, false, and null MUST be in lower case.';
    }
}
