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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jefersson Nathan <malukenho.dev@gmail.com>
 */
final class PhpUnitSizeClassFixer extends AbstractPhpUnitFixer implements WhitespacesAwareFixerInterface, ConfigurableFixerInterface
{
    private const SIZES = ['small', 'medium', 'large'];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'All PHPUnit test cases should have `@small`, `@medium` or `@large` annotation to enable run time limits.',
            [
                new CodeSample("<?php\nclass MyTest extends TestCase {}\n"),
                new CodeSample("<?php\nclass MyTest extends TestCase {}\n", ['group' => 'medium']),
            ],
            'The special groups [small, medium, large] provides a way to identify tests that are taking long to be executed.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocSeparationFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('group', 'Define a specific group to be used in case no group is already in use.'))
                ->setAllowedValues(self::SIZES)
                ->setDefault('small')
                ->getOption(),
        ]);
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $classIndex = $tokens->getPrevTokenOfKind($startIndex, [[T_CLASS]]);

        if ($this->isAbstractClass($tokens, $classIndex)) {
            return;
        }

        $this->ensureIsDockBlockWithAnnotation(
            $tokens,
            $classIndex,
            $this->configuration['group'],
            self::SIZES
        );
    }

    private function isAbstractClass(Tokens $tokens, int $i): bool
    {
        $typeIndex = $tokens->getPrevMeaningfulToken($i);

        return $tokens[$typeIndex]->isGivenKind(T_ABSTRACT);
    }
}
