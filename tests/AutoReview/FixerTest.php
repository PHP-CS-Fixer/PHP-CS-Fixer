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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 */
final class FixerTest extends TestCase
{
    /**
     * @param FixerInterface $fixer
     *
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixerDefinitions(FixerInterface $fixer)
    {
        $this->assertInstanceOf('PhpCsFixer\Fixer\DefinedFixerInterface', $fixer);

        $definition = $fixer->getDefinition();

        $this->assertRegExp('/^[A-Z@].*\.$/', $definition->getSummary(), sprintf('[%s] Description must start with capital letter or an @ and end with dot.', $fixer->getName()));

        $samples = $definition->getCodeSamples();
        $this->assertNotEmpty($samples, sprintf('[%s] Code samples are required.', $fixer->getName()));

        $dummyFileInfo = new StdinFileInfo();
        foreach ($samples as $sampleCounter => $sample) {
            $this->assertInstanceOf(CodeSampleInterface::class, $sample, sprintf('[%s] Sample #%d', $fixer->getName(), $sampleCounter));
            $code = $sample->getCode();
            $this->assertStringIsNotEmpty($code, sprintf('[%s] Sample #%d', $fixer->getName(), $sampleCounter));

            if ($sample instanceof VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(PHP_VERSION_ID)) {
                continue;
            }

            $config = $sample->getConfiguration();
            if (null !== $config) {
                $this->assertInternalType('array', $config, sprintf('[%s] Sample #%d configuration must be an array or null.', $fixer->getName(), $sampleCounter));
                if ($fixer instanceof ConfigurableFixerInterface) {
                    $fixer->configure($config);
                } else {
                    $this->assertInternalType('array', $config, sprintf('[%s] Sample #%d has configuration, but the fixer is not configurable.', $fixer->getName(), $sampleCounter));
                }
            }

            Tokens::clearCache();
            $tokens = Tokens::fromCode($code);
            $fixer->fix(
                $sample instanceof FileSpecificCodeSampleInterface ? $sample->getSplFileInfo() : $dummyFileInfo,
                $tokens
            );
            $this->assertTrue($tokens->isChanged(), sprintf('[%s] Sample #%d is not changed during fixing.', $fixer->getName(), $sampleCounter));

            $duplicatedCodeSamples = array_keys(array_filter(
                array_slice($samples, 0, $sampleCounter),
                function (CodeSampleInterface $item) use ($sample) { return $this->areObjectGettersEqual($sample, $item); }
            ));
            $this->assertEmpty(
                $duplicatedCodeSamples,
                sprintf('[%s] Code sample #%d duplicates #%d.', $fixer->getName(), $sampleCounter, array_pop($duplicatedCodeSamples))
            );
        }

        if ($fixer->isRisky()) {
            $this->assertStringIsNotEmpty($definition->getRiskyDescription(), sprintf('[%s] Risky reasoning is required.', $fixer->getName()));
        } else {
            $this->assertNull($definition->getRiskyDescription(), sprintf('[%s] Fixer is not risky so no description of it expected.', $fixer->getName()));
        }
    }

    /**
     * @param FixerInterface $fixer
     *
     * @group legacy
     * @dataProvider provideFixerDefinitionsCases
     * @expectedDeprecation PhpCsFixer\FixerDefinition\FixerDefinition::getConfigurationDescription is deprecated and will be removed in 3.0.
     * @expectedDeprecation PhpCsFixer\FixerDefinition\FixerDefinition::getDefaultConfiguration is deprecated and will be removed in 3.0.
     */
    public function testLegacyFixerDefinitions(FixerInterface $fixer)
    {
        $definition = $fixer->getDefinition();

        $this->assertNull($definition->getConfigurationDescription(), sprintf('[%s] No configuration description expected.', $fixer->getName()));
        $this->assertNull($definition->getDefaultConfiguration(), sprintf('[%s] No default configuration expected.', $fixer->getName()));
    }

    /**
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixersAreFinal(FixerInterface $fixer)
    {
        $reflection = new \ReflectionClass($fixer);

        $this->assertTrue(
            $reflection->isFinal(),
            sprintf('Fixer "%s" must be declared "final".', $fixer->getName())
        );
    }

    /**
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixersAreDefined(FixerInterface $fixer)
    {
        $this->assertInstanceOf('PhpCsFixer\Fixer\DefinedFixerInterface', $fixer);
    }

    public function provideFixerDefinitionsCases()
    {
        return array_map(function (FixerInterface $fixer) {
            return array($fixer);
        }, $this->getAllFixers());
    }

    /**
     * @param ConfigurationDefinitionFixerInterface $fixer
     *
     * @dataProvider provideFixerConfigurationDefinitionsCases
     */
    public function testFixerConfigurationDefinitions(ConfigurationDefinitionFixerInterface $fixer)
    {
        $configurationDefinition = $fixer->getConfigurationDefinition();

        $this->assertInstanceOf('PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface', $configurationDefinition);

        foreach ($configurationDefinition->getOptions() as $option) {
            $this->assertNotEmpty($option->getDescription());
        }
    }

    public function provideFixerConfigurationDefinitionsCases()
    {
        $fixers = array_filter($this->getAllFixers(), function (FixerInterface $fixer) {
            return $fixer instanceof ConfigurationDefinitionFixerInterface;
        });

        return array_map(function (FixerInterface $fixer) {
            return array($fixer);
        }, $fixers);
    }

    private function getPublicGetterNames($obj)
    {
        $classReflection = new \ReflectionClass($obj);

        $methods = array_filter(
            array_map(
                function (\ReflectionMethod $reflectionMethod) { return $reflectionMethod->getName(); },
                $classReflection->getMethods(\ReflectionMethod::IS_PUBLIC)
            ),
            function ($methodName) { return 'get' === substr($methodName, 0, 3); }
        );

        sort($methods);

        return $methods;
    }

    private function getPublicGetterValues($obj, array $getters)
    {
        $values = [];

        foreach ($getters as $getter) {
            $values[$getter] = $obj->$getter();
        }

        return $values;
    }

    private function areObjectGettersEqual(CodeSampleInterface $left, CodeSampleInterface $right)
    {
        $leftGetters = $this->getPublicGetterNames($left);
        $rightGetters = $this->getPublicGetterNames($right);

        if ($leftGetters !== $rightGetters) {
            return false;
        }

        $leftValues = $this->getPublicGetterValues($left, $leftGetters);
        $rightValues = $this->getPublicGetterValues($right, $rightGetters);

        if ($leftValues !== $rightValues) {
            return false;
        }

        return true;
    }

    private function getAllFixers()
    {
        $factory = new FixerFactory();

        return $factory->registerBuiltInFixers()->getFixers();
    }

    /**
     * copy paste from GeckoPackages/GeckoPHPUnit StringsAssertTrait, to replace with Trait when possible.
     *
     * @param mixed  $actual
     * @param string $message
     */
    private static function assertStringIsNotEmpty($actual, $message = '')
    {
        self::assertInternalType('string', $actual, $message);
        self::assertNotEmpty($actual, $message);
    }
}
