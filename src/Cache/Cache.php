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

    public function signature()
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
            throw new \InvalidArgumentException('Value needs to be an integer');
        }

        $this->hashes[$file] = $hash;
    }

    public function clear($file)
    {
        unset($this->hashes[$file]);
    }

    public function serialize()
    {
        return serialize(array(
            'php' => $this->signature()->php(),
            'version' => $this->signature()->version(),
            'linting' => $this->signature()->linting(),
            'rules' => $this->signature()->rules(),
            'hashes' => $this->hashes,
        ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->signature = new Signature(
            $data['php'],
            $data['version'],
            $data['linting'],
            $data['rules']
        );

        $this->hashes = $data['hashes'];
    }
}
