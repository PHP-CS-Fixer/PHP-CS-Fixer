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
 * @author Denis Platov <d.platov@owox.com>
 */
final class ControlStructuresParenthesesWhitespacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $tokens_structure_elements = array(
            T_IF,
            T_ELSEIF,
            T_FOR,
            T_FOREACH,
            T_WHILE,
            T_DO,
        );

        /*
         * @var Token[] $structure_elements
         */
        foreach ($tokens_structure_elements as $token_structure_element) {
            foreach ($tokens->findGivenKind($token_structure_element) as $index => $structure_element) {
                $tokens->ensureWhitespaceAtIndex($index + 1, 0, ' ');

                $curly_brace_index = $tokens->getNextTokenOfKind($index, array('{'));

                if ($curly_brace_index) {
                    $tokens->ensureWhitespaceAtIndex($curly_brace_index - 1, 1, ' ');
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
        return 'Add whitespace around parentheses in declaration of structure elements like if, for, etc.';
    }
}
