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
final class NoUselessCommentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There must be no comment like "Class Foo".',
            [
                new CodeSample('<?php
/**
 * Class Foo
 * Class to do something
 */
class Foo {}
'),
                new CodeSample('<?php
class Foo {
    /**
     * Get bar
     */
    function getBar() {
        return "bar";
    }
}
'),
            ]
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

            $nextIndex = $tokens->getTokenNotOfKindSibling(
                $index,
                1,
                [[T_WHITESPACE], [T_COMMENT], [T_DOC_COMMENT], [T_ABSTRACT], [T_FINAL], [T_PUBLIC], [T_PROTECTED], [T_PRIVATE], [T_STATIC]]
            );
            if (null === $nextIndex) {
                continue;
            }

            if ($tokens[$nextIndex]->isGivenKind([T_CLASS, T_INTERFACE, T_TRAIT])) {
                $newContent = Preg::replace(
                    '/((^|\R)\h*(#|\/*\**))\h*\b(Class|Interface|Trait)\h+[A-Za-z0-9\\\\_]+\.?(\h*\R\h*\*?|\h*$)/i',
                    '$1',
                    $token->getContent()
                );
            } elseif ($tokens[$nextIndex]->isGivenKind(T_FUNCTION)) {
                $newContent = Preg::replace(
                    '/((^|\R)\h*(#|\/*\**))\h*\b((Gets?|SetS?)\h+[A-Za-z0-9\\\\_]+|[A-Za-z0-9\\\\_]+\h+constructor)\.?(\h*\R\h*\*?|\h*$)/i',
                    '$1',
                    $token->getContent()
                );
            } else {
                continue;
            }

            if ($newContent === $token->getContent()) {
                continue;
            }

            $tokens[$index] = new Token([$token->getId(), $newContent]);
        }
    }
}
