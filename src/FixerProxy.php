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
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class FixerProxy implements FixerInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var FixerInterface
     */
    private $fixer;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return FixerInterface
     */
    public function retrieveFixer()
    {
        if (null === $this->fixer) {
            $name = $this->name;
            \set_error_handler(function () { return false; }, E_USER_DEPRECATED);
            $this->fixer = new $name();
            \restore_error_handler();
        }

        return $this->fixer;
    }

    public function isCandidate(Tokens $tokens)
    {
        return $this->retrieveFixer()->isCandidate($tokens);
    }

    public function isRisky()
    {
        return $this->retrieveFixer()->isRisky();
    }

    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        return $this->retrieveFixer()->fix($file, $tokens);
    }

    public function getName()
    {
        return $this->retrieveFixer()->getName();
    }

    public function getPriority()
    {
        return $this->retrieveFixer()->getPriority();
    }

    public function supports(\SplFileInfo $file)
    {
        return $this->retrieveFixer()->supports($file);
    }
}
