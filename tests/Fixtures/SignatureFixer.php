<?php

declare(strict_types=1);

namespace PhpCsFixer\Tests\Fixtures;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

final class SignatureFixer implements FixerInterface
{
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Signature Fixer', []);
    }

    public function getName(): string
    {
        return 'signature_fixer';
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(\SplFileInfo $file): bool
    {
        return true;
    }
}
