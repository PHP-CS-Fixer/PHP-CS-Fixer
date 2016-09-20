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
     * @var array
     */
    private $rules;

    /**
     * @param string $phpVersion
     * @param string $fixerVersion
     * @param array  $rules
     */
    public function __construct($phpVersion, $fixerVersion, array $rules)
    {
        $this->phpVersion = $phpVersion;
        $this->fixerVersion = $fixerVersion;
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

    public function getRules()
    {
        return $this->rules;
    }

    public function equals(SignatureInterface $signature)
    {
        if (
            $this->phpVersion !== $signature->getPhpVersion()
            || $this->fixerVersion !== $signature->getFixerVersion()
            || $this->rules !== $signature->getRules()
        ) {
            return false;
        }

        return true;
    }
}
