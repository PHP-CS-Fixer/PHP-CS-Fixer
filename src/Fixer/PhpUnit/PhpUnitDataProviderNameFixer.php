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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\DataProviderAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class PhpUnitDataProviderNameFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Data provider names used only once must match the name of the test.',
            [
                new CodeSample(
                    '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider dataProvider
     */
    public function testSomething($expected, $actual) {}
    public function dataProvider() {}
}
',
                ),
                new CodeSample(
                    '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider dt_prvdr_ftr
     */
    public function test_feature($expected, $actual) {}
    public function dt_prvdr_ftr() {}
}
',
                    [
                        'prefix' => 'data_',
                        'suffix' => '',
                    ]
                ),
            ],
            null,
            'Fixer could be risky if one is calling data provider by name as function.'
        );
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('prefix', 'Prefix that replaces "test".'))
                ->setAllowedTypes(['string'])
                ->setDefault('provide')
                ->getOption(),
            (new FixerOptionBuilder('suffix', 'Suffix to be present at the end.'))
                ->setAllowedTypes(['string'])
                ->setDefault('Cases')
                ->getOption(),
        ]);
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $dataProviderAnalyzer = new DataProviderAnalyzer();
        foreach ($dataProviderAnalyzer->getDataProviders($tokens, $startIndex, $endIndex) as $dataProviderAnalysis) {
            if (\count($dataProviderAnalysis->getUsageIndices()) > 1) {
                continue;
            }

            $usageIndex = $dataProviderAnalysis->getUsageIndices()[0];

            $testNameIndex = $tokens->getNextTokenOfKind($usageIndex, [[T_STRING]]);
            \assert(\is_int($testNameIndex));

            $dataProviderNewName = $this->getProviderNameForTestName($tokens[$testNameIndex]->getContent());
            if (null !== $tokens->findSequence([[T_FUNCTION], [T_STRING, $dataProviderNewName]], $startIndex, $endIndex)) {
                continue;
            }

            $tokens[$dataProviderAnalysis->getNameIndex()] = new Token([T_STRING, $dataProviderNewName]);

            $newCommentContent = Preg::replace(
                sprintf('/(@dataProvider\s+)%s/', $dataProviderAnalysis->getName()),
                sprintf('$1%s', $dataProviderNewName),
                $tokens[$usageIndex]->getContent(),
            );

            $tokens[$usageIndex] = new Token([T_DOC_COMMENT, $newCommentContent]);
        }
    }

    private function getProviderNameForTestName(string $name): string
    {
        $name = Preg::replace('/^test_*/i', '', $name);

        if ('' === $this->configuration['prefix']) {
            $name = lcfirst($name);
        } elseif ('_' !== substr($this->configuration['prefix'], -1)) {
            $name = ucfirst($name);
        }

        return $this->configuration['prefix'].$name.$this->configuration['suffix'];
    }
}
