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
 * @author Ben Harold <benharold@mac.com>
 */
class NamespaceFollowsOpeningTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        // ignore non-monolithic files
        if (!$tokens->isMonolithicPhp()) {
            return $content;
        }

        // ignore files with short open tag
        if (!$tokens[0]->isGivenKind(T_OPEN_TAG)) {
            return $content;
        }

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_NAMESPACE)) {
                $tokens[0]->setContent('<?php ');
                for ($x = 1; $x <= ($index - 1); ++$x) {
                    $tokens[$x]->setContent('');
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
        return 'There should be one space and no newline between the opening tag and namespace declaration.';
    }
}
