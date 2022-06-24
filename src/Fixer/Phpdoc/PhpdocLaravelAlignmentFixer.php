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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * @author Julius Kiekbusch <jubeki99@gmail.com>
 */
class PhpLaraveldocAlignmentFixer implements FixerInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Laravel/laravel_phpdoc_alignment';
    }

    /**
     * @inheritdoc
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_DOC_COMMENT]);
    }

    /**
     * @inheritdoc
     */
    public function isRisky(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index > 0; $index--) {
            if (! $tokens[$index]->isGivenKind([\T_DOC_COMMENT])) {
                continue;
            }

            $newContent = preg_replace_callback(
                '/(?P<tag>@param)\s+(?P<hint>(?:'.TypeExpression::REGEX_TYPES.')?)\s+(?P<var>(?:&|\.{3})?\$\S+)/ux',
                fn ($matches) => $matches['tag'].'  '.$matches['hint'].'  '.$matches['var'],
                $tokens[$index]->getContent()
            );

            if ($newContent == $tokens[$index]->getContent()) {
                continue;
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
        }
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('@param and type definition must be followed by two spaces.', []);
    }

    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        return -42;
    }

    /**
     * @inheritdoc
     */
    public function supports(SplFileInfo $file): bool
    {
        return true;
    }
}
