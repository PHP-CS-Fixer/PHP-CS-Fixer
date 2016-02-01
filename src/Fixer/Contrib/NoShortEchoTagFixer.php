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

namespace PhpCsFixer\Fixer\Contrib;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vincent Klaiber <hello@vinkla.com>
 */
final class NoShortEchoTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_OPEN_TAG_WITH_ECHO);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $i = count($tokens);

        while ($i--) {
            if (!$tokens[$i]->isGivenKind(T_OPEN_TAG_WITH_ECHO)) {
                continue;
            }

            $nextIndex = $i + 1;

            $tokens->overrideAt($i, array(T_OPEN_TAG, '<?php '));

            if (!$tokens[$nextIndex]->isWhitespace()) {
                $tokens->insertAt($nextIndex, new Token(array(T_WHITESPACE, ' ')));
            }

            $tokens->insertAt($nextIndex, new Token(array(T_ECHO, 'echo')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replace short-echo <?= with long format <?php echo syntax.';
    }
}
