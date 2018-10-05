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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFopenFlagFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class FopenFlagsFixer extends AbstractFopenFlagFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The flags in `fopen` calls must contain `b` and must omit `t`.',
            [new CodeSample("<?php\n\$a = fopen(\$foo, 'rwt');\n")],
            null,
            'Risky when the function `fopen` is overridden.'
        );
    }

    /**
     * @param Tokens $tokens
     * @param int    $argumentStartIndex
     * @param int    $argumentEndIndex
     */
    protected function fixFopenFlagToken(Tokens $tokens, $argumentStartIndex, $argumentEndIndex)
    {
        $argumentFlagIndex = null;

        for ($i = $argumentStartIndex; $i <= $argumentEndIndex; ++$i) {
            if ($tokens[$i]->isGivenKind([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                continue;
            }

            if (null !== $argumentFlagIndex) {
                return; // multiple meaningful tokens found, no candidate for fixing
            }

            $argumentFlagIndex = $i;
        }

        // check if second argument is candidate
        if (null === $argumentFlagIndex || !$tokens[$argumentFlagIndex]->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            return;
        }

        $content = $tokens[$argumentFlagIndex]->getContent();
        $contentQuote = $content[0]; // `'`, `"`, `b` or `B`

        if ('b' === $contentQuote || 'B' === $contentQuote) {
            $binPrefix = $contentQuote;
            $contentQuote = $content[1]; // `'` or `"`
            $mode = substr($content, 2, -1);
        } else {
            $binPrefix = '';
            $mode = substr($content, 1, -1);
        }

        $modeLength = \strlen($mode);
        if ($modeLength < 1 || $modeLength > 13) { // 13 === length 'r+w+a+x+c+etb'
            return; // sanity check to be less risky
        }

        $mode = str_replace('t', '', $mode);

        if (false === strpos($mode, 'b')) {
            $mode .= 'b';
        }

        $newContent = $binPrefix.$contentQuote.$mode.$contentQuote;

        if ($content !== $newContent) {
            $tokens[$argumentFlagIndex] = new Token([T_CONSTANT_ENCAPSED_STRING, $newContent]);
        }
    }
}
