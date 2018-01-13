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

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class CommentToPhpdocFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // Should be run before PhpdocToCommentFixer
        return 26;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Comments with annotation should be docblock.',
            [
                new CodeSample(
                    "<?php /* @var bool \$isFoo */\n"
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            if (1 === preg_match('~(#|//|/\*+|\R(\s*\*)?)\s*\@[a-zA-Z0-9_\\\\-]+(?=\s|\(|$)~', $token->getContent())) {
                $tokens[$index] = new Token([T_DOC_COMMENT, $this->fixContent($token->getContent())]);
            }
        }
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function fixContent($content)
    {
        if (0 === strpos($content, '#')) {
            $content = substr($content, 1);
        } elseif (0 === strpos($content, '//')) {
            $content = substr($content, 2);
        } else {
            $content = ltrim($content, '/*');
            $content = rtrim($content, '*/');
        }

        if ('' !== trim(substr($content, 0, 1))) {
            $content = ' '.$content;
        }

        if ('' !== trim(substr($content, -1))) {
            $content .= ' ';
        }

        return '/**'.$content.'*/';
    }
}
