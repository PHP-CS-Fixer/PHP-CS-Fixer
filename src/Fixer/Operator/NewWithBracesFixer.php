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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @deprecated
 */
final class NewWithBracesFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, DeprecatedFixerInterface
{
    private NewWithParenthesesFixer $newWithParenthesesFixer;

    public function __construct()
    {
        $this->newWithParenthesesFixer = new NewWithParenthesesFixer();

        parent::__construct();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $fixerDefinition = $this->newWithParenthesesFixer->getDefinition();

        return new FixerDefinition(
            'All instances created with `new` keyword must (not) be followed by braces.',
            $fixerDefinition->getCodeSamples(),
            $fixerDefinition->getDescription(),
            $fixerDefinition->getRiskyDescription(),
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ClassDefinitionFixer.
     */
    public function getPriority(): int
    {
        return $this->newWithParenthesesFixer->getPriority();
    }

    public function configure(array $configuration): void
    {
        $this->newWithParenthesesFixer->configure($configuration);

        parent::configure($configuration);
    }

    public function getSuccessorsNames(): array
    {
        return [
            $this->newWithParenthesesFixer->getName(),
        ];
    }

    protected function createProxyFixers(): array
    {
        return [
            $this->newWithParenthesesFixer,
        ];
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return $this->newWithParenthesesFixer->createConfigurationDefinition();
    }
}
