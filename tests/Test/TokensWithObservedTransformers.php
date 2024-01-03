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

use PhpCsFixer\AccessibleObject\AccessibleObject;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Transformers;

class TokensWithObservedTransformers extends Tokens
{
    /**
     * @var null|string
     */
    public $currentTransformer;

    /**
     * @var array<string, array<int|string>>
     */
    public $observedModificationsPerTransformer = [];

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
        foreach (AccessibleObject::create($transformers)->items as $transformer) {
            $this->currentTransformer = $transformer->getName();
            $this->observedModificationsPerTransformer[$this->currentTransformer] = [];

            foreach ($this as $index => $token) {
                $transformer->process($this, $token, $index);
            }
        }

        $this->currentTransformer = null;
    }

    /**
     * @param array{int}|string|Token $token token prototype
     *
     * @return int|string
     */
    private function extractTokenKind($token)
    {
        return $token instanceof Token
            ? ($token->isArray() ? $token->getId() : $token->getContent())
            : (\is_array($token) ? $token[0] : $token);
    }
}
