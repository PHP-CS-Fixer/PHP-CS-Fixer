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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class FixerTest extends TestCase
{
    // do not modify this structure without prior discussion
    private $allowedRequiredOptions = [
        'header_comment' => ['header' => true],
    ];

    // do not modify this structure without prior discussion
    private $allowedFixersWithoutDefaultCodeSample = [
        'general_phpdoc_annotation_remove' => true,
    ];

    /**
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixerDefinitions(FixerInterface $fixer)
    {
        /** @var FixerInterface $fixer */
        $fixerName = $fixer->getName();
        $definition = $fixer->getDefinition();
        $fixerIsConfigurable = $fixer instanceof ConfigurableFixerInterface;

        static::assertRegExp('/^[A-Z`].*\.$/', $definition->getSummary(), sprintf('[%s] Description must start with capital letter or a ` and end with dot.', $fixerName));
        static::assertNotContains('phpdocs', $definition->getSummary(), sprintf('[%s] `PHPDoc` must not be in the plural in description.', $fixerName), true);
        static::assertCorrectCasing($definition->getSummary(), 'PHPDoc', sprintf('[%s] `PHPDoc` must be in correct casing in description.', $fixerName));
        static::assertCorrectCasing($definition->getSummary(), 'PHPUnit', sprintf('[%s] `PHPUnit` must be in correct casing in description.', $fixerName));

        $samples = $definition->getCodeSamples();
        static::assertNotEmpty($samples, sprintf('[%s] Code samples are required.', $fixerName));

        $configSamplesProvided = [];
        $dummyFileInfo = new StdinFileInfo();
        foreach ($samples as $sampleCounter => $sample) {
            static::assertInstanceOf(CodeSampleInterface::class, $sample, sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));
            static::assertInternalType('int', $sampleCounter);

            $code = $sample->getCode();
            static::assertStringIsNotEmpty($code, sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));
            if (!($fixer instanceof SingleBlankLineAtEofFixer)) {
                static::assertSame("\n", substr($code, -1), sprintf('[%s] Sample #%d must end with linebreak', $fixerName, $sampleCounter));
            }

            $config = $sample->getConfiguration();
            if (null !== $config) {
                static::assertTrue($fixerIsConfigurable, sprintf('[%s] Sample #%d has configuration, but the fixer is not configurable.', $fixerName, $sampleCounter));
                static::assertInternalType('array', $config, sprintf('[%s] Sample #%d configuration must be an array or null.', $fixerName, $sampleCounter));

                $configSamplesProvided[$sampleCounter] = $config;
            } elseif ($fixerIsConfigurable) {
                if (!$sample instanceof VersionSpecificCodeSampleInterface) {
                    static::assertArrayNotHasKey('default', $configSamplesProvided, sprintf('[%s] Multiple non-versioned samples with default configuration.', $fixerName));
                }

                $configSamplesProvided['default'] = true;
            }

            if ($sample instanceof VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(\PHP_VERSION_ID)) {
                continue;
            }

            if ($fixerIsConfigurable) {
                // always re-configure as the fixer might have been configured with diff. configuration form previous sample
                $fixer->configure(null === $config ? [] : $config);
            }

            Tokens::clearCache();
            $tokens = Tokens::fromCode($code);
            $fixer->fix(
                $sample instanceof FileSpecificCodeSampleInterface ? $sample->getSplFileInfo() : $dummyFileInfo,
                $tokens
            );

            static::assertTrue($tokens->isChanged(), sprintf('[%s] Sample #%d is not changed during fixing.', $fixerName, $sampleCounter));

            $duplicatedCodeSample = array_search(
                $sample,
                \array_slice($samples, 0, $sampleCounter),
                false
            );

            static::assertFalse(
                $duplicatedCodeSample,
                sprintf('[%s] Sample #%d duplicates #%d.', $fixerName, $sampleCounter, $duplicatedCodeSample)
            );
        }

        if ($fixerIsConfigurable) {
            if (isset($configSamplesProvided['default'])) {
                reset($configSamplesProvided);
                static::assertSame('default', key($configSamplesProvided), sprintf('[%s] First sample must be for the default configuration.', $fixerName));
            } elseif (!isset($this->allowedFixersWithoutDefaultCodeSample[$fixerName])) {
                static::assertArrayHasKey($fixerName, $this->allowedRequiredOptions, sprintf('[%s] Has no sample for default configuration.', $fixerName));
            }

            $options = $fixer->getConfigurationDefinition()->getOptions();

            foreach ($options as $option) {
                static::assertRegExp('/^[a-z_]*$/', $option->getName(), sprintf('[%s] Option %s is not snake_case.', $fixerName, $option->getName()));
            }
        }

        if ($fixer->isRisky()) {
            static::assertStringIsNotEmpty($definition->getRiskyDescription(), sprintf('[%s] Risky reasoning is required.', $fixerName));
            static::assertNotContains('phpdocs', $definition->getRiskyDescription(), sprintf('[%s] `PHPDoc` must not be in the plural in risky reasoning.', $fixerName), true);
            static::assertCorrectCasing($definition->getRiskyDescription(), 'PHPDoc', sprintf('[%s] `PHPDoc` must be in correct casing in risky reasoning.', $fixerName));
            static::assertCorrectCasing($definition->getRiskyDescription(), 'PHPUnit', sprintf('[%s] `PHPUnit` must be in correct casing in risky reasoning.', $fixerName));
        } else {
            static::assertNull($definition->getRiskyDescription(), sprintf('[%s] Fixer is not risky so no description of it expected.', $fixerName));
        }
    }

    /**
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixersAreFinal(FixerInterface $fixer)
    {
        $reflection = new \ReflectionClass($fixer);

        static::assertTrue(
            $reflection->isFinal(),
            sprintf('Fixer "%s" must be declared "final".', $fixer->getName())
        );
    }

    /**
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testDeprecatedFixersHaveCorrectSummary(FixerInterface $fixer)
    {
        $reflection = new \ReflectionClass($fixer);
        $comment = $reflection->getDocComment();

        static::assertNotContains(
            'DEPRECATED',
            $fixer->getDefinition()->getSummary(),
            'Fixer cannot contain word "DEPRECATED" in summary'
        );

        if ($fixer instanceof DeprecatedFixerInterface) {
            static::assertContains('@deprecated', $comment);
        } elseif (\is_string($comment)) {
            static::assertNotContains('@deprecated', $comment);
        }
    }

    public function provideFixerDefinitionsCases()
    {
        return array_map(static function (FixerInterface $fixer) {
            return [$fixer];
        }, $this->getAllFixers());
    }

    /**
     * @dataProvider provideFixerConfigurationDefinitionsCases
     */
    public function testFixerConfigurationDefinitions(ConfigurableFixerInterface $fixer)
    {
        $configurationDefinition = $fixer->getConfigurationDefinition();

        static::assertInstanceOf(\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface::class, $configurationDefinition);

        foreach ($configurationDefinition->getOptions() as $option) {
            static::assertInstanceOf(\PhpCsFixer\FixerConfiguration\FixerOptionInterface::class, $option);
            static::assertNotEmpty($option->getDescription());

            static::assertSame(
                !isset($this->allowedRequiredOptions[$fixer->getName()][$option->getName()]),
                $option->hasDefault(),
                sprintf(
                    $option->hasDefault()
                        ? 'Option `%s` of fixer `%s` is wrongly listed in `$allowedRequiredOptions` structure, as it is not required. If you just changed that option to not be required anymore, please adjust mentioned structure.'
                        : 'Option `%s` of fixer `%s` shall not be required. If you want to introduce new required option please adjust `$allowedRequiredOptions` structure.',
                    $option->getName(),
                    $fixer->getName()
                )
            );

            static::assertNotContains(
                'DEPRECATED',
                $option->getDescription(),
                'Option description cannot contain word "DEPRECATED"'
            );
        }
    }

    public function provideFixerConfigurationDefinitionsCases()
    {
        $fixers = array_filter($this->getAllFixers(), static function (FixerInterface $fixer) {
            return $fixer instanceof ConfigurableFixerInterface;
        });

        return array_map(static function (FixerInterface $fixer) {
            return [$fixer];
        }, $fixers);
    }

    private function getAllFixers()
    {
        $factory = new FixerFactory();

        return $factory->registerBuiltInFixers()->getFixers();
    }

    /**
     * @param mixed  $actual
     * @param string $message
     */
    private static function assertStringIsNotEmpty($actual, $message = '')
    {
        static::assertInternalType('string', $actual, $message);
        static::assertNotEmpty($actual, $message);
    }

    /**
     * @param string $needle
     * @param string $haystack
     * @param string $message
     */
    private static function assertCorrectCasing($needle, $haystack, $message)
    {
        static::assertSame(substr_count(strtolower($haystack), strtolower($needle)), substr_count($haystack, $needle), $message);
    }
}
