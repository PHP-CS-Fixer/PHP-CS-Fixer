<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Marcel Behrmann <marcel@behrmann.dev>
 */
final class PhpdocReadonlyCommentToKeywordFixer extends AbstractFixer
{

    /**
     * {@inheritdoc}
     *
     * Must run before NoExtraBlankLinesFixer, NoTrailingWhitespaceFixer, NoWhitespaceInBlankLineFixer, PhpdocAlignFixer.
     */
    public function getPriority(): int
    {
        return 4;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());

            $annotations = $doc->getAnnotationsOfType('readonly');

            foreach ($annotations as $annotation) {
                $annotation->remove();
            }

            $mainIndex = $index;
            $index = $tokens->getNextMeaningfulToken($index);

            while ($tokens[$index]->isGivenKind([
                T_ABSTRACT,
                T_FINAL,
                T_PRIVATE,
                T_PUBLIC,
                T_PROTECTED,
            ])) {
                $index = $tokens->getNextMeaningfulToken($index);
            }

            if (!$tokens[$index]->isGivenKind(T_CLASS)) {
                continue;
            }

            $tokens->insertAt($index, [new Token([T_READONLY, 'readonly']), new Token([T_WHITESPACE, ' '])]);

            $newContent = $doc->getContent();

            if ($newContent === $token->getContent()) {
                continue;
            }

            if ('' === $newContent) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($mainIndex);
                continue;
            }

            $tokens[$mainIndex] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    public function isCandidate(Tokens $tokens): bool
    {
        if(!defined('T_READONLY')) {
            return false;
        }

        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Converts readonly comment to readonly keyword.',
            [
                new CodeSample(<<<EOT
                    <?php
                    /** @readonly */
                    class C {
                    }\n
                EOT,
                ),
            ]
        );
    }
}
