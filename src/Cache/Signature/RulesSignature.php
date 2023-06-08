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

final class RulesSignature
{
    /**
     * @var array<string, FixerSignature>
     */
    private array $fixerSignatures = [];
    private string $hash;

    public function __construct(FixerSignature ...$fixerSignatures)
    {
        foreach ($fixerSignatures as $signature) {
            if (isset($this->fixerSignatures[$signature->getName()])) {
                throw new \UnexpectedValueException(sprintf(
                    'Fixer signature for "%s" is already registered.',
                    $signature->getName()
                ));
            }

            $this->fixerSignatures[$signature->getName()] = $signature;
        }

        ksort($this->fixerSignatures);
        $this->hash = md5(json_encode(array_map(
            static fn (FixerSignature $signature) => [
                'hash' => $signature->getContentHash(),
                'config' => $signature->getConfig(),
            ],
            $this->fixerSignatures
        )));
    }

    public function equals(self $signature): bool
    {
        return $this->hash === $signature->hash;
    }

    /**
     * @return array<string, FixerSignature>
     */
    public function getFixerSignatures(): array
    {
        return $this->fixerSignatures;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
