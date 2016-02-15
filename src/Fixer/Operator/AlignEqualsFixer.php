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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractAlignFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 */
final class AlignEqualsFixer extends AbstractAlignFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('=');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->deepestLevel = 0;

        // This fixer works partially on Tokens and partially on string representation of code.
        // During the process of fixing internal state of single Token may be affected by injecting ALIGNABLE_PLACEHOLDER to its content.
        // The placeholder will be resolved by `replacePlaceholder` method by removing placeholder or changing it into spaces.
        // That way of fixing the code causes disturbances in marking Token as changed - if code is perfectly valid then placeholder
        // still be injected and removed, which will cause the `changed` flag to be set.
        // To handle that unwanted behavior we work on clone of Tokens collection and then override original collection with fixed collection.
        $tokensClone = clone $tokens;

        $this->injectAlignmentPlaceholders($tokensClone);
        $content = $this->replacePlaceholder($tokensClone);

        $tokens->setCode($content);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Align equals symbols in consecutive lines.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the BinaryOperatorSpacesFixer
        return -10;
    }

    /**
     * Inject into the text placeholders of candidates of vertical alignment.
     *
     * @param Tokens $tokens
     */
    private function injectAlignmentPlaceholders(Tokens $tokens)
    {
        $deepestLevel = 0;
        $limit = $tokens->count();

        for ($index = 0; $index < $limit; ++$index) {
            $token = $tokens[$index];

            if ($token->equals('=')) {
                $token->setContent(sprintf(self::ALIGNABLE_PLACEHOLDER, $deepestLevel).$token->getContent());
                continue;
            }

            if ($token->isGivenKind(T_FUNCTION)) {
                ++$deepestLevel;
                continue;
            }

            if ($token->equals('(')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }

            if ($token->equals('[')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);
                continue;
            }

            if ($token->isGivenKind(CT_ARRAY_SQUARE_BRACE_OPEN)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                continue;
            }
        }

        $this->deepestLevel = $deepestLevel;
    }
}
