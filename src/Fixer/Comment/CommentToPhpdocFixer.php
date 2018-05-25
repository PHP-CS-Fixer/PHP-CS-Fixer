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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class CommentToPhpdocFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
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
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // Should be run before all other PHPDoc fixers
        return 26;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Comments with annotation should be docblock when used on structural elements.',
            [new CodeSample("<?php /* header */ \$x = true; /* @var bool \$isFoo */ \$isFoo = true;\n")],
            null,
            'Risky as new docblocks might mean more, e.g. a Doctrine entity might have a new column in database'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $commentsAnalyzer = new CommentsAnalyzer();

        for ($index = 0, $limit = count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            if ($commentsAnalyzer->isHeaderComment($tokens, $index)) {
                continue;
            }

            if (!$commentsAnalyzer->isBeforeStructuralElement($tokens, $index)) {
                continue;
            }

            $commentIndices = $commentsAnalyzer->getCommentBlockIndices($tokens, $index);

            if ($this->isCommentCandidate($tokens, $commentIndices)) {
                $this->fixComment($tokens, $commentIndices);
            }

            $index = max($commentIndices);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int[]  $indices
     *
     * @return bool
     */
    private function isCommentCandidate(Tokens $tokens, array $indices)
    {
        return array_reduce(
            $indices,
            function ($carry, $index) use ($tokens) {
                return $carry || 1 === Preg::match('~(#|//|/\*+|\R(\s*\*)?)\s*\@[a-zA-Z0-9_\\\\-]+(?=\s|\(|$)~', $tokens[$index]->getContent());
            },
            false
        );
    }

    /**
     * @param Tokens $tokens
     * @param int[]  $indices
     */
    private function fixComment(Tokens $tokens, $indices)
    {
        if (1 === count($indices)) {
            $this->fixCommentSingleLine($tokens, reset($indices));
        } else {
            $this->fixCommentMultiLine($tokens, $indices);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixCommentSingleLine(Tokens $tokens, $index)
    {
        $message = $this->getMessage($tokens[$index]->getContent());

        if ('' !== trim(substr($message, 0, 1))) {
            $message = ' '.$message;
        }

        if ('' !== trim(substr($message, -1))) {
            $message .= ' ';
        }

        $tokens[$index] = new Token([T_DOC_COMMENT, '/**'.$message.'*/']);
    }

    /**
     * @param Tokens $tokens
     * @param int[]  $indices
     */
    private function fixCommentMultiLine(Tokens $tokens, array $indices)
    {
        $startIndex = reset($indices);
        $indent = Utils::calculateTrailingWhitespaceIndent($tokens[$startIndex - 1]);

        $content = '/**'.$this->whitespacesConfig->getLineEnding();
        $count = max($indices);

        for ($index = $startIndex; $index <= $count; ++$index) {
            if ($tokens[$index]->isComment()) {
                $content .= $indent.' *'.$this->getMessage($tokens[$index]->getContent()).$this->whitespacesConfig->getLineEnding();
            }
            $tokens->clearAt($index);
        }
        $content .= $indent.' */';

        $tokens->insertAt($startIndex, new Token([T_DOC_COMMENT, $content]));
    }

    private function getMessage($content)
    {
        if (0 === strpos($content, '#')) {
            return substr($content, 1);
        }
        if (0 === strpos($content, '//')) {
            return substr($content, 2);
        }

        return rtrim(ltrim($content, '/*'), '*/');
    }
}
