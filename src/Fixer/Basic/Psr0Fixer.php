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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Bram Gotink <bram@gotink.me>
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @deprecated
 */
final class Psr0Fixer extends AbstractProxyFixer implements ConfigurationDefinitionFixerInterface, DeprecatedFixerInterface
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
            'Classes must be in a path that matches their namespace, be at least one namespace deep and the class name should match the file name.',
            $definition->getCodeSamples(),
            $definition->getDescription(),
            $definition->getRiskyDescription()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $this->fixer->configure($configuration);
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
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('dir', 'The directory where the project code is placed.'))
                ->setAllowedTypes(['string'])
                ->setDefault('')
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        return [$this->fixer];
    }
}
