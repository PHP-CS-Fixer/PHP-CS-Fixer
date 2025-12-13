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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\InternalFixerInterface;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\Linter\CachingLinter;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\ProcessLinter;
use PhpCsFixer\PhpunitConstraintIsIdenticalString\Constraint\IsIdenticalString;
use PhpCsFixer\Preg;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tests\Fixer\ClassNotation\ClassAttributesSeparationFixerTest;
use PhpCsFixer\Tests\Fixer\ClassNotation\ClassDefinitionFixerTest;
use PhpCsFixer\Tests\Fixer\Comment\NoEmptyCommentFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\NoUselessElseFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixerTest;
use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @template TFixer of FixerInterface
 *
 * @phpstan-import-type _PhpTokenArrayPartial from Token
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
abstract class AbstractFixerTestCase extends TestCase
{
    use AssertTokensTrait;

    /**
     * do not modify this structure without prior discussion.
     *
     * @var array<string, array<string, bool>>
     */
    private const ALLOWED_REQUIRED_OPTIONS = [
        'header_comment' => ['header' => true],
    ];

    protected ?LinterInterface $linter = null;

    /**
     * @var null|TFixer
     */
    protected ?FixerInterface $fixer = null;

    /**
     * do not modify this structure without prior discussion.
     *
     * @var array<string, bool>
     */
    private array $allowedFixersWithoutDefaultCodeSample = [
        'general_phpdoc_annotation_remove' => true,
        'general_attribute_remove' => true,
        'general_phpdoc_tag_rename' => true,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->linter = $this->getLinter();
        $this->fixer = $this->createFixer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->linter = null;
        $this->fixer = null;
    }

    final public function testIsRisky(): void
    {
        if ($this->fixer->isRisky()) {
            self::assertValidDescription($this->fixer->getName(), 'risky description', $this->fixer->getDefinition()->getRiskyDescription());
        } else {
            self::assertNull($this->fixer->getDefinition()->getRiskyDescription(), \sprintf('[%s] Fixer is not risky so no description of it expected.', $this->fixer->getName()));
        }

        if ($this->fixer instanceof AbstractProxyFixer) {
            return;
        }

        $reflection = new \ReflectionMethod($this->fixer, 'isRisky');

        // If fixer is not risky then the method `isRisky` from `AbstractFixer` must be used
        self::assertSame(
            !$this->fixer->isRisky(),
            AbstractFixer::class === $reflection->getDeclaringClass()->getName()
        );
    }

    final public function testFixerDefinitions(): void
    {
        $fixerName = $this->fixer->getName();
        $definition = $this->fixer->getDefinition();
        $fixerIsConfigurable = $this->fixer instanceof ConfigurableFixerInterface;

        self::assertValidDescription($fixerName, 'summary', $definition->getSummary());
        if (null !== $definition->getDescription()) {
            self::assertValidDescription($fixerName, 'description', $definition->getDescription());
        }

        $samples = $definition->getCodeSamples();
        self::assertNotEmpty($samples, \sprintf('[%s] Code samples are required.', $fixerName));

        $configSamplesProvided = [];
        $dummyFileInfo = new StdinFileInfo();

        foreach ($samples as $sampleCounter => $sample) {
            $code = $sample->getCode();

            self::assertNotEmpty($code, \sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));

            self::assertStringStartsNotWith("\n", $code, \sprintf('[%s] Sample #%d must not start with linebreak', $fixerName, $sampleCounter));

            if (!$this->fixer instanceof SingleBlankLineAtEofFixer) {
                self::assertStringEndsWith("\n", $code, \sprintf('[%s] Sample #%d must end with linebreak', $fixerName, $sampleCounter));
            }

            $config = $sample->getConfiguration();

            if (null !== $config) {
                self::assertTrue($fixerIsConfigurable, \sprintf('[%s] Sample #%d has configuration, but the fixer is not configurable.', $fixerName, $sampleCounter));

                $configSamplesProvided[$sampleCounter] = $config;
            } elseif ($fixerIsConfigurable) {
                if (!$sample instanceof VersionSpecificCodeSampleInterface) {
                    self::assertArrayNotHasKey('default', $configSamplesProvided, \sprintf('[%s] Multiple non-versioned samples with default configuration.', $fixerName));
                }

                $configSamplesProvided['default'] = true;
            }

            if ($sample instanceof VersionSpecificCodeSampleInterface) {
                $supportedPhpVersions = [7_04_00, 8_00_00, 8_01_00, 8_02_00, 8_03_00, 8_04_00];

                $hasSuitableSupportedVersion = false;
                foreach ($supportedPhpVersions as $version) {
                    if ($sample->isSuitableFor($version)) {
                        $hasSuitableSupportedVersion = true;
                    }
                }
                self::assertTrue($hasSuitableSupportedVersion, 'Version specific code sample must be suitable for at least 1 supported PHP version.');

                $hasUnsuitableSupportedVersion = false;
                foreach ($supportedPhpVersions as $version) {
                    if (!$sample->isSuitableFor($version)) {
                        $hasUnsuitableSupportedVersion = true;
                    }
                }
                self::assertTrue($hasUnsuitableSupportedVersion, 'Version specific code sample must be unsuitable for at least 1 supported PHP version.');

                if (!$sample->isSuitableFor(\PHP_VERSION_ID)) {
                    continue;
                }
            }

            if ($this->fixer instanceof ConfigurableFixerInterface) {
                // always re-configure as the fixer might have been configured with diff. configuration form previous sample
                $this->fixer->configure($config ?? []);
            }

            Tokens::clearCache();
            $tokens = Tokens::fromCode($code);
            $this->fixer->fix(
                $sample instanceof FileSpecificCodeSampleInterface ? $sample->getSplFileInfo() : $dummyFileInfo,
                $tokens
            );

            self::assertTrue($tokens->isChanged(), \sprintf('[%s] Sample #%d is not changed during fixing.', $fixerName, $sampleCounter));

            $duplicatedCodeSample = array_search(
                $sample,
                \array_slice($samples, 0, $sampleCounter),
                true
            );

            self::assertFalse(
                $duplicatedCodeSample,
                \sprintf('[%s] Sample #%d duplicates #%d.', $fixerName, $sampleCounter, $duplicatedCodeSample)
            );
        }

        if ($this->fixer instanceof ConfigurableFixerInterface) {
            if (isset($configSamplesProvided['default'])) {
                self::assertSame('default', array_key_first($configSamplesProvided), \sprintf('[%s] First sample must be for the default configuration.', $fixerName));
            } elseif (!isset($this->allowedFixersWithoutDefaultCodeSample[$fixerName])) {
                self::assertArrayHasKey($fixerName, self::ALLOWED_REQUIRED_OPTIONS, \sprintf('[%s] Has no sample for default configuration.', $fixerName));
            }

            if (\count($configSamplesProvided) < 2) {
                self::fail(\sprintf('[%s] Configurable fixer only provides a default configuration sample and none for its configuration options.', $fixerName));
            }

            // @phpstan-ignore-next-line method.notFound
            $options = $this->fixer->getConfigurationDefinition()->getOptions();

            foreach ($options as $option) {
                self::assertMatchesRegularExpression('/^[a-z_]+[a-z]$/', $option->getName(), \sprintf('[%s] Option %s is not snake_case.', $fixerName, $option->getName()));
                self::assertMatchesRegularExpression(
                    '/^[A-Z].+\.$/s',
                    $option->getDescription(),
                    \sprintf('[%s] Description of option "%s" must start with capital letter and end with dot.', $fixerName, $option->getName())
                );
            }
        }

        self::assertIsInt($this->fixer->getPriority());
    }

    final public function testFixersAreFinal(): void
    {
        $reflection = $this->getFixerReflection();

        self::assertTrue(
            $reflection->isFinal(),
            \sprintf('Fixer "%s" must be declared "final".', $this->fixer->getName())
        );
    }

    final public function testDeprecatedFixersHaveCorrectSummary(): void
    {
        self::assertStringNotContainsString(
            'DEPRECATED',
            $this->fixer->getDefinition()->getSummary(),
            'Fixer cannot contain word "DEPRECATED" in summary'
        );

        $reflection = $this->getFixerReflection();
        $comment = $reflection->getDocComment();

        if ($this->fixer instanceof DeprecatedFixerInterface) {
            self::assertIsString($comment, \sprintf('Missing class PHPDoc for deprecated fixer "%s".', $this->fixer->getName()));
            self::assertStringContainsString('@deprecated', $comment);
        } elseif (\is_string($comment)) {
            self::assertStringNotContainsString('@deprecated', $comment);
        }
    }

    final public function testDeprecatedFixersDoNotHaveDeprecatedSuccessor(): void
    {
        if (!$this->fixer instanceof DeprecatedFixerInterface || [] === $this->fixer->getSuccessorsNames()) {
            $this->addToAssertionCount(1);

            return;
        }

        foreach ($this->fixer->getSuccessorsNames() as $successorName) {
            self::assertNotInstanceOf(
                DeprecatedFixerInterface::class,
                TestCaseUtils::getFixerByName($successorName),
                \sprintf(
                    'Successor fixer `%s` for deprecated fixer `%s` is deprecated itself.',
                    $successorName,
                    $this->fixer->getName(),
                )
            );
        }
    }

    /**
     * Blur filter that find candidate fixer for performance optimization to use only `insertSlices` instead of multiple `insertAt` if there is no other collection manipulation.
     */
    public function testFixerUseInsertSlicesWhenOnlyInsertionsArePerformed(): void
    {
        $reflection = $this->getFixerReflection();

        $filePath = $reflection->getFileName();
        if (false === $filePath) {
            throw new \RuntimeException('Cannot determine sourcefile for class.');
        }

        $tokens = Tokens::fromCode((string) file_get_contents($filePath));

        $sequences = $this->findAllTokenSequences($tokens, [[\T_VARIABLE, '$tokens'], [\T_OBJECT_OPERATOR], [\T_STRING]]);

        $usedMethods = array_unique(array_map(static function (array $sequence): string {
            $last = end($sequence);

            return $last->getContent();
        }, $sequences));

        // if there is no `insertAt`, it's not a candidate
        if (!\in_array('insertAt', $usedMethods, true)) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $usedMethods = array_filter($usedMethods, static fn (string $method): bool => !Preg::match('/^(count|find|generate|get|is|rewind)/', $method));

        $allowedMethods = ['insertAt'];
        $nonAllowedMethods = array_diff($usedMethods, $allowedMethods);

        if ([] === $nonAllowedMethods) {
            $fixerName = $this->fixer->getName();
            if (\in_array($fixerName, [
                // DO NOT add anything to this list at ease, align with core contributors whether it makes sense to insert tokens individually or by bulk for your case.
                // The original list of the fixers being exceptions and insert tokens individually came from legacy reasons when it was the only available methods to insert tokens.
                'PhpCsFixerInternal/configurable_fixer_template',
                'blank_line_after_namespace',
                'blank_line_after_opening_tag',
                'blank_line_before_statement',
                'class_attributes_separation',
                'date_time_immutable',
                'declare_strict_types',
                'doctrine_annotation_braces',
                'doctrine_annotation_spaces',
                'final_internal_class',
                'final_public_method_for_abstract_class',
                'function_typehint_space',
                'heredoc_indentation',
                'method_chaining_indentation',
                'native_constant_invocation',
                'new_with_braces',
                'new_with_parentheses',
                'no_short_echo_tag',
                'not_operator_with_space',
                'not_operator_with_successor_space',
                'php_unit_internal_class',
                'php_unit_no_expectation_annotation',
                'php_unit_set_up_tear_down_visibility',
                'php_unit_size_class',
                'php_unit_test_annotation',
                'php_unit_test_class_requires_covers',
                'phpdoc_to_param_type',
                'phpdoc_to_property_type',
                'phpdoc_to_return_type',
                'random_api_migration',
                'semicolon_after_instruction',
                'single_line_after_imports',
                'static_lambda',
                'strict_param',
                'void_return',
            ], true)) {
                self::markTestIncomplete(\sprintf('Fixer "%s" may be optimized to use `Tokens::insertSlices` instead of `%s`, please help and optimize it.', $fixerName, implode(', ', $allowedMethods)));
            }
            self::fail(\sprintf('Fixer "%s" shall be optimized to use `Tokens::insertSlices` instead of `%s`.', $fixerName, implode(', ', $allowedMethods)));
        }

        $this->addToAssertionCount(1);
    }

    final public function testFixerConfigurationDefinitions(): void
    {
        if (!$this->fixer instanceof ConfigurableFixerInterface) {
            $this->expectNotToPerformAssertions(); // not applied to the fixer without configuration

            return;
        }

        $configurationDefinition = $this->fixer->getConfigurationDefinition();

        foreach ($configurationDefinition->getOptions() as $option) {
            self::assertInstanceOf(FixerOptionInterface::class, $option);
            self::assertOption($option, $this->fixer);
        }
    }

    final public function testProperMethodNaming(): void
    {
        if ($this->fixer instanceof DeprecatedFixerInterface) {
            self::markTestSkipped('Not worth refactoring tests for deprecated fixers.');
        }

        /** @var array<class-string, list<string>> */
        $allowedExtraMethods = [
            ClassAttributesSeparationFixerTest::class => ['testCommentBlockStartDetection', 'provideCommentBlockStartDetectionCases'],
            ClassDefinitionFixerTest::class => ['testClassyDefinitionInfo', 'provideClassyDefinitionInfoCases', 'testClassyInheritanceInfo', 'provideClassyInheritanceInfoCases', 'testClassyInheritanceInfoPre80', 'provideClassyInheritanceInfoPre80Cases'],
            NoEmptyCommentFixerTest::class => ['testGetCommentBlock', 'provideGetCommentBlockCases'],
            NoUselessElseFixerTest::class => ['testBlockDetection', 'provideBlockDetectionCases', 'testIsInConditionWithoutBraces', 'provideIsInConditionWithoutBracesCases'],
            PhpUnitTestCaseStaticMethodCallsFixerTest::class => ['testFixerContainsAllPhpunitStaticMethodsInItsList', 'testWrongConfigTypeForMethodsAndTargetVersion', 'testPHPUnit10', 'testPHPUnit11', 'testPHPUnit12', 'testPHPUnit13'],
        ];

        $names = ['Fix', 'FixDeprecated', 'FixPre80', 'Fix80', 'FixPre81', 'Fix81', 'Fix82', 'Fix83', 'FixPre84', 'Fix84', 'FixPre85', 'Fix85', 'WithShortOpenTag', 'WithWhitespacesConfig', 'InvalidConfiguration'];
        $methodNames = ['testConfigure'];
        foreach ($names as $name) {
            $methodNames[] = 'test'.$name;
            $methodNames[] = 'provide'.$name.'Cases';
        }

        $reflectionClass = new \ReflectionObject($this);

        $extraMethods = array_map(
            static fn (\ReflectionMethod $method): string => $method->getName(),
            array_values(array_filter(
                $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
                static fn (\ReflectionMethod $method): bool => $method->getDeclaringClass()->getName() === $reflectionClass->getName()
                    && !\in_array($method->getName(), $methodNames, true)
            ))
        );

        if (isset($allowedExtraMethods[static::class])) {
            self::assertSame($allowedExtraMethods[static::class], $extraMethods);
        } else {
            self::assertTrue(method_exists($this, 'testFix'), \sprintf('Method testFix does not exist in %s.', static::class));
            self::assertTrue(method_exists($this, 'provideFixCases'), \sprintf('Method provideFixCases does not exist in %s.', static::class));
            self::assertSame(
                [],
                $extraMethods,
                \sprintf('Methods "%s" should not be present in %s.', implode('". "', $extraMethods), static::class),
            );
        }
    }

    final public function testProperMethodParameterNaming(): void
    {
        if ($this->fixer instanceof InternalFixerInterface) {
            self::markTestSkipped('Tests not implemented for this class, run the rule on codebase and check if PHPStan accepts the changes.');
        }

        $reflectionObject = new \ReflectionObject($this);

        foreach ($reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!str_starts_with($method->getName(), 'testFix')) {
                continue;
            }

            $parameters = $method->getParameters();
            if (0 === \count($parameters)) {
                continue;
            }

            self::assertSame('expected', $parameters[0]->getName(), "First parameter name in {$reflectionObject->getName()}::{$method->getName()} is incorrectly named.");

            if (1 < \count($parameters)) {
                self::assertArrayHasKey(1, $parameters);
                self::assertSame('input', $parameters[1]->getName(), "Second parameter name in {$reflectionObject->getName()}::{$method->getName()} is incorrectly named.");
            }
        }
    }

    public function testImplementingWhitespacesAwareFixerInterface(): void
    {
        $tokens = Tokens::fromCode((string) file_get_contents((string) $this->getFixerReflection()->getFileName()));

        if ($this->fixer instanceof AbstractPhpUnitFixer) {
            // AbstractPhpUnitFixer is using `$this->whitespacesConfig` and we cannot verify it is needed for the child class
            $this->addToAssertionCount(1);

            return;
        }

        if ($this->fixer instanceof AbstractProxyFixer) {
            self::assertSame(
                array_any(
                    \Closure::bind(static fn (AbstractProxyFixer $fixer): array => $fixer->createProxyFixers(), null, AbstractProxyFixer::class)($this->fixer),
                    static fn (FixerInterface $fixer): bool => $fixer instanceof WhitespacesAwareFixerInterface,
                ),
                $this->fixer instanceof WhitespacesAwareFixerInterface,
            );

            return;
        }

        self::assertSame(
            null !== $tokens->findSequence([
                [\T_VARIABLE, '$this'],
                [\T_OBJECT_OPERATOR],
                [\T_STRING, 'whitespacesConfig'],
            ]),
            $this->fixer instanceof WhitespacesAwareFixerInterface,
        );
    }

    /**
     * @return TFixer
     */
    protected function createFixer(): FixerInterface
    {
        /** @var class-string<TFixer> $fixerClassName */
        $fixerClassName = Preg::replace('/^(PhpCsFixer)\\\Tests(\\\.+)Test$/', '$1$2', static::class);

        return new $fixerClassName();
    }

    /**
     * Tests if a fixer fixes a given string to match the expected result.
     *
     * It is used both if you want to test if something is fixed or if it is not touched by the fixer.
     * It also makes sure that the expected output does not change when run through the fixer. That means that you
     * do not need two test cases like [$expected] and [$expected, $input] (where $expected is the same in both cases)
     * as the latter covers both of them.
     * This method throws an exception if $expected and $input are equal to prevent test cases that accidentally do
     * not test anything.
     *
     * @param string            $expected The expected fixer output
     * @param null|string       $input    The fixer input, or null if it should intentionally be equal to the output
     * @param null|\SplFileInfo $file     The file to fix, or null if unneeded
     */
    protected function doTest(string $expected, ?string $input = null, ?\SplFileInfo $file = null): void
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $file ??= new \SplFileInfo(__FILE__);
        $fileIsSupported = $this->fixer->supports($file);

        if (null !== $input) {
            self::assertNull($this->lintSource($input));

            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            if ($fileIsSupported) {
                self::assertTrue($this->fixer->isCandidate($tokens), 'Fixer must be a candidate for input code.');
                self::assertFalse($tokens->isChanged(), 'Fixer must not touch Tokens on candidate check.');
                $this->fixer->fix($file, $tokens);
            }

            self::assertThat(
                $tokens->generateCode(),
                new IsIdenticalString($expected),
                'Code built on input code must match expected code.'
            );
            self::assertTrue($tokens->isChanged(), 'Tokens collection built on input code must be marked as changed after fixing.');

            $tokens->clearEmptyTokens();

            self::assertSameSize(
                $tokens,
                array_unique(array_map(static fn (Token $token): string => spl_object_hash($token), $tokens->toArray())),
                'Token items inside Tokens collection must be unique.'
            );

            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($expected);
            self::assertTokens($expectedTokens, $tokens);
        }

        self::assertNull($this->lintSource($expected));

        Tokens::clearCache();
        $tokens = Tokens::fromCode($expected);

        if ($fileIsSupported) {
            $this->fixer->fix($file, $tokens);
        }

        self::assertThat(
            $tokens->generateCode(),
            new IsIdenticalString($expected),
            'Code built on expected code must not change.'
        );
        self::assertFalse($tokens->isChanged(), 'Tokens collection built on expected code must not be marked as changed after fixing.');
    }

    protected function lintSource(string $source): ?string
    {
        try {
            $this->linter->lintSource($source)->check();
        } catch (\Exception $e) {
            return $e->getMessage()."\n\nSource:\n{$source}";
        }

        return null;
    }

    protected static function assertCorrectCasing(string $haystack, string $needle, string $fixerName, string $descriptionType): void
    {
        self::assertSame(
            substr_count(strtolower($haystack), strtolower($needle)),
            substr_count($haystack, $needle),
            \sprintf('[%s] `%s` must be in correct casing in %s.', $fixerName, $needle, $descriptionType)
        );
    }

    /**
     * @return \ReflectionClass<FixerInterface>
     */
    private function getFixerReflection(): \ReflectionClass
    {
        if (null === $this->fixer) {
            throw new \LogicException('Too early call of getFixerReflection(), fixer not yet provided.');
        }

        return new \ReflectionClass($this->fixer);
    }

    private function getLinter(): LinterInterface
    {
        static $linter = null;

        if (null === $linter) {
            $linter = new CachingLinter(
                filter_var(getenv('PHP_CS_FIXER_FAST_LINT_TEST_CASES'), \FILTER_VALIDATE_BOOLEAN)
                    ? new Linter()
                    : new ProcessLinter()
            );
        }

        return $linter;
    }

    private static function assertValidDescription(string $fixerName, string $descriptionType, string $description): void
    {
        // Description:
        // "Option `a` and `b_c` are allowed."
        // becomes:
        // "Option `_` and `_` are allowed."
        // so values in backticks are excluded from check
        $descriptionWithExcludedNames = Preg::replace('/`([^`]+)`/', '`_`', $description);

        self::assertMatchesRegularExpression('/^[A-Z`].+\.$/s', $description, \sprintf('[%s] The %s must start with capital letter or a ` and end with dot.', $fixerName, $descriptionType));
        self::assertStringNotContainsString('phpdocs', $descriptionWithExcludedNames, \sprintf('[%s] `PHPDoc` must not be in the plural in %s.', $fixerName, $descriptionType));
        self::assertCorrectCasing($descriptionWithExcludedNames, 'PHPDoc', $fixerName, $descriptionType);
        self::assertCorrectCasing($descriptionWithExcludedNames, 'PHPUnit', $fixerName, $descriptionType);
        self::assertFalse(strpos($descriptionType, '``'), \sprintf('[%s] The %s must no contain sequential backticks.', $fixerName, $descriptionType));
    }

    private static function assertOption(FixerOptionInterface $option, FixerInterface $fixer): void
    {
        self::assertNotEmpty($option->getDescription());
        self::assertValidDescription($fixer->getName(), 'option:'.$option->getName(), $option->getDescription());

        self::assertSame(
            !isset(self::ALLOWED_REQUIRED_OPTIONS[$fixer->getName()][$option->getName()]),
            $option->hasDefault(),
            \sprintf(
                $option->hasDefault()
                    ? 'Option `%s` of fixer `%s` is wrongly listed in `ALLOWED_REQUIRED_OPTIONS` structure, as it is not required. If you just changed that option to not be required anymore, please adjust mentioned structure.'
                    : 'Option `%s` of fixer `%s` shall not be required. If you want to introduce new required option please adjust `ALLOWED_REQUIRED_OPTIONS` structure.',
                $option->getName(),
                $fixer->getName()
            )
        );

        self::assertStringNotContainsString(
            'DEPRECATED',
            $option->getDescription(),
            'Option description cannot contain word "DEPRECATED"'
        );

        if (!$option->hasDefault()) {
            return;
        }

        $allowedValues = $option->getAllowedValues();

        if (null === $allowedValues) {
            return;
        }

        $allowedValueSubset = $allowedValues[0];

        if (
            !$allowedValueSubset instanceof AllowedValueSubset
            || \count($option->getDefault()) !== \count($allowedValueSubset->getAllowedValues())
        ) {
            return;
        }

        self::assertSame(
            $option->getDefault(),
            $allowedValueSubset->getAllowedValues(),
            \sprintf('[%s] `%s` has default and allowed sets of the same size, so they must be the same.', $fixer->getName(), $option->getName())
        );
    }

    /**
     * @param non-empty-list<_PhpTokenArrayPartial> $sequence
     *
     * @return list<non-empty-array<int, Token>>
     */
    private function findAllTokenSequences(Tokens $tokens, array $sequence): array
    {
        $lastIndex = 0;
        $sequences = [];

        while (($found = $tokens->findSequence($sequence, $lastIndex)) !== null) {
            $keys = array_keys($found);
            $sequences[] = $found;
            \assert(\array_key_exists(2, $keys));
            $lastIndex = $keys[2];
        }

        return $sequences;
    }
}
