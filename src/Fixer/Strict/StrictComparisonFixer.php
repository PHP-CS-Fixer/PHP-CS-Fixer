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

namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class StrictComparisonFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        static $map = array(
            T_IS_EQUAL => array(
                'id' => T_IS_IDENTICAL,
                'content' => '===',
            ),
            T_IS_NOT_EQUAL => array(
                'id' => T_IS_NOT_IDENTICAL,
                'content' => '!==',
            ),
        );

        foreach ($tokens as $index => $token) {
            $tokenId = $token->getId();

            if (isset($map[$tokenId])) {
                $tokens->overrideAt($index, array($map[$tokenId]['id'], $map[$tokenId]['content']));
            }
        }
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            'Comparisons should be strict.',
            array(new CodeSample("<?php\n\$a = 1== \$b;")),
            null,
            'Changing comparisons to strict might change code behavior.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_IS_EQUAL, T_IS_NOT_EQUAL));
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }
}
