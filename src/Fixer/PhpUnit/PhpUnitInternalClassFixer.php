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
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gert de Pagter <BackEndTea@gmail.com>
 */
final class PhpUnitInternalClassFixer extends AbstractPhpUnitFixer implements WhitespacesAwareFixerInterface, ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'All PHPUnit test classes should be marked as internal.',
            [
                new CodeSample("<?php\nclass MyTest extends TestCase {}\n"),
                new CodeSample(
                    "<?php\nclass MyTest extends TestCase {}\nfinal class FinalTest extends TestCase {}\nabstract class AbstractTest extends TestCase {}\n",
                    ['types' => ['final']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before FinalInternalClassFixer, PhpdocSeparationFixer.
     */
    public function getPriority(): int
    {
        return 68;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $types = ['normal', 'final', 'abstract'];

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('types', 'What types of classes to mark as internal.'))
                ->setAllowedValues([new AllowedValueSubset($types)])
                ->setAllowedTypes(['array'])
                ->setDefault(['normal', 'final'])
                ->getOption(),
        ]);
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $classIndex = $tokens->getPrevTokenOfKind($startIndex, [[T_CLASS]]);

        if (!$this->isAllowedByConfiguration($tokens, $classIndex)) {
            return;
        }

        $this->ensureIsDockBlockWithAnnotation(
            $tokens,
            $classIndex,
            'internal',
            ['internal']
        );
    }

    private function isAllowedByConfiguration(Tokens $tokens, int $index): bool
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $modifiers = $tokensAnalyzer->getClassyModifiers($index);

        if (isset($modifiers['final'])) {
            return \in_array('final', $this->configuration['types'], true);
        }

        if (isset($modifiers['abstract'])) {
            return \in_array('abstract', $this->configuration['types'], true);
        }

        return \in_array('normal', $this->configuration['types'], true);
    }
}
