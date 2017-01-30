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

namespace PhpCsFixer\Fixer\PhpTag;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
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
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $i = count($tokens);

        while ($i--) {
            $token = $tokens[$i];

            if (
                !$token->isGivenKind(T_OPEN_TAG_WITH_ECHO)
                && !(
                    /*
                     * HHVM parses '<?=' as T_ECHO instead of T_OPEN_TAG_WITH_ECHO
                     *
                     * @see https://github.com/facebook/hhvm/issues/4809
                     * @see https://github.com/facebook/hhvm/issues/7161
                     */
                    defined('HHVM_VERSION')
                    && $token->equals(array(T_ECHO, '<?='))
                )
            ) {
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replace short-echo `<?=` with long format `<?php echo` syntax.',
            array(new VersionSpecificCodeSample('<?= "foo";', new VersionSpecification(50400)))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_OPEN_TAG_WITH_ECHO)
        /*
         * HHVM parses '<?=' as T_ECHO instead of T_OPEN_TAG_WITH_ECHO
         *
         * @see https://github.com/facebook/hhvm/issues/4809
         * @see https://github.com/facebook/hhvm/issues/7161
         */
        || (
            defined('HHVM_VERSION')
            && $tokens->isTokenKindFound(T_ECHO)
        );
    }
}
