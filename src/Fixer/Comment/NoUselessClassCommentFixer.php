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
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class NoUselessClassCommentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There must be no comment like "Class Foo".',
            [new CodeSample('<?php
/**
 * Class Foo
 * Class to do something
 */
class FooBar {}
')]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_COMMENT, T_DOC_COMMENT]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must be run before NoEmptyCommentFixer, NoEmptyPhpdocFixer, PhpdocTrimConsecutiveBlankLineSeparationFixer and PhpdocTrimFixer
        return 6;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind([T_COMMENT, T_DOC_COMMENT])) {
                continue;
            }

            if (false === \strpos($token->getContent(), 'Class ')) {
                continue;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($index);
            if (null === $nextIndex || !$tokens[$nextIndex]->isGivenKind([T_ABSTRACT, T_CLASS, T_FINAL])) {
                continue;
            }

            $newContent = Preg::replace(
                '/(\*)?\h*\bClass\h+[A-Za-z0-1\\\\_]+\.?(\h*\R\h*|\h*$)/i',
                '',
                $token->getContent()
            );

            if ($newContent === $token->getContent()) {
                continue;
            }

            $tokens[$index] = new Token([$token->getId(), $newContent]);
        }
    }
}
