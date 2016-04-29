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
final class Signature implements SignatureInterface
{
    /**
     * @var string
     */
    private $php;

    /**
     * @var string
     */
    private $version;

    /**
     * @var bool
     */
    private $linting;

    /**
     * @var array
     */
    private $rules;

    /**
     * @param string $php
     * @param string $version
     * @param bool   $linting
     * @param array  $rules
     */
    public function __construct($php, $version, $linting, array $rules)
    {
        $this->php = $php;
        $this->version = $version;
        $this->linting = $linting;
        $this->rules = $rules;
    }

    public function php()
    {
        return $this->php;
    }

    public function version()
    {
        return $this->version;
    }

    public function linting()
    {
        return $this->linting;
    }

    public function rules()
    {
        return $this->rules;
    }

    public function equals(SignatureInterface $signature)
    {
        if (
            $this->php !== $signature->php()
            || $this->version !== $signature->version()
            || $this->linting !== $signature->linting()
            || $this->rules !== $signature->rules()
        ) {
            return false;
        }

        return true;
    }
}
