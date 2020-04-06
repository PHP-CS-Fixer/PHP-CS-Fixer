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
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gregor Harlan
 */
final class NoTrailingWhitespaceInStringFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE, T_INLINE_HTML]);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There must be no trailing whitespace in strings.',
            [
                new CodeSample(
                    "<?php \$a = '  \n    foo \n';\n"
                ),
            ],
            null,
            'Changing the whitespaces in strings might affect string comparisons and outputs.'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1, $last = true; $index >= 0; --$index, $last = false) {
            /** @var Token $token */
            $token = $tokens[$index];

            if (!$token->isGivenKind([T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE, T_INLINE_HTML])) {
                continue;
            }

            $isInlineHtml = $token->isGivenKind(T_INLINE_HTML);
            $regex = $isInlineHtml && $last ? '/\h+(?=\R|$)/' : '/\h+(?=\R)/';
            $content = Preg::replace($regex, '', $token->getContent());

            if ($token->getContent() === $content) {
                continue;
            }

            if (!$isInlineHtml || 0 === $index) {
                $this->updateContent($tokens, $index, $content);

                continue;
            }

            $prev = $index - 1;

            if ($tokens[$prev]->equals([T_CLOSE_TAG, '?>']) && Preg::match('/^\R/', $content, $match)) {
                $tokens[$prev] = new Token([T_CLOSE_TAG, $tokens[$prev]->getContent().$match[0]]);
                $content = substr($content, \strlen($match[0]));
                $content = false === $content ? '' : $content;
            }

            $this->updateContent($tokens, $index, $content);
        }
    }

    /**
     * @param int    $index
     * @param string $content
     */
    private function updateContent(Tokens $tokens, $index, $content)
    {
        if ('' === $content) {
            $tokens->clearAt($index);

            return;
        }

        $tokens[$index] = new Token([$tokens[$index]->getId(), $content]);
    }
}
