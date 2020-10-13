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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Bram Gotink <bram@gotink.me>
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @deprecated
 */
final class Psr4Fixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    /**
     * @var PsrAutoloadingFixer
     */
    private $fixer;

    public function __construct()
    {
        $this->fixer = new PsrAutoloadingFixer();
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $definition = $this->fixer->getDefinition();

        return new FixerDefinition(
            'Class names should match the file name.',
            \array_slice($definition->getCodeSamples(), 0, 1),
            $definition->getDescription(),
            $definition->getRiskyDescription()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames()
    {
        return [$this->fixer->getName()];
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        return [$this->fixer];
    }
}
