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

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transforms tokens added in non-lowest supported PHP version to custom tokens.
 *
 * @internal
 */
final class ForwardCompatibilityTransformer extends AbstractTransformer
{
    /** @var array<int, int> */
    private array $map = [];

    public function __construct()
    {
        if (\defined('T_PUBLIC_SET')) { // @TODO: drop condition when PHP 8.4+ is required
            $this->map[T_PUBLIC_SET] = CT::T_PUBLIC_SET;
            $this->map[T_PROTECTED_SET] = CT::T_PROTECTED_SET;
            $this->map[T_PRIVATE_SET] = CT::T_PRIVATE_SET;
        }
    }

    public function getRequiredPhpVersionId(): int
    {
        return 8_04_00;
    }

    public function process(Tokens $tokens, Token $token, int $index): void
    {
        if (!isset($this->map[$token->getId()])) {
            return;
        }

        $tokens[$index] = new Token([
            $this->map[$token->getId()],
            $token->getContent(),
        ]);
    }

    public function getCustomTokens(): array
    {
        return [
            CT::T_PUBLIC_SET,
            CT::T_PROTECTED_SET,
            CT::T_PRIVATE_SET,
        ];
    }
}
