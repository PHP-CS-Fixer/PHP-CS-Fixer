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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class DeclareEqualNormalizeFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $count = $tokens->count();

        for ($index = 0; $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_DECLARE)) {
                continue;
            }

            while (!$tokens[++$index]->equals('='));
            $tokens->removeLeadingWhitespace($index);
            $tokens->removeTrailingWhitespace($index);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Equal sign in declare statement should not be surrounded by spaces.';
    }
}
