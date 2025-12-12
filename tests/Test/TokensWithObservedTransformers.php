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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Transformers;

/**
 * @phpstan-import-type _PhpTokenKind from Token
 * @phpstan-import-type _PhpTokenPrototypePartial from Token
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class TokensWithObservedTransformers extends Tokens
{
    public ?string $currentTransformer = null;

    /**
     * @var array<string, list<_PhpTokenKind>>
     */
    public array $observedModificationsPerTransformer = [];

    public function offsetSet($index, $newval): void
    {
        if (null !== $this->currentTransformer) {
            $this->observedModificationsPerTransformer[$this->currentTransformer][] = $this->extractTokenKind($newval);
        }

        parent::offsetSet($index, $newval);
    }

    /**
     * @internal
     */
    protected function applyTransformers(): void
    {
        $this->observedModificationsPerTransformer = [];

        $transformers = Transformers::createSingleton();

        $items = \Closure::bind(
            static fn (Transformers $transformers): array => $transformers->items,
            null,
            Transformers::class
        )($transformers);

        foreach ($items as $transformer) {
            $this->currentTransformer = $transformer->getName();
            $this->observedModificationsPerTransformer[$this->currentTransformer] = [];

            foreach ($this as $index => $token) {
                $transformer->process($this, $token, $index);
            }
        }

        $this->currentTransformer = null;
    }

    /**
     * @param _PhpTokenPrototypePartial|Token $token token prototype
     *
     * @return _PhpTokenKind
     */
    private function extractTokenKind($token)
    {
        return $token instanceof Token
            ? ($token->isArray() ? $token->getId() : $token->getContent())
            : (\is_array($token) ? $token[0] : $token);
    }
}
