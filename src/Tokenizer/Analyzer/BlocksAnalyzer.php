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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class BlocksAnalyzer
{
    public function isBlock(Tokens $tokens, ?int $openIndex, ?int $closeIndex): bool
    {
        if (null === $openIndex || null === $closeIndex) {
            throw new \InvalidArgumentException('Token index not provided');
        }

        if (!$tokens->offsetExists($openIndex)) {
            throw new \InvalidArgumentException(sprintf('Token open index %d does not exist.', $openIndex));
        }

        if (!$tokens->offsetExists($closeIndex)) {
            throw new \InvalidArgumentException(sprintf('Token close index %d does not exist.', $closeIndex));
        }

        $blockType = $this->getBlockType($tokens[$openIndex]);

        if (null === $blockType) {
            return false;
        }

        return $closeIndex === $tokens->findBlockEnd($blockType, $openIndex);
    }

    /**
     * @return Tokens::BLOCK_TYPE_*
     */
    private function getBlockType(Token $token): ?int
    {
        foreach (Tokens::getBlockEdgeDefinitions() as $blockType => $definition) {
            if ($token->equals($definition['start'])) {
                return $blockType;
            }
        }

        return null;
    }
}
