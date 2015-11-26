<?php

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class PrintEchoParenthesesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind([T_PRINT, T_ECHO])) {
                continue;
            }

            $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
            if (null === $nextTokenIndex || !$tokens[$nextTokenIndex]->equals('(')) {
                continue;
            }

            $blockEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextTokenIndex);
            $blockEndNextIndex = $tokens->getNextMeaningfulToken($blockEndIndex);

            if (!$tokens[$blockEndNextIndex]->equalsAny(array(';', array(T_CLOSE_TAG)))) {
                continue;
            }

            $tokens[$nextTokenIndex]->clear();
            $tokens[$blockEndIndex]->clear();
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes parentheses around echo and print calls.';
    }
}
