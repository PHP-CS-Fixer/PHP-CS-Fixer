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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
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
use PhpCsFixer\Tests\Fixer\Comment\HeaderCommentFixerTest;
use PhpCsFixer\Tests\Fixer\Comment\NoEmptyCommentFixerTest;
use PhpCsFixer\Tests\Fixer\Comment\SingleLineCommentStyleFixerTest;
use PhpCsFixer\Tests\Fixer\ConstantNotation\NativeConstantInvocationFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\NoBreakCommentFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\NoUnneededControlParenthesesFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\NoUselessElseFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\YodaStyleFixerTest;
use PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixerTest;
use PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationBracesFixerTest;
use PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixerTest;
use PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixerTest;
use PhpCsFixer\Tests\Fixer\FunctionNotation\FunctionDeclarationFixerTest;
use PhpCsFixer\Tests\Fixer\FunctionNotation\MethodArgumentSpaceFixerTest;
use PhpCsFixer\Tests\Fixer\FunctionNotation\NativeFunctionInvocationFixerTest;
use PhpCsFixer\Tests\Fixer\FunctionNotation\ReturnTypeDeclarationFixerTest;
use PhpCsFixer\Tests\Fixer\Import\FullyQualifiedStrictTypesFixerTest;
use PhpCsFixer\Tests\Fixer\Import\GlobalNamespaceImportFixerTest;
use PhpCsFixer\Tests\Fixer\Import\OrderedImportsFixerTest;
use PhpCsFixer\Tests\Fixer\Import\SingleImportPerStatementFixerTest;
use PhpCsFixer\Tests\Fixer\LanguageConstruct\FunctionToConstantFixerTest;
use PhpCsFixer\Tests\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixerTest;
use PhpCsFixer\Tests\Fixer\ListNotation\ListSyntaxFixerTest;
use PhpCsFixer\Tests\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixerTest;
use PhpCsFixer\Tests\Fixer\Operator\BinaryOperatorSpacesFixerTest;
use PhpCsFixer\Tests\Fixer\Operator\ConcatSpaceFixerTest;
use PhpCsFixer\Tests\Fixer\Operator\IncrementStyleFixerTest;
use PhpCsFixer\Tests\Fixer\Operator\NewWithParenthesesFixerTest;
use PhpCsFixer\Tests\Fixer\Operator\NoUselessConcatOperatorFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\AlignMultilineCommentFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\GeneralPhpdocTagRenameFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocNoAccessFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocNoAliasTagFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocNoEmptyReturnFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocNoPackageFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocOrderByValueFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocOrderFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocParamOrderFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocSeparationFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocSummaryFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocTrimFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocTypesOrderFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocVarWithoutNameFixerTest;
use PhpCsFixer\Tests\Fixer\PhpTag\EchoTagSyntaxFixerTest;
use PhpCsFixer\Tests\Fixer\PhpTag\NoClosingTagFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitConstructFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitDedicateAssertFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitMethodCasingFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitStrictFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixerTest;
use PhpCsFixer\Tests\Fixer\ReturnNotation\ReturnAssignmentFixerTest;
use PhpCsFixer\Tests\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixerTest;
use PhpCsFixer\Tests\Fixer\Semicolon\NoEmptyStatementFixerTest;
use PhpCsFixer\Tests\Fixer\Semicolon\SemicolonAfterInstructionFixerTest;
use PhpCsFixer\Tests\Fixer\Semicolon\SpaceAfterSemicolonFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\BlankLineBeforeStatementFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\IndentationTypeFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\NoExtraBlankLinesFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\NoSpacesAroundOffsetFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\SpacesInsideParenthesesFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\StatementIndentationFixerTest;
use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractFixerTestCase extends TestCase
{
    use AssertTokensTrait;

    /**
     * @var null|LinterInterface
     */
    protected $linter;

    /**
     * @var null|AbstractFixer
     */
    protected $fixer;

    /**
     * do not modify this structure without prior discussion.
     *
     * @var array<string, array<string, bool>>
     */
    private array $allowedRequiredOptions = [
        'header_comment' => ['header' => true],
    ];

    /**
     * do not modify this structure without prior discussion.
     *
     * @var array<string, bool>
     */
    private array $allowedFixersWithoutDefaultCodeSample = [
        'general_phpdoc_annotation_remove' => true,
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
            self::assertNull($this->fixer->getDefinition()->getRiskyDescription(), sprintf('[%s] Fixer is not risky so no description of it expected.', $this->fixer->getName()));
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
        self::assertNotEmpty($samples, sprintf('[%s] Code samples are required.', $fixerName));

        $configSamplesProvided = [];
        $dummyFileInfo = new StdinFileInfo();

        foreach ($samples as $sampleCounter => $sample) {
            self::assertInstanceOf(CodeSampleInterface::class, $sample, sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));
            self::assertIsInt($sampleCounter);

            $code = $sample->getCode();

            self::assertNotEmpty($code, sprintf('[%s] Sample #%d', $fixerName, $sampleCounter));

            self::assertStringStartsNotWith("\n", $code, sprintf('[%s] Sample #%d must not start with linebreak', $fixerName, $sampleCounter));

            if (!$this->fixer instanceof SingleBlankLineAtEofFixer) {
                self::assertStringEndsWith("\n", $code, sprintf('[%s] Sample #%d must end with linebreak', $fixerName, $sampleCounter));
            }

            $config = $sample->getConfiguration();

            if (null !== $config) {
                self::assertTrue($fixerIsConfigurable, sprintf('[%s] Sample #%d has configuration, but the fixer is not configurable.', $fixerName, $sampleCounter));

                $configSamplesProvided[$sampleCounter] = $config;
            } elseif ($fixerIsConfigurable) {
                if (!$sample instanceof VersionSpecificCodeSampleInterface) {
                    self::assertArrayNotHasKey('default', $configSamplesProvided, sprintf('[%s] Multiple non-versioned samples with default configuration.', $fixerName));
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

            if ($fixerIsConfigurable) {
                // always re-configure as the fixer might have been configured with diff. configuration form previous sample
                $this->fixer->configure($config ?? []);
            }

            Tokens::clearCache();
            $tokens = Tokens::fromCode($code);
            $this->fixer->fix(
                $sample instanceof FileSpecificCodeSampleInterface ? $sample->getSplFileInfo() : $dummyFileInfo,
                $tokens
            );

            self::assertTrue($tokens->isChanged(), sprintf('[%s] Sample #%d is not changed during fixing.', $fixerName, $sampleCounter));

            $duplicatedCodeSample = array_search(
                $sample,
                \array_slice($samples, 0, $sampleCounter),
                true
            );

            self::assertFalse(
                $duplicatedCodeSample,
                sprintf('[%s] Sample #%d duplicates #%d.', $fixerName, $sampleCounter, $duplicatedCodeSample)
            );
        }

        if ($fixerIsConfigurable) {
            if (isset($configSamplesProvided['default'])) {
                self::assertSame('default', array_key_first($configSamplesProvided), sprintf('[%s] First sample must be for the default configuration.', $fixerName));
            } elseif (!isset($this->allowedFixersWithoutDefaultCodeSample[$fixerName])) {
                self::assertArrayHasKey($fixerName, $this->allowedRequiredOptions, sprintf('[%s] Has no sample for default configuration.', $fixerName));
            }

            if (\count($configSamplesProvided) < 2) {
                self::fail(sprintf('[%s] Configurable fixer only provides a default configuration sample and none for its configuration options.', $fixerName));
            }

            $options = $this->fixer->getConfigurationDefinition()->getOptions();

            foreach ($options as $option) {
                self::assertMatchesRegularExpression('/^[a-z_]+[a-z]$/', $option->getName(), sprintf('[%s] Option %s is not snake_case.', $fixerName, $option->getName()));
                self::assertMatchesRegularExpression(
                    '/^[A-Z].+\.$/s',
                    $option->getDescription(),
                    sprintf('[%s] Description of option "%s" must start with capital letter and end with dot.', $fixerName, $option->getName())
                );
            }
        }

        self::assertIsInt($this->fixer->getPriority());
    }

    final public function testFixersAreFinal(): void
    {
        $reflection = new \ReflectionClass($this->fixer);

        self::assertTrue(
            $reflection->isFinal(),
            sprintf('Fixer "%s" must be declared "final".', $this->fixer->getName())
        );
    }

    final public function testDeprecatedFixersHaveCorrectSummary(): void
    {
        self::assertStringNotContainsString(
            'DEPRECATED',
            $this->fixer->getDefinition()->getSummary(),
            'Fixer cannot contain word "DEPRECATED" in summary'
        );

        $reflection = new \ReflectionClass($this->fixer);
        $comment = $reflection->getDocComment();

        if ($this->fixer instanceof DeprecatedFixerInterface) {
            self::assertIsString($comment, sprintf('Missing class PHPDoc for deprecated fixer "%s".', $this->fixer->getName()));
            self::assertStringContainsString('@deprecated', $comment);
        } elseif (\is_string($comment)) {
            self::assertStringNotContainsString('@deprecated', $comment);
        }
    }

    /**
     * Blur filter that find candidate fixer for performance optimization to use only `insertSlices` instead of multiple `insertAt` if there is no other collection manipulation.
     */
    public function testFixerUseInsertSlicesWhenOnlyInsertionsArePerformed(): void
    {
        $reflection = new \ReflectionClass($this->fixer);

        $filePath = $reflection->getFileName();
        if (false === $filePath) {
            throw new \RuntimeException('Cannot determine sourcefile for class.');
        }

        $tokens = Tokens::fromCode(file_get_contents($filePath));

        $sequences = $this->findAllTokenSequences($tokens, [[T_VARIABLE, '$tokens'], [T_OBJECT_OPERATOR], [T_STRING]]);

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
                self::markTestIncomplete(sprintf('Fixer "%s" may be optimized to use `Tokens::insertSlices` instead of `%s`, please help and optimize it.', $fixerName, implode(', ', $allowedMethods)));
            }
            self::fail(sprintf('Fixer "%s" shall be optimized to use `Tokens::insertSlices` instead of `%s`.', $fixerName, implode(', ', $allowedMethods)));
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
            self::assertNotEmpty($option->getDescription());
            self::assertValidDescription($this->fixer->getName(), 'option:'.$option->getName(), $option->getDescription());

            self::assertSame(
                !isset($this->allowedRequiredOptions[$this->fixer->getName()][$option->getName()]),
                $option->hasDefault(),
                sprintf(
                    $option->hasDefault()
                        ? 'Option `%s` of fixer `%s` is wrongly listed in `$allowedRequiredOptions` structure, as it is not required. If you just changed that option to not be required anymore, please adjust mentioned structure.'
                        : 'Option `%s` of fixer `%s` shall not be required. If you want to introduce new required option please adjust `$allowedRequiredOptions` structure.',
                    $option->getName(),
                    $this->fixer->getName()
                )
            );

            self::assertStringNotContainsString(
                'DEPRECATED',
                $option->getDescription(),
                'Option description cannot contain word "DEPRECATED"'
            );
        }
    }

    final public function testProperMethodNaming(): void
    {
        if ($this->fixer instanceof DeprecatedFixerInterface) {
            self::markTestSkipped('Not worth refactoring tests for deprecated fixers.');
        }

        // should only shrink, baseline of classes violating method naming
        $exceptionClasses = [
            AlignMultilineCommentFixerTest::class,
            BinaryOperatorSpacesFixerTest::class,
            BlankLineBeforeStatementFixerTest::class,
            BlankLinesBeforeNamespaceFixerTest::class,
            ClassAttributesSeparationFixerTest::class,
            ClassDefinitionFixerTest::class,
            ConcatSpaceFixerTest::class,
            DoctrineAnnotationArrayAssignmentFixerTest::class,
            DoctrineAnnotationBracesFixerTest::class,
            DoctrineAnnotationIndentationFixerTest::class,
            DoctrineAnnotationSpacesFixerTest::class,
            EchoTagSyntaxFixerTest::class,
            FullyQualifiedStrictTypesFixerTest::class,
            FunctionDeclarationFixerTest::class,
            FunctionToConstantFixerTest::class,
            GeneralPhpdocTagRenameFixerTest::class,
            GlobalNamespaceImportFixerTest::class,
            HeaderCommentFixerTest::class,
            IncrementStyleFixerTest::class,
            IndentationTypeFixerTest::class,
            ListSyntaxFixerTest::class,
            MethodArgumentSpaceFixerTest::class,
            MultilineWhitespaceBeforeSemicolonsFixerTest::class,
            NativeConstantInvocationFixerTest::class,
            NativeFunctionInvocationFixerTest::class,
            NewWithParenthesesFixerTest::class,
            NoBlankLinesAfterPhpdocFixerTest::class,
            NoBreakCommentFixerTest::class,
            NoClosingTagFixerTest::class,
            NoEmptyCommentFixerTest::class,
            NoEmptyStatementFixerTest::class,
            NoExtraBlankLinesFixerTest::class,
            NoSpacesAroundOffsetFixerTest::class,
            NoUnneededControlParenthesesFixerTest::class,
            NoUselessConcatOperatorFixerTest::class,
            NoUselessElseFixerTest::class,
            OrderedImportsFixerTest::class,
            PhpdocAddMissingParamAnnotationFixerTest::class,
            PhpdocNoAccessFixerTest::class,
            PhpdocNoAliasTagFixerTest::class,
            PhpdocNoEmptyReturnFixerTest::class,
            PhpdocNoPackageFixerTest::class,
            PhpdocOrderByValueFixerTest::class,
            PhpdocOrderFixerTest::class,
            PhpdocParamOrderFixerTest::class,
            PhpdocReturnSelfReferenceFixerTest::class,
            PhpdocSeparationFixerTest::class,
            PhpdocSummaryFixerTest::class,
            PhpdocTrimFixerTest::class,
            PhpdocTypesOrderFixerTest::class,
            PhpdocVarWithoutNameFixerTest::class,
            PhpUnitConstructFixerTest::class,
            PhpUnitDedicateAssertFixerTest::class,
            PhpUnitMethodCasingFixerTest::class,
            PhpUnitStrictFixerTest::class,
            PhpUnitTestCaseStaticMethodCallsFixerTest::class,
            ReturnAssignmentFixerTest::class,
            ReturnTypeDeclarationFixerTest::class,
            SemicolonAfterInstructionFixerTest::class,
            SingleImportPerStatementFixerTest::class,
            SingleLineCommentStyleFixerTest::class,
            SingleSpaceAroundConstructFixerTest::class,
            SpaceAfterSemicolonFixerTest::class,
            SpacesInsideParenthesesFixerTest::class,
            StatementIndentationFixerTest::class,
            YodaStyleFixerTest::class,
        ];

        $names = ['Fix', 'Fix74Deprecated', 'FixPre80', 'Fix80', 'FixPre81', 'Fix81', 'Fix82', 'Fix83', 'WithWhitespacesConfig', 'InvalidConfiguration'];
        $methodNames = ['testConfigure'];
        foreach ($names as $name) {
            $methodNames[] = 'test'.$name;
            $methodNames[] = 'provide'.$name.'Cases';
        }

        $reflectionClass = new \ReflectionObject($this);

        $extraMethods = array_map(
            static fn (\ReflectionMethod $method): string => $method->getName(),
            array_filter(
                $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
                static fn (\ReflectionMethod $method): bool => $method->getDeclaringClass()->getName() === $reflectionClass->getName()
                    && !\in_array($method->getName(), $methodNames, true)
            )
        );

        if (\in_array(static::class, $exceptionClasses, true)) {
            self::assertNotSame(
                [],
                $extraMethods,
                sprintf('Class "%s" have correct method names, remove it from exceptions list.', static::class),
            );
            self::markTestSkipped('Not covered yet.');
        }

        self::assertTrue(method_exists($this, 'testFix'), sprintf('Method testFix does not exist in %s.', static::class));
        self::assertTrue(method_exists($this, 'provideFixCases'), sprintf('Method provideFixCases does not exist in %s.', static::class));
        self::assertSame(
            [],
            $extraMethods,
            sprintf('Methods "%s" should not be present in %s.', implode('". "', $extraMethods), static::class),
        );
    }

    protected function createFixer(): AbstractFixer
    {
        $fixerClassName = preg_replace('/^(PhpCsFixer)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);

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
                'Code build on input code must match expected code.'
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
            'Code build on expected code must not change.'
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
            sprintf('[%s] `%s` must be in correct casing in %s.', $fixerName, $needle, $descriptionType)
        );
    }

    private function getLinter(): LinterInterface
    {
        static $linter = null;

        if (null === $linter) {
            $linter = new CachingLinter(
                getenv('FAST_LINT_TEST_CASES') ? new Linter() : new ProcessLinter()
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
        $descriptionWithExcludedNames = preg_replace('/`([^`]+)`/', '`_`', $description);

        self::assertMatchesRegularExpression('/^[A-Z`].+\.$/s', $description, sprintf('[%s] The %s must start with capital letter or a ` and end with dot.', $fixerName, $descriptionType));
        self::assertStringNotContainsString('phpdocs', $descriptionWithExcludedNames, sprintf('[%s] `PHPDoc` must not be in the plural in %s.', $fixerName, $descriptionType));
        self::assertCorrectCasing($descriptionWithExcludedNames, 'PHPDoc', $fixerName, $descriptionType);
        self::assertCorrectCasing($descriptionWithExcludedNames, 'PHPUnit', $fixerName, $descriptionType);
        self::assertFalse(strpos($descriptionType, '``'), sprintf('[%s] The %s must no contain sequential backticks.', $fixerName, $descriptionType));
    }

    /**
     * @param list<array{0: int, 1?: string}> $sequence
     *
     * @return list<non-empty-array<int, Token>>
     */
    private function findAllTokenSequences(Tokens $tokens, array $sequence): array
    {
        $lastIndex = 0;
        $sequences = [];

        while ($found = $tokens->findSequence($sequence, $lastIndex)) {
            $keys = array_keys($found);
            $sequences[] = $found;
            $lastIndex = $keys[2];
        }

        return $sequences;
    }
}
