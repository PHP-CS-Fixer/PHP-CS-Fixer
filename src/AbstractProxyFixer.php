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

namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractProxyFixer extends AbstractFixer
{
    /**
     * @var FixerInterface
     */
    protected $proxyFixer;

    public function __construct()
    {
        parent::__construct();

        $this->proxyFixer = $this->createProxyFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $this->proxyFixer->isCandidate($tokens);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->proxyFixer->getPriority();
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->proxyFixer->fix($file, $tokens);
    }

    /**
     * @return FixerInterface
     */
    abstract protected function createProxyFixer();
}
