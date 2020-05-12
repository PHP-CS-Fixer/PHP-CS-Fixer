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

namespace PhpCsFixer\Tests\Fixtures;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

final class DeprecatedFixer extends AbstractFixer implements DeprecatedFixerInterface, ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        throw new \LogicException('Not implemented.');
    }

    public function isCandidate(Tokens $tokens): bool
    {
        throw new \LogicException('Not implemented.');
    }

    public function doSomethingWithCreateConfigDefinition(): FixerConfigurationResolver
    {
        return $this->createConfigurationDefinition();
    }

    public function getSuccessorsNames(): array
    {
        return ['testA', 'testB'];
    }

    public function getName(): string
    {
        return 'Vendor4/foo';
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('foo', 'Foo.'))->getOption()
        ]);
    }
}
