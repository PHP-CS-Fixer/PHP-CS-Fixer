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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class NoUnneededCurlyBracesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Removes unneeded curly braces that are superfluous and aren\'t part of a control structure\'s body.',
            [
                new CodeSample(
                    '<?php {
    echo 1;
}

switch ($b) {
    case 1: {
        break;
    }
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must be run before NoUselessElseFixer and NoUselessReturnFixer.
        return 26;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('}');
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($this->findCurlyBraceOpen($tokens) as $index) {
            if ($this->isOverComplete($tokens, $index)) {
                $this->clearOverCompleteBraces($tokens, $index, $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index));
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $openIndex  index of `{` token
     * @param int    $closeIndex index of `}` token
     */
    private function clearOverCompleteBraces(Tokens $tokens, $openIndex, $closeIndex)
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($closeIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($openIndex);
    }

    private function findCurlyBraceOpen(Tokens $tokens)
    {
        for ($i = count($tokens) - 1; $i > 0; --$i) {
            if ($tokens[$i]->equals('{')) {
                yield $i;
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  index of `{` token
     *
     * @return bool
     */
    private function isOverComplete(Tokens $tokens, $index)
    {
        static $whiteList = ['{', '}', [T_OPEN_TAG], ':', ';'];

        return $tokens[$tokens->getPrevMeaningfulToken($index)]->equalsAny($whiteList);
    }
}
