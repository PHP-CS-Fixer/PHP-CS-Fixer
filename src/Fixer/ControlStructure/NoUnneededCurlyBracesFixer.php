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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * @deprecated
 */
final class NoUnneededCurlyBracesFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, DeprecatedFixerInterface
{
    private NoUnneededBracesFixer $noUnneededBracesFixer;

    public function __construct()
    {
        $this->noUnneededBracesFixer = new NoUnneededBracesFixer();

        parent::__construct();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $fixerDefinition = $this->noUnneededBracesFixer->getDefinition();

        return new FixerDefinition(
            'Removes unneeded curly braces that are superfluous and aren\'t part of a control structure\'s body.',
            $fixerDefinition->getCodeSamples(),
            $fixerDefinition->getDescription(),
            $fixerDefinition->getRiskyDescription()
        );
    }

    public function configure(array $configuration): void
    {
        $this->noUnneededBracesFixer->configure($configuration);

        parent::configure($configuration);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoUselessElseFixer, NoUselessReturnFixer, ReturnAssignmentFixer, SimplifiedIfReturnFixer.
     */
    public function getPriority(): int
    {
        return $this->noUnneededBracesFixer->getPriority();
    }

    public function getSuccessorsNames(): array
    {
        return [
            $this->noUnneededBracesFixer->getName(),
        ];
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('namespaces', 'Remove unneeded curly braces from bracketed namespaces.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function createProxyFixers(): array
    {
        return [
            $this->noUnneededBracesFixer,
        ];
    }
}
