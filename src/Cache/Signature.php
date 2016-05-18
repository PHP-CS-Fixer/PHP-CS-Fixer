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
    private $phpVersion;

    /**
     * @var string
     */
    private $fixerVersion;

    /**
     * @var bool
     */
    private $isLintingEnabled;

    /**
     * @var array
     */
    private $rules;

    /**
     * @param string $phpVersion
     * @param string $fixerVersion
     * @param bool   $isLintingEnabled
     * @param array  $rules
     */
    public function __construct($phpVersion, $fixerVersion, $isLintingEnabled, array $rules)
    {
        $this->phpVersion = $phpVersion;
        $this->fixerVersion = $fixerVersion;
        $this->isLintingEnabled = $isLintingEnabled;
        $this->rules = $rules;
    }

    public function getPhpVersion()
    {
        return $this->phpVersion;
    }

    public function getFixerVersion()
    {
        return $this->fixerVersion;
    }

    public function isLintingEnabled()
    {
        return $this->isLintingEnabled;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function equals(SignatureInterface $signature)
    {
        if (
            $this->phpVersion !== $signature->getPhpVersion()
            || $this->fixerVersion !== $signature->getFixerVersion()
            || $this->isLintingEnabled !== $signature->isLintingEnabled()
            || $this->rules !== $signature->getRules()
        ) {
            return false;
        }

        return true;
    }
}
