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

use PhpCsFixer\Fixer\FixerInterface;

final class FixerSignature
{
    private string $name;
    private string $contentHash;

    /**
     * @var array<string, mixed>|bool
     */
    private $config;

    private function __construct()
    {
    }

    /**
     * @param array<string, mixed>|bool $config
     */
    public static function fromInstance(FixerInterface $fixer, $config): self
    {
        $signature = new self();
        $signature->name = $fixer->getName();
        $signature->contentHash = md5(file_get_contents((new \ReflectionClass($fixer))->getFileName()));
        $signature->config = \is_array($config) ? self::makeJsonEncodable($config) : $config;

        return $signature;
    }

    /**
     * @param array<string, mixed>|bool $config
     */
    public static function fromRawValues(string $name, string $contentHash, $config): self
    {
        $signature = new self();
        $signature->name = $name;
        $signature->contentHash = $contentHash;
        $signature->config = \is_array($config) ? self::makeJsonEncodable($config) : $config;

        return $signature;
    }

    public function equals(self $signature): bool
    {
        return $this->contentHash === $signature->getContentHash();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContentHash(): string
    {
        return $this->contentHash;
    }

    /**
     * @return array<string, mixed>|bool
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array<string, array<string, mixed>|bool> $data
     *
     * @return array<string, array<string, mixed>|bool>
     */
    private static function makeJsonEncodable(array $data): array
    {
        array_walk_recursive($data, static function (&$item): void {
            if (\is_string($item) && !mb_detect_encoding($item, 'utf-8', true)) {
                $item = base64_encode($item);
            }
        });

        return $data;
    }
}
