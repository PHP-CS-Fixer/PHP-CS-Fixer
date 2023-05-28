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

namespace PhpCsFixer\Cache\Signature;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class ConfigSignature implements ConfigSignatureInterface
{
    private string $phpVersion;

    private string $fixerVersion;

    private string $indent;

    private string $lineEnding;

    private RulesSignature $rules;

    public function __construct(
        string $phpVersion,
        string $fixerVersion,
        string $indent,
        string $lineEnding,
        RulesSignature $rules
    ) {
        $this->phpVersion = $phpVersion;
        $this->fixerVersion = $fixerVersion;
        $this->indent = $indent;
        $this->lineEnding = $lineEnding;
        $this->rules = $rules;
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function getFixerVersion(): string
    {
        return $this->fixerVersion;
    }

    public function getIndent(): string
    {
        return $this->indent;
    }

    public function getLineEnding(): string
    {
        return $this->lineEnding;
    }

    public function getRulesSignature(): RulesSignature
    {
        return $this->rules;
    }

    /**
     * @return array<string, array{hash: string, config: array<string, mixed>|bool}>
     */
    public function getRules(): array
    {
        return array_map(
            static fn (FixerSignature $signature) => [
                'hash' => $signature->getContentHash(),
                'config' => $signature->getConfig(),
            ],
            $this->rules->getFixerSignatures()
        );
    }

    public function equals(ConfigSignatureInterface $signature): bool
    {
        return $this->phpVersion === $signature->getPhpVersion()
            && $this->fixerVersion === $signature->getFixerVersion()
            && $this->indent === $signature->getIndent()
            && $this->lineEnding === $signature->getLineEnding()
            && $this->rules->equals($signature->getRulesSignature());
    }
}
