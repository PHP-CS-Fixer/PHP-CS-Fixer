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

namespace PhpCsFixer\Cache;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class Signature implements SignatureInterface
{
    private string $phpVersion;

    private string $fixerVersion;

    private string $indent;

    private string $lineEnding;

    /**
     * @var array<string, array<string, mixed>|bool>
     */
    private array $rules;

    private string $ruleCustomisationPolicyVersion;

    /**
     * @param array<string, array<string, mixed>|bool> $rules
     */
    public function __construct(string $phpVersion, string $fixerVersion, string $indent, string $lineEnding, array $rules, string $ruleCustomisationPolicyVersion)
    {
        $this->phpVersion = $phpVersion;
        $this->fixerVersion = $fixerVersion;
        $this->indent = $indent;
        $this->lineEnding = $lineEnding;
        $this->rules = self::makeJsonEncodable($rules);
        $this->ruleCustomisationPolicyVersion = $ruleCustomisationPolicyVersion;
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

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getRuleCustomisationPolicyVersion(): string
    {
        return $this->ruleCustomisationPolicyVersion;
    }

    public function equals(SignatureInterface $signature): bool
    {
        return $this->phpVersion === $signature->getPhpVersion()
            && $this->fixerVersion === $signature->getFixerVersion()
            && $this->indent === $signature->getIndent()
            && $this->lineEnding === $signature->getLineEnding()
            && $this->rules === $signature->getRules()
            && $this->ruleCustomisationPolicyVersion === $signature->getRuleCustomisationPolicyVersion();
    }

    /**
     * @param array<string, array<string, mixed>|bool> $data
     *
     * @return array<string, array<string, mixed>|bool>
     */
    private static function makeJsonEncodable(array $data): array
    {
        array_walk_recursive($data, static function (&$item): void {
            if (\is_string($item) && false === mb_detect_encoding($item, 'utf-8', true)) {
                $item = base64_encode($item);
            }
        });

        return $data;
    }
}
