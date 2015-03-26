<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
class SingleQuoteFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $content = $token->getContent();
            if (
                '"' === $content[0] &&
                false === strpos($content, "'") &&
                false === strpos($content, '\\')
            ) {
                $token->setContent("'".substr($content, 1, -1)."'");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Convert double quotes to single quotes for simple strings.';
    }
}
