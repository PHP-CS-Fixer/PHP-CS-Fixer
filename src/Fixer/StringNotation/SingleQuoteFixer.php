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

namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class SingleQuoteFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Convert double quotes to single quotes for simple strings.',
            [new CodeSample("<?php \$a = \"sample\";\n")]
        );
    }

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
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
                $content = str_replace(['\\"', '\\$'], ['"', '$'], $content);
                $tokens[$index] = new Token([T_CONSTANT_ENCAPSED_STRING, '\''.$content.'\'']);
            }
        }
    }
}
