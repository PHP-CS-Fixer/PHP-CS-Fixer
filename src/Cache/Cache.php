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

namespace PhpCsFixer\Cache;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class Cache implements CacheInterface
{
    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var array
     */
    private $hashes = array();

    public function __construct(SignatureInterface $signature)
    {
        $this->signature = $signature;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function has($file)
    {
        return array_key_exists($file, $this->hashes);
    }

    public function get($file)
    {
        if (!$this->has($file)) {
            return;
        }

        return $this->hashes[$file];
    }

    public function set($file, $hash)
    {
        if (!is_int($hash)) {
            throw new \InvalidArgumentException(sprintf(
                'Value needs to be an integer, got "%s".',
                is_object($hash) ? get_class($hash) : gettype($hash))
            );
        }

        $this->hashes[$file] = $hash;
    }

    public function clear($file)
    {
        unset($this->hashes[$file]);
    }

    public function toJson()
    {
        $json = json_encode(array(
            'php' => $this->getSignature()->getPhpVersion(),
            'version' => $this->getSignature()->getFixerVersion(),
            'rules' => $this->getSignature()->getRules(),
            'hashes' => $this->hashes,
        ));

        if (false === $json) {
            throw new \UnexpectedValueException(sprintf(
                'Can not encode cache signature to JSON, error: "%s". If you have non-UTF8 chars in your signature, like in license for `header_comment`, consider enabling `ext-mbstring` or install `symfony/polyfill-mbstring`.',
                json_last_error_msg()
            ));
        }

        return $json;
    }

    /**
     * @param string $json
     *
     * @throws \InvalidArgumentException
     *
     * @return Cache
     */
    public static function fromJson($json)
    {
        $data = json_decode($json, true);

        if (null === $data && JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf(
                'Value needs to be a valid JSON string, got "%s", error: "%s".',
                is_object($json) ? get_class($json) : gettype($json),
                json_last_error_msg()
            ));
        }

        $requiredKeys = array(
            'php',
            'version',
            'rules',
            'hashes',
        );

        $missingKeys = array_diff_key(array_flip($requiredKeys), $data);

        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf(
                'JSON data is missing keys "%s"',
                implode('", "', $missingKeys)
            ));
        }

        $signature = new Signature(
            $data['php'],
            $data['version'],
            $data['rules']
        );

        $cache = new self($signature);

        $cache->hashes = $data['hashes'];

        return $cache;
    }
}
