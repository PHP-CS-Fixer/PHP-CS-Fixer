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
final class SingleQuoteFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

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
                // regex: odd number of backslashes, not followed by double quote or dollar
                !preg_match('/(?<!\\\\)(?:\\\\{2})*\\\\(?!["$\\\\])/', $content)
            ) {
                $content = substr($content, 1, -1);
                $content = str_replace('\\"', '"', $content);
                $content = str_replace('\\$', '$', $content);
                $token->setContent('\''.$content.'\'');
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
