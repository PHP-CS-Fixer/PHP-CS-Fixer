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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\AbstractPhpdocToTypeDeclarationFixer;
use PhpCsFixer\AbstractPhpdocTypesFixer;
use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNamespacedFixer;
use PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Preg;
use PhpCsFixer\Runner\Parallel\ProcessUtils;
use PhpCsFixer\Tests\PregTest;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tests\Test\AbstractIntegrationTestCase;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tests\Tokenizer\CTTest;
use PhpCsFixer\Tests\Tokenizer\FCTTest;
use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ProjectCodeTest extends TestCase
{
    /**
     * @var null|array<string, array{class-string<TestCase>}>
     */
    private static ?array $testClassCases = null;

    /**
     * @var null|array<string, array{class-string}>
     */
    private static ?array $srcClassCases = null;

    /**
     * @var null|array<string, array{class-string, string}>
     */
    private static ?array $dataProviderMethodCases = null;

    /**
     * @var array<class-string, Tokens>
     */
    private static array $tokensCache = [];

    public static function tearDownAfterClass(): void
    {
        self::$srcClassCases = null;
        self::$testClassCases = null;
        self::$tokensCache = [];
    }

    /**
     * @dataProvider provideThatSrcClassHaveTestClassCases
     */
    public function testThatSrcClassHaveTestClass(string $className): void
    {
        $testClassName = 'PhpCsFixer\Tests'.substr($className, 10).'Test';

        self::assertTrue(class_exists($testClassName), \sprintf('Expected test class "%s" for "%s" not found.', $testClassName, $className));
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideThatSrcClassHaveTestClassCases(): iterable
    {
        return array_map(
            static fn (string $item): array => [$item],
            array_filter(
                self::getSrcClasses(),
                static function (string $className): bool {
                    $rc = new \ReflectionClass($className);

                    return !$rc->isTrait() && !$rc->isAbstract() && !$rc->isInterface() && \count($rc->getMethods(\ReflectionMethod::IS_PUBLIC)) > 0;
                }
            )
        );
    }

    /**
     * This test requires 8.2+, so it can properly detect readonly parent class.
     *
     * @dataProvider provideSrcClassCases
     *
     * @param class-string $className
     *
     * @requires PHP 8.2
     */
    public function testThatSrcClassesAreReadonlyWhenPossible(string $className): void
    {
        $rc = new \ReflectionClass($className);
        $rcProperties = $rc->getProperties();

        if (0 === \count($rcProperties)) {
            $this->addToAssertionCount(1);

            return; // public properties present, no need for class to be readonly
        }

        if (\PHP_VERSION_ID >= 8_02_00) {
            if ($rc->isReadOnly()) {
                $this->addToAssertionCount(1);

                return; // Class is readonly, no need for further checks
            }

            $parentClass = $rc->getParentClass();
            if (false !== $parentClass && !$parentClass->isReadOnly()) {
                $this->addToAssertionCount(1);

                return; // Parent class is _not_ readonly, child class cannot be readonly in such case
            }
        }

        $rc = new \ReflectionClass($className);
        $docComment = $rc->getDocComment();
        $doc = new DocBlock(false !== $docComment ? $docComment : '/** */');
        $readonly = \count($doc->getAnnotationsOfType('readonly')) > 0;

        $exceptions = [
            AliasedFixerOptionBuilder::class,
        ];

        // we allow exceptions to _not_ follow the rule,
        // but when they are ready to start following it - we shall remove them from exceptions list
        if (\in_array($className, $exceptions, true)) {
            self::assertFalse($readonly);

            return;
        }

        if ($readonly) {
            $this->addToAssertionCount(1);

            return; // already readonly
        }

        $tokens = $this->createTokensForClass($className);

        $constructorSequence = $tokens->findSequence([
            [\T_FUNCTION],
            [\T_STRING, '__construct'],
            '(',
        ]);
        if (null !== $constructorSequence) {
            $tokens = clone $tokens;
            $openIndex = $tokens->getNextTokenOfKind(array_key_last($constructorSequence), ['{']);
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);
            $tokens->overrideRange($openIndex + 1, $closeIndex - 1, []);
        }

        $tokensContent = $tokens->generateCode();
        $propertyNames = array_map(static fn (\ReflectionProperty $item) => $item->getName(), $rcProperties);

        $overrideFound = Preg::match(
            '/(?:self::\$|static::\$|\$this->)(?:'.implode('|', $propertyNames).')(?:\[[^=]*\])?\s*(?:=|(?:\?\?=))/',
            $tokensContent
        );

        if ($overrideFound) {
            $this->addToAssertionCount(1);

            return; // properties are mutable during lifecycle of instance, class is not readonly
        }

        self::fail(
            \sprintf('The class "%s" should have readonly annotation.', $className)
        );
    }

    /**
     * @dataProvider provideThatSrcClassesNotAbuseInterfacesCases
     *
     * @param class-string $className
     */
    public function testThatSrcClassesNotAbuseInterfaces(string $className): void
    {
        $rc = new \ReflectionClass($className);

        $allowedMethods = array_map(
            fn (\ReflectionClass $interface): array => $this->getPublicMethodNames($interface),
            $rc->getInterfaces()
        );

        if (\count($allowedMethods) > 0) {
            $allowedMethods = array_unique(array_merge(...array_values($allowedMethods)));
        }

        $allowedMethods[] = '__construct';
        $allowedMethods[] = '__destruct';
        $allowedMethods[] = '__wakeup';

        $exceptionMethods = [
            'configure', // due to AbstractFixer::configure
            'getConfigurationDefinition', // due to AbstractFixer::getConfigurationDefinition
            'getDefaultConfiguration', // due to AbstractFixer::getDefaultConfiguration
            'setWhitespacesConfig', // due to AbstractFixer::setWhitespacesConfig
            'createConfigurationDefinition', // due to AbstractProxyFixer calling `createConfigurationDefinition` of proxied rule
        ];

        $definedMethods = $this->getPublicMethodNames($rc);

        $extraMethods = array_diff(
            $definedMethods,
            $allowedMethods,
            $exceptionMethods
        );

        sort($extraMethods);

        self::assertEmpty(
            $extraMethods,
            \sprintf(
                "Class '%s' should not have public methods that are not part of implemented interfaces.\nViolations:\n%s",
                $className,
                implode("\n", array_map(static fn (string $item): string => " * {$item}", $extraMethods))
            )
        );
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideThatSrcClassesNotAbuseInterfacesCases(): iterable
    {
        return array_map(
            static fn (string $item): array => [$item],
            array_filter(self::getSrcClasses(), static function (string $className): bool {
                $rc = new \ReflectionClass($className);

                $doc = false !== $rc->getDocComment()
                    ? new DocBlock($rc->getDocComment())
                    : null;

                if (
                    $rc->isInterface()
                    || (null !== $doc && \count($doc->getAnnotationsOfType('internal')) > 0)
                    || \in_array($className, [
                        \PhpCsFixer\Finder::class,
                        AbstractFixerTestCase::class,
                        AbstractIntegrationTestCase::class,
                        Tokens::class,
                    ], true)
                ) {
                    return false;
                }

                $interfaces = $rc->getInterfaces();
                $interfacesCount = \count($interfaces);

                if (0 === $interfacesCount) {
                    return false;
                }

                if (1 === $interfacesCount) {
                    $interface = reset($interfaces);

                    if (\Stringable::class === $interface->getName()) {
                        return false;
                    }
                }

                return true;
            })
        );
    }

    /**
     * @dataProvider provideSrcClassCases
     *
     * @param class-string $className
     */
    public function testThatSrcClassesNotExposeProperties(string $className): void
    {
        $rc = new \ReflectionClass($className);

        self::assertEmpty(
            $rc->getProperties(\ReflectionProperty::IS_PUBLIC),
            \sprintf('Class \'%s\' should not have public properties.', $className)
        );

        if ($rc->isFinal()) {
            return;
        }

        $allowedProps = [];
        $definedProps = $rc->getProperties(\ReflectionProperty::IS_PROTECTED);

        if (false !== $rc->getParentClass()) {
            $allowedProps = $rc->getParentClass()->getProperties(\ReflectionProperty::IS_PROTECTED);
        }

        $allowedProps = array_map(static fn (\ReflectionProperty $item): string => $item->getName(), $allowedProps);

        $definedProps = array_map(static fn (\ReflectionProperty $item): string => $item->getName(), $definedProps);

        $exceptionPropsPerClass = [
            AbstractFixer::class => ['configuration', 'configurationDefinition', 'whitespacesConfig'],
            AbstractPhpdocToTypeDeclarationFixer::class => ['configuration'],
            AbstractPhpdocTypesFixer::class => ['tags'],
            AbstractProxyFixer::class => ['proxyFixers'],
            ConfigurableFixerTrait::class => ['configuration'],
            FixCommand::class => ['defaultDescription', 'defaultName'], // TODO: PHP 8.0+, remove properties and test when PHP 8+ is required
        ];

        $extraProps = array_diff(
            $definedProps,
            $allowedProps,
            $exceptionPropsPerClass[$className] ?? []
        );

        sort($extraProps);

        self::assertEmpty(
            $extraProps,
            \sprintf(
                "Class '%s' should not have protected properties.\nViolations:\n%s",
                $className,
                implode("\n", array_map(static fn (string $item): string => " * {$item}", $extraProps))
            )
        );
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testThatTestClassExtendsPhpCsFixerTestCaseClass(string $className): void
    {
        self::assertTrue(is_subclass_of($className, TestCase::class), \sprintf('Expected test class "%s" to be a subclass of "%s".', $className, TestCase::class));
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testThatTestClassesAreTraitOrAbstractOrFinal(string $testClassName): void
    {
        $rc = new \ReflectionClass($testClassName);

        self::assertTrue(
            $rc->isTrait() || $rc->isAbstract() || $rc->isFinal(),
            \sprintf('Test class %s should be trait, abstract or final.', $testClassName)
        );
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testThatTestClassesAreInternal(string $testClassName): void
    {
        $rc = new \ReflectionClass($testClassName);
        $doc = new DocBlock((string) $rc->getDocComment());

        self::assertNotEmpty(
            $doc->getAnnotationsOfType('internal'),
            \sprintf('Test class %s should have internal annotation.', $testClassName)
        );
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testThatTestClassesPublicMethodsAreCorrectlyNamed(string $testClassName): void
    {
        $reflectionClass = new \ReflectionClass($testClassName);

        $publicMethods = array_filter(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
            static fn (\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->getDeclaringClass()->getName() === $reflectionClass->getName()
        );

        if ([] === $publicMethods) {
            $this->expectNotToPerformAssertions(); // no methods to test, all good!

            return;
        }

        foreach ($publicMethods as $method) {
            self::assertMatchesRegularExpression(
                '/^(test|expect|provide|setUpBeforeClass$|tearDownAfterClass$|__construct$)/',
                $method->getName(),
                \sprintf('Public method "%s::%s" is not properly named.', $reflectionClass->getName(), $method->getName())
            );
        }
    }

    /**
     * @dataProvider provideDataProviderMethodCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testThatTestDataProvidersAreUsed(string $testClassName, string $dataProviderName): void
    {
        $usedDataProviderMethodNames = [];

        foreach ($this->getUsedDataProviderMethodNames($testClassName) as $providerName) {
            $usedDataProviderMethodNames[] = $providerName;
        }

        self::assertContains(
            $dataProviderName,
            $usedDataProviderMethodNames,
            \sprintf('Data provider "%s::%s" is not used.', $testClassName, $dataProviderName),
        );
    }

    /**
     * @dataProvider provideDataProviderMethodCases
     */
    public function testThatTestDataProvidersAreCorrectlyNamed(string $testClassName, string $dataProviderName): void
    {
        self::assertMatchesRegularExpression('/^provide[A-Z]\S+Cases$/', $dataProviderName, \sprintf(
            'Data provider "%s::%s" is not correctly named.',
            $testClassName,
            $dataProviderName,
        ));
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testThatTestClassCoversAreCorrect(string $testClassName): void
    {
        $reflectionClass = new \ReflectionClass($testClassName);

        if ($reflectionClass->isAbstract() || $reflectionClass->isInterface()) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $doc = $reflectionClass->getDocComment();
        self::assertNotFalse($doc);

        if (Preg::match('/@coversNothing/', $doc)) {
            return;
        }

        $covers = Preg::matchAll('/@covers (\S*)/', $doc, $matches);
        self::assertGreaterThanOrEqual(1, $covers, \sprintf('Missing @covers in PHPDoc of test class "%s".', $testClassName));

        array_shift($matches);

        /** @var class-string $class */
        $class = '\\'.str_replace('PhpCsFixer\Tests\\', 'PhpCsFixer\\', substr($testClassName, 0, -4));
        $parentClass = (new \ReflectionClass($class))->getParentClass();
        $parentClassName = false === $parentClass ? null : '\\'.$parentClass->getName();

        foreach ($matches as $match) {
            $classMatch = array_shift($match);
            self::assertTrue(
                $classMatch === $class || $parentClassName === $classMatch,
                \sprintf('Unexpected @covers "%s" for "%s".', $classMatch, $testClassName)
            );
        }
    }

    /**
     * @dataProvider provideSrcClassCases
     * @dataProvider provideTestClassCases
     *
     * @param class-string $className
     */
    public function testThereIsNoUsageOfDefined(string $className): void
    {
        if (CTTest::class === $className || FCTTest::class === $className) {
            $this->expectNotToPerformAssertions();

            return;
        }

        self::assertNotContains(
            'defined',
            $this->extractFunctionNamesCalledInClass($className),
            \sprintf('Class %s must not use "defined()", use "%s" to use newly introduced Token kinds.', $className, FCT::class)
        );
    }

    /**
     * @dataProvider provideSrcClassCases
     * @dataProvider provideTestClassCases
     *
     * @param class-string $className
     */
    public function testThereIsNoUsageOfExtract(string $className): void
    {
        $calledFunctions = $this->extractFunctionNamesCalledInClass($className);

        $message = \sprintf('Class %s must not use "extract()", explicitly extract only the keys that are needed - you never know what\'s else inside.', $className);
        self::assertNotContains('extract', $calledFunctions, $message);
    }

    /**
     * @dataProvider provideThereIsNoPregFunctionUsedDirectlyCases
     *
     * @param class-string $className
     */
    public function testThereIsNoPregFunctionUsedDirectly(string $className): void
    {
        if (\in_array($className, [
            ProcessUtils::class, // code copied from Symfony, we do not want to make custom adjustments there
        ], true)) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $calledFunctions = $this->extractFunctionNamesCalledInClass($className);

        $message = \sprintf('Class %s must not use preg_*, it shall use Preg::* instead.', $className);
        self::assertNotContains('preg_filter', $calledFunctions, $message);
        self::assertNotContains('preg_grep', $calledFunctions, $message);
        self::assertNotContains('preg_match', $calledFunctions, $message);
        self::assertNotContains('preg_match_all', $calledFunctions, $message);
        self::assertNotContains('preg_replace', $calledFunctions, $message);
        self::assertNotContains('preg_replace_callback', $calledFunctions, $message);
        self::assertNotContains('preg_split', $calledFunctions, $message);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideThereIsNoPregFunctionUsedDirectlyCases(): iterable
    {
        foreach (self::getSrcClasses() as $className) {
            if (Preg::class === $className) {
                continue;
            }

            yield $className => [$className];
        }

        foreach (self::getTestsDirectoryClasses('*.php') as $className) {
            if (PregTest::class === $className) {
                continue;
            }

            yield $className => [$className];
        }
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string $className
     */
    public function testThereIsNoUsageOfSetAccessible(string $className): void
    {
        $calledFunctions = $this->extractFunctionNamesCalledInClass($className);

        $message = \sprintf('Class %s must not use "setAccessible()", use "Closure::bind()" instead.', $className);
        self::assertNotContains('setAccessible', $calledFunctions, $message);
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string<TestCase> $className
     */
    public function testNoPHPUnitMockUsed(string $className): void
    {
        $calledFunctions = $this->extractFunctionNamesCalledInClass($className);

        $message = \sprintf('Class %s must not use PHPUnit\'s mock, it shall use anonymous class instead.', $className);
        self::assertNotContains('getMockBuilder', $calledFunctions, $message);
        self::assertNotContains('createMock', $calledFunctions, $message);
        self::assertNotContains('createMockForIntersectionOfInterfaces', $calledFunctions, $message);
        self::assertNotContains('createPartialMock', $calledFunctions, $message);
        self::assertNotContains('createTestProxy', $calledFunctions, $message);
        self::assertNotContains('getMockForAbstractClass', $calledFunctions, $message);
        self::assertNotContains('getMockFromWsdl', $calledFunctions, $message);
        self::assertNotContains('getMockForTrait', $calledFunctions, $message);
        self::assertNotContains('getMockClass', $calledFunctions, $message);
        self::assertNotContains('createConfiguredMock', $calledFunctions, $message);
        self::assertNotContains('getObjectForTrait', $calledFunctions, $message);
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testExpectedInputOrder(string $testClassName): void
    {
        $reflectionClass = new \ReflectionClass($testClassName);

        $publicMethods = array_filter(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
            static fn (\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->getDeclaringClass()->getName() === $reflectionClass->getName()
        );

        if ([] === $publicMethods) {
            $this->expectNotToPerformAssertions(); // no methods to test, all good!

            return;
        }

        foreach ($publicMethods as $method) {
            $parameters = $method->getParameters();

            if (\count($parameters) < 2) {
                $this->addToAssertionCount(1); // not enough parameters to test, all good!

                continue;
            }

            $expected = [
                'expected' => false,
                'input' => false,
            ];

            for ($i = \count($parameters) - 1; $i >= 0; --$i) {
                \assert(\array_key_exists($i, $parameters));
                $name = $parameters[$i]->getName();

                if (isset($expected[$name])) {
                    $expected[$name] = $i;
                }
            }

            $expectedFound = array_filter($expected, static fn ($item): bool => false !== $item);

            if (\count($expectedFound) < 2) {
                $this->addToAssertionCount(1); // not enough parameters to test, all good!

                continue;
            }

            self::assertLessThan(
                $expected['input'],
                $expected['expected'],
                \sprintf('Public method "%s::%s" has parameter \'input\' before \'expected\'.', $reflectionClass->getName(), $method->getName())
            );
        }
    }

    /**
     * @dataProvider provideDataProviderMethodCases
     *
     * @param class-string<TestCase> $testClassName
     * @param non-empty-string       $dataProviderName
     */
    public function testDataProvidersAreNonPhpVersionConditional(string $testClassName, string $dataProviderName): void
    {
        $tokens = $this->createTokensForClass($testClassName);
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $dataProviderElements = array_filter($tokensAnalyzer->getClassyElements(), static function (array $v, int $k) use ($tokens, $dataProviderName) {
            $nextToken = $tokens[$tokens->getNextMeaningfulToken($k)];

            // element is data provider method
            return 'method' === $v['type'] && $nextToken->equals([\T_STRING, $dataProviderName]);
        }, \ARRAY_FILTER_USE_BOTH);

        if (1 !== \count($dataProviderElements)) {
            throw new \UnexpectedValueException(\sprintf('Data provider "%s::%s" should be found exactly once, got %d times.', $testClassName, $dataProviderName, \count($dataProviderElements)));
        }

        $methodIndex = array_key_first($dataProviderElements);
        $startIndex = $tokens->getNextTokenOfKind($methodIndex, ['{']);
        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);

        $versionTokens = array_filter($tokens->findGivenKind(\T_STRING, $startIndex, $endIndex), static fn (Token $v): bool => $v->equalsAny([
            [\T_STRING, 'PHP_VERSION_ID'],
            [\T_STRING, 'PHP_MAJOR_VERSION'],
            [\T_STRING, 'PHP_MINOR_VERSION'],
            [\T_STRING, 'PHP_RELEASE_VERSION'],
            [\T_STRING, 'phpversion'],
        ], false));

        self::assertCount(
            0,
            $versionTokens,
            \sprintf(
                'Data provider "%s::%s" should not check PHP version and provide different cases depends on it. It leads to situation when DataProvider provides "sometimes 10, sometimes 11" test cases, depends on PHP version. That makes John Doe to see "you run 10/10" and thinking all tests are executed, instead of actually seeing "you run 10/11 and 1 skipped".',
                $testClassName,
                $dataProviderName,
            ),
        );
    }

    /**
     * @dataProvider provideDataProviderMethodCases
     */
    public function testDataProvidersDeclaredReturnType(string $testClassName, string $dataProviderName): void
    {
        $dataProvider = new \ReflectionMethod($testClassName, $dataProviderName);

        self::assertSame('iterable', $dataProvider->hasReturnType() && $dataProvider->getReturnType() instanceof \ReflectionNamedType ? $dataProvider->getReturnType()->getName() : null, \sprintf('Data provider "%s::%s" must provide `iterable` as return in method prototype.', $testClassName, $dataProviderName));

        $doc = new DocBlock(false !== $dataProvider->getDocComment() ? $dataProvider->getDocComment() : '/** */');

        $returnDocs = $doc->getAnnotationsOfType('return');

        if (\count($returnDocs) > 1) {
            throw new \UnexpectedValueException(\sprintf('Multiple "%s::%s@return" annotations.', $testClassName, $dataProviderName));
        }

        if (1 !== \count($returnDocs)) {
            $this->addToAssertionCount(1); // no @return annotation, all good!

            return;
        }

        $returnDoc = $returnDocs[0];
        $content = $returnDoc->getContent();

        self::assertMatchesRegularExpression('/iterable\</', $content, \sprintf('Data provider "%s::%s@return" must return iterable.', $testClassName, $dataProviderName));
        self::assertMatchesRegularExpression('/iterable\<(?:(int|string|int\|string), )?(?:array\{|_PhpTokenArray)/', $content, \sprintf('Data provider "%s::%s@return" must return iterable of tuples (eg `iterable<string, array{string, string}>`).', $testClassName, $dataProviderName));
    }

    /**
     * @dataProvider provideSrcClassCases
     * @dataProvider provideTestClassCases
     *
     * @param class-string $className
     */
    public function testAllCodeContainSingleClassy(string $className): void
    {
        $headerTypes = [
            \T_ABSTRACT,
            \T_AS,
            \T_COMMENT,
            \T_DECLARE,
            \T_DOC_COMMENT,
            \T_FINAL,
            \T_LNUMBER,
            \T_NAMESPACE,
            \T_NS_SEPARATOR,
            \T_OPEN_TAG,
            \T_STRING,
            \T_USE,
            \T_WHITESPACE,
            FCT::T_READONLY,
        ];

        $tokens = $this->createTokensForClass($className);
        $classyIndex = null;

        self::assertTrue($tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds()), \sprintf('File for "%s" should contains a classy.', $className));

        $count = \count($tokens);

        for ($index = 1; $index < $count; ++$index) {
            if ($tokens[$index]->isClassy()) {
                $classyIndex = $index;

                break;
            }

            if ($tokens[$index]->isGivenKind(FCT::T_ATTRIBUTE)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ATTRIBUTE, $index);

                continue;
            }

            if (!$tokens[$index]->isGivenKind($headerTypes) && !$tokens[$index]->equalsAny([';', '=', '(', ')'])) {
                self::fail(\sprintf('File for "%s" should only contains single classy, found "%s" @ %d.', $className, $tokens[$index]->toJson(), $index));
            }
        }

        self::assertNotNull($classyIndex, \sprintf('File for "%s" does not contain a classy.', $className));

        $nextTokenOfKind = $tokens->getNextTokenOfKind($classyIndex, ['{']);

        if (!\is_int($nextTokenOfKind)) {
            throw new \UnexpectedValueException('Classy without {} - braces.');
        }

        $classyEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $nextTokenOfKind);

        self::assertNull($tokens->getNextMeaningfulToken($classyEndIndex), \sprintf('File for "%s" should only contains a single classy.', $className));
    }

    /**
     * @dataProvider provideSrcClassCases
     *
     * @param class-string $className
     */
    public function testInheritdocIsNotAbused(string $className): void
    {
        $rc = new \ReflectionClass($className);

        $allowedMethods = array_map(
            fn (\ReflectionClass $interface): array => $this->getPublicMethodNames($interface),
            $rc->getInterfaces()
        );

        if (\count($allowedMethods) > 0) {
            $allowedMethods = array_merge(...array_values($allowedMethods));
        }

        $parentClass = $rc;
        while (false !== $parentClass = $parentClass->getParentClass()) {
            foreach ($parentClass->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED) as $method) {
                $allowedMethods[] = $method->getName();
            }
        }

        $allowedMethods = array_unique($allowedMethods);

        $methodsWithInheritdoc = array_filter(
            $rc->getMethods(),
            static fn (\ReflectionMethod $rm): bool => false !== $rm->getDocComment() && str_contains(strtolower($rm->getDocComment()), '@inheritdoc')
        );

        $methodsWithInheritdoc = array_map(
            static fn (\ReflectionMethod $rm): string => $rm->getName(),
            $methodsWithInheritdoc
        );

        $extraMethods = array_diff($methodsWithInheritdoc, $allowedMethods);

        self::assertEmpty(
            $extraMethods,
            \sprintf(
                "Class '%s' should not have methods with '@inheritdoc' in PHPDoc that are not inheriting PHPDoc.\nViolations:\n%s",
                $className,
                implode("\n", array_map(static fn ($item): string => " * {$item}", $extraMethods))
            )
        );
    }

    public function testAllTestsForShortOpenTagAreHandled(): void
    {
        $testClassesWithShortOpenTag = array_filter(
            self::getTestsDirectoryClasses('*Test.php'),
            fn (string $className): bool => str_contains($this->getFileContentForClass($className), 'short_open_tag') && self::class !== $className
        );
        $testFilesWithShortOpenTag = array_map(
            fn (string $className): string => './'.$this->getFilePathForClass($className),
            $testClassesWithShortOpenTag
        );

        $phpunitXmlContent = file_get_contents(__DIR__.'/../../phpunit.xml.dist');
        self::assertIsString($phpunitXmlContent);

        $phpunitXml = simplexml_load_string($phpunitXmlContent);
        self::assertNotFalse($phpunitXml);

        $shortOpenTag = $phpunitXml->xpath('testsuites/testsuite[@name="short-open-tag"]');
        self::assertIsArray($shortOpenTag);
        self::assertArrayHasKey(0, $shortOpenTag);

        $phpunitFiles = (array) $shortOpenTag[0]->file;

        sort($testFilesWithShortOpenTag);
        sort($phpunitFiles);
        self::assertSame($testFilesWithShortOpenTag, $phpunitFiles);
    }

    /**
     * @dataProvider provideDataProviderMethodCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testDataFromDataProviders(string $testClassName, string $dataProviderName): void
    {
        $exceptionsForDuplicatesCheck = [ // should only shrink
            'PhpCsFixer\Tests\AutoReview\CommandTest::provideCommandHasNameConstCases',
            'PhpCsFixer\Tests\AutoReview\DocumentationTest::provideFixerDocumentationFileIsUpToDateCases',
            'PhpCsFixer\Tests\AutoReview\FixerFactoryTest::providePriorityIntegrationTestFilesAreListedInPriorityGraphCases',
            'PhpCsFixer\Tests\Console\Command\DescribeCommandTest::provideExecuteOutputCases',
            'PhpCsFixer\Tests\Console\Command\HelpCommandTest::provideGetDisplayableAllowedValuesCases',
            'PhpCsFixer\Tests\Documentation\FixerDocumentGeneratorTest::provideGenerateRuleSetsDocumentationCases',
            'PhpCsFixer\Tests\Fixer\Basic\EncodingFixerTest::provideFixCases',
            'PhpCsFixer\Tests\UtilsTest::provideStableSortCases',
        ];

        $dataProvider = new \ReflectionMethod($testClassName, $dataProviderName);

        $docComment = $dataProvider->getDocComment();
        self::assertNotFalse($docComment, \sprintf('Data provider %s::%s is missing PHPDoc.', $testClassName, $dataProviderName));
        self::assertSame(1, substr_count($docComment, '@return'));
        $parentClass = $dataProvider->getDeclaringClass()->getParentClass();
        self::assertNotFalse($parentClass);
        if (str_starts_with($dataProviderName, 'provideFix') && AbstractFixerTestCase::class === $parentClass->getName()) {
            self::assertSame(
                1,
                substr_count($docComment, 'array'),
                \sprintf('Data provider %s::%s must use _AutogeneratedInputConfiguration.', $testClassName, $dataProviderName)
            );
        }

        $duplicates = [];
        $keyTypes = [];
        $alreadyFoundCases = [];
        foreach ($dataProvider->invoke($dataProvider->getDeclaringClass()->newInstanceWithoutConstructor()) as $candidateKey => $candidateData) {
            $keyTypes[get_debug_type($candidateKey)] = true;

            if (\in_array($testClassName.'::'.$dataProviderName, $exceptionsForDuplicatesCheck, true)) {
                continue;
            }

            $serializedCandidateData = serialize($candidateData);
            $foundInDuplicates = false;
            foreach ($alreadyFoundCases as $caseKey => $caseData) {
                if ($serializedCandidateData === $caseData) {
                    $duplicates[] = \sprintf(
                        'Duplicate in %s::%s: %s and %s.'.\PHP_EOL,
                        $testClassName,
                        $dataProviderName,
                        \is_int($caseKey) ? '#'.$caseKey : '"'.$caseKey.'"',
                        \is_int($candidateKey) ? '#'.$candidateKey : '"'.$candidateKey.'"',
                    );
                    $foundInDuplicates = true;
                }
            }
            if (!$foundInDuplicates) {
                $alreadyFoundCases[$candidateKey] = $candidateData;
            }
        }

        self::assertTrue(1 === \count($keyTypes) || 2 === \count($keyTypes));

        if (1 === \count($keyTypes)) {
            // all data provider's keys are of single type - type must be present
            $type = array_keys($keyTypes)[0];
            self::assertMatchesRegularExpression(
                \sprintf('/@return iterable\<%s, (?:array\{|_PhpTokenArray)/', $type),
                $docComment,
                \sprintf('Data provider %s::%s iterable key "%s" must be present.', $testClassName, $dataProviderName, $type)
            );
        } else {
            // data provider's keys are of both types - int and string - type must be omitted
            $types = array_keys($keyTypes);
            sort($types);
            self::assertSame(['int', 'string'], $types);
            self::assertMatchesRegularExpression(
                '/@return iterable\<(?:array\{|_PhpTokenArray)/',
                $docComment,
                \sprintf('Data provider %s::%s iterable must not have key type.', $testClassName, $dataProviderName)
            );
        }

        self::assertSame([], $duplicates);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideDataProviderMethodCases(): iterable
    {
        if (null === self::$dataProviderMethodCases) {
            self::$dataProviderMethodCases = [];
            foreach (self::provideTestClassCases() as $testClassName) {
                $testClassName = reset($testClassName);
                $reflectionClass = new \ReflectionClass($testClassName);

                $dataProviderNames = array_filter(
                    $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
                    static fn (\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->getDeclaringClass()->getName() === $reflectionClass->getName() && str_starts_with($reflectionMethod->getName(), 'provide')
                );

                foreach ($dataProviderNames as $dataProviderName) {
                    self::$dataProviderMethodCases[$testClassName.'::'.$dataProviderName->getName()] = [$testClassName, $dataProviderName->getName()];
                }
            }
        }

        yield from self::$dataProviderMethodCases;
    }

    /**
     * @dataProvider providePhpUnitFixerExtendsAbstractPhpUnitFixerCases
     *
     * @param class-string $className
     */
    public function testPhpUnitFixerExtendsAbstractPhpUnitFixer(string $className): void
    {
        $reflection = new \ReflectionClass($className);

        self::assertTrue($reflection->isSubclassOf(AbstractPhpUnitFixer::class));
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function providePhpUnitFixerExtendsAbstractPhpUnitFixerCases(): iterable
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        foreach ($factory->getFixers() as $fixer) {
            if (!str_starts_with($fixer->getName(), 'php_unit_')) {
                continue;
            }

            // this one fixes usage of PHPUnit classes
            if ($fixer instanceof PhpUnitNamespacedFixer) {
                continue;
            }

            if ($fixer instanceof AbstractProxyFixer) {
                continue;
            }

            yield [\get_class($fixer)];
        }
    }

    /**
     * @dataProvider provideSrcClassCases
     * @dataProvider provideTestClassCases
     *
     * @param class-string $name
     */
    public function testConsistentClassyNaming(string $name): void
    {
        $reflection = new \ReflectionClass($name);

        if ($reflection->isInterface()) {
            self::assertStringEndsWith('Interface', $name);
        } elseif ($reflection->isTrait()) {
            self::assertStringEndsWith('Trait', $name);
        } else {
            self::assertStringEndsNotWith('Interface', $name);
            self::assertStringEndsNotWith('Trait', $name);
        }
    }

    /**
     * @dataProvider provideSrcClassCases
     * @dataProvider provideTestClassCases
     *
     * @param class-string $className
     */
    public function testConstantsAreInUpperCase(string $className): void
    {
        $rc = new \ReflectionClass($className);

        $reflectionClassConstants = $rc->getReflectionConstants();

        if (\count($reflectionClassConstants) < 1) {
            $this->expectNotToPerformAssertions();

            return;
        }

        foreach ($reflectionClassConstants as $constant) {
            $constantName = $constant->getName();
            self::assertSame(strtoupper($constantName), $constantName, $className);
        }
    }

    /**
     * @return iterable<string, array{class-string}>
     */
    public static function provideSrcClassCases(): iterable
    {
        if (null === self::$srcClassCases) {
            $cases = self::getSrcClasses();

            self::$srcClassCases = array_combine(
                $cases,
                array_map(static fn (string $case): array => [$case], $cases),
            );
        }

        yield from self::$srcClassCases;
    }

    /**
     * @return iterable<string, array{class-string<TestCase>}>
     */
    public static function provideTestClassCases(): iterable
    {
        if (null === self::$testClassCases) {
            $cases = self::getTestsDirectoryClasses('*Test.php');

            self::$testClassCases = array_combine(
                $cases,
                array_map(static fn (string $case): array => [$case], $cases),
            );
        }

        yield from self::$testClassCases;
    }

    /**
     * @param class-string $className
     *
     * @return array<array-key, string>
     */
    private function extractFunctionNamesCalledInClass(string $className): array
    {
        /** @var list<Token> $tokens */
        $tokens = $this->createTokensForClass($className)->toArray();

        /** @var list<Token> $stringTokens */
        $stringTokens = array_filter(
            $tokens,
            static fn (Token $token): bool => $token->isGivenKind(\T_STRING)
        );

        $strings = array_map(
            static fn (Token $token): string => $token->getContent(),
            $stringTokens
        );

        return array_unique($strings);
    }

    /**
     * @param class-string $className
     */
    private function getFilePathForClass(string $className): string
    {
        $file = $className;
        $file = Preg::replace('#^PhpCsFixer\\\Tests\\\#', 'tests\\', $file);
        $file = Preg::replace('#^PhpCsFixer\\\#', 'src\\', $file);

        return str_replace('\\', \DIRECTORY_SEPARATOR, $file).'.php';
    }

    /**
     * @param class-string $className
     */
    private function getFileContentForClass(string $className): string
    {
        return (string) file_get_contents($this->getFilePathForClass($className));
    }

    /**
     * @param class-string $className
     */
    private function createTokensForClass(string $className): Tokens
    {
        if (!isset(self::$tokensCache[$className])) {
            self::$tokensCache[$className] = Tokens::fromCode(self::getFileContentForClass($className));
        }

        return self::$tokensCache[$className];
    }

    /**
     * @param class-string<TestCase> $testClassName
     *
     * @return iterable<string, string>
     */
    private function getUsedDataProviderMethodNames(string $testClassName): iterable
    {
        foreach ($this->getAnnotationsOfTestClass($testClassName, 'dataProvider') as $methodName => $dataProviderAnnotation) {
            if (Preg::match('/@dataProvider\s+(?P<methodName>\w+)/', $dataProviderAnnotation->getContent(), $matches)) {
                yield $methodName => $matches['methodName'];
            }
        }
    }

    /**
     * @param class-string<TestCase> $testClassName
     *
     * @return iterable<string, Annotation>
     */
    private function getAnnotationsOfTestClass(string $testClassName, string $annotation): iterable
    {
        $tokens = $this->createTokensForClass($testClassName);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }

            $methodName = $tokens[$tokens->getNextTokenOfKind($index, [[\T_STRING]])]->getContent();

            $docBlock = new DocBlock($token->getContent());
            $dataProviderAnnotations = $docBlock->getAnnotationsOfType($annotation);

            foreach ($dataProviderAnnotations as $dataProviderAnnotation) {
                yield $methodName => $dataProviderAnnotation;
            }
        }
    }

    /**
     * @return list<class-string>
     */
    private static function getSrcClasses(): array
    {
        static $classes;

        if (null !== $classes) {
            return $classes;
        }

        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__.'/../../src')
            ->exclude([
                'Resources',
            ])
        ;

        /** @var list<class-string> $classes */
        $classes = array_map(
            static fn (SplFileInfo $file): string => \sprintf(
                '%s\%s%s%s',
                'PhpCsFixer',
                strtr($file->getRelativePath(), \DIRECTORY_SEPARATOR, '\\'),
                '' !== $file->getRelativePath() ? '\\' : '',
                $file->getBasename('.'.$file->getExtension())
            ),
            iterator_to_array($finder, false)
        );

        sort($classes);

        return $classes;
    }

    /**
     * @return ($pattern is '*Test.php' ? list<class-string<TestCase>> : list<class-string>)
     */
    private static function getTestsDirectoryClasses(string $pattern): array
    {
        /** @var array<string, list<class-string>> $classes */
        static $classes = [];

        if (isset($classes[$pattern])) {
            return $classes[$pattern];
        }

        $finder = Finder::create()
            ->files()
            ->name($pattern)
            ->in(__DIR__.'/..')
            ->exclude([
                'Fixtures',
            ])
        ;

        /** @var ($pattern is '*Test.php' ? list<class-string<TestCase>> : list<class-string>) $foundClasses */
        $foundClasses = array_map(
            static fn (SplFileInfo $file): string => \sprintf(
                'PhpCsFixer\Tests\%s%s%s',
                strtr($file->getRelativePath(), \DIRECTORY_SEPARATOR, '\\'),
                '' !== $file->getRelativePath() ? '\\' : '',
                $file->getBasename('.'.$file->getExtension())
            ),
            iterator_to_array($finder, false)
        );

        sort($foundClasses);

        return $classes[$pattern] = $foundClasses;
    }

    /**
     * @param \ReflectionClass<object> $rc
     *
     * @return list<string>
     */
    private function getPublicMethodNames(\ReflectionClass $rc): array
    {
        return array_map(
            static fn (\ReflectionMethod $rm): string => $rm->getName(),
            $rc->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
    }
}
