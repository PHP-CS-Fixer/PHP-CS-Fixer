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
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Preg;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tests\Test\AbstractIntegrationTestCase;
use PhpCsFixer\Tests\TestCase;
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
        $doc = new DocBlock($rc->getDocComment());

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

        /** @var \ReflectionMethod $method */
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
            return 'method' === $v['type'] && $nextToken->equals([T_STRING, $dataProviderName]);
        }, ARRAY_FILTER_USE_BOTH);

        if (1 !== \count($dataProviderElements)) {
            throw new \UnexpectedValueException(\sprintf('Data provider "%s::%s" should be found exactly once, got %d times.', $testClassName, $dataProviderName, \count($dataProviderElements)));
        }

        $methodIndex = array_key_first($dataProviderElements);
        $startIndex = $tokens->getNextTokenOfKind($methodIndex, ['{']);
        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);

        $versionTokens = array_filter($tokens->findGivenKind(T_STRING, $startIndex, $endIndex), static function (Token $v): bool {
            return $v->equalsAny([
                [T_STRING, 'PHP_VERSION_ID'],
                [T_STRING, 'PHP_MAJOR_VERSION'],
                [T_STRING, 'PHP_MINOR_VERSION'],
                [T_STRING, 'PHP_RELEASE_VERSION'],
                [T_STRING, 'phpversion'],
            ], false);
        });

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
        $types = $returnDoc->getTypes();

        self::assertCount(1, $types, \sprintf('Data provider "%s::%s@return" must provide single type.', $testClassName, $dataProviderName));
        self::assertMatchesRegularExpression('/^iterable\</', $types[0], \sprintf('Data provider "%s::%s@return" must return iterable.', $testClassName, $dataProviderName));
        self::assertMatchesRegularExpression('/^iterable\<(?:(?:int\|)?string, )?array\{/', $types[0], \sprintf('Data provider "%s::%s@return" must return iterable of tuples (eg `iterable<string, array{string, string}>`).', $testClassName, $dataProviderName));
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
            T_ABSTRACT,
            T_AS,
            T_COMMENT,
            T_DECLARE,
            T_DOC_COMMENT,
            T_FINAL,
            T_LNUMBER,
            T_NAMESPACE,
            T_NS_SEPARATOR,
            T_OPEN_TAG,
            T_STRING,
            T_USE,
            T_WHITESPACE,
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

            if (\defined('T_ATTRIBUTE') && $tokens[$index]->isGivenKind(T_ATTRIBUTE)) {
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
            static fn (\ReflectionMethod $rm): bool => false !== $rm->getDocComment() && stripos($rm->getDocComment(), '@inheritdoc')
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
     * @return iterable<array{string}>
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
     * @return iterable<array{string}>
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

    public function testAllTestsForShortOpenTagAreHandled(): void
    {
        $testClassesWithShortOpenTag = array_filter(
            self::getTestClasses(),
            fn (string $className): bool => str_contains($this->getFileContentForClass($className), 'short_open_tag') && self::class !== $className
        );
        $testFilesWithShortOpenTag = array_map(
            fn (string $className): string => './'.$this->getFilePathForClass($className),
            $testClassesWithShortOpenTag
        );

        $phpunitXmlContent = file_get_contents(__DIR__.'/../../phpunit.xml.dist');
        $phpunitFiles = (array) simplexml_load_string($phpunitXmlContent)->xpath('testsuites/testsuite[@name="short-open-tag"]')[0]->file;

        sort($testFilesWithShortOpenTag);
        sort($phpunitFiles);
        self::assertSame($testFilesWithShortOpenTag, $phpunitFiles);
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param class-string $className
     */
    public function testThatTestMethodsAreNotDuplicated(string $className): void
    {
        $class = new \ReflectionClass($className);

        $alreadyFoundMethods = [];
        $duplicates = [];
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!str_starts_with($method->getName(), 'test')) {
                continue;
            }

            $startLine = (int) $method->getStartLine();
            $length = (int) $method->getEndLine() - $startLine;
            if (3 === $length) { // open and closing brace are included - this checks for single line methods
                continue;
            }

            /** @var list<string> $source */
            $source = file((string) $method->getFileName());

            $candidateContent = implode('', \array_slice($source, $startLine, $length));
            if (str_contains($candidateContent, '$this->doTest(')) {
                continue;
            }

            $foundInDuplicates = false;
            foreach ($alreadyFoundMethods as $methodKey => $methodContent) {
                if ($candidateContent === $methodContent) {
                    $duplicates[] = \sprintf('%s is duplicate of %s', $methodKey, $method->getName());
                    $foundInDuplicates = true;
                }
            }
            if (!$foundInDuplicates) {
                $alreadyFoundMethods[$method->getName()] = $candidateContent;
            }
        }

        self::assertSame(
            [],
            $duplicates,
            \sprintf(
                "Duplicated methods found in %s:\n - %s",
                $className,
                implode("\n - ", $duplicates)
            )
        );
    }

    /**
     * @dataProvider provideDataProviderMethodCases
     *
     * @param class-string<TestCase> $testClassName
     */
    public function testThatDataFromDataProvidersIsNotDuplicated(string $testClassName, string $dataProviderName): void
    {
        $exceptions = [ // should only shrink
            'PhpCsFixer\Tests\AutoReview\CommandTest::provideCommandHasNameConstCases',
            'PhpCsFixer\Tests\AutoReview\DocumentationTest::provideFixerDocumentationFileIsUpToDateCases',
            'PhpCsFixer\Tests\AutoReview\FixerFactoryTest::providePriorityIntegrationTestFilesAreListedInPriorityGraphCases',
            'PhpCsFixer\Tests\Console\Command\DescribeCommandTest::provideExecuteOutputCases',
            'PhpCsFixer\Tests\Console\Command\HelpCommandTest::provideGetDisplayableAllowedValuesCases',
            'PhpCsFixer\Tests\Documentation\FixerDocumentGeneratorTest::provideGenerateRuleSetsDocumentationCases',
            'PhpCsFixer\Tests\Fixer\Basic\EncodingFixerTest::provideFixCases',
            'PhpCsFixer\Tests\UtilsTest::provideStableSortCases',
        ];
        if (\in_array($testClassName.'::'.$dataProviderName, $exceptions, true)) {
            $this->addToAssertionCount(1);

            return;
        }

        $dataProvider = new \ReflectionMethod($testClassName, $dataProviderName);

        $duplicates = [];
        $alreadyFoundCases = [];
        foreach ($dataProvider->invoke($dataProvider->getDeclaringClass()->newInstanceWithoutConstructor()) as $candidateKey => $candidateData) {
            $candidateData = serialize($candidateData);
            $foundInDuplicates = false;
            foreach ($alreadyFoundCases as $caseKey => $caseData) {
                if ($candidateData === $caseData) {
                    $duplicates[] = \sprintf(
                        'Duplicate in %s::%s: %s and %s.'.PHP_EOL,
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

        self::assertSame([], $duplicates);
    }

    /**
     * @return iterable<string, array{class-string<TestCase>}>
     */
    public static function provideTestClassCases(): iterable
    {
        if (null === self::$testClassCases) {
            $cases = self::getTestClasses();

            self::$testClassCases = array_combine(
                $cases,
                array_map(static fn (string $case): array => [$case], $cases),
            );
        }

        yield from self::$testClassCases;
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideThereIsNoPregFunctionUsedDirectlyCases(): iterable
    {
        return array_map(
            static fn (string $item): array => [$item],
            array_filter(
                self::getSrcClasses(),
                static fn (string $className): bool => Preg::class !== $className,
            ),
        );
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
     * @return iterable<array{string}>
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
     * @param class-string $className
     *
     * @return list<string>
     */
    private function extractFunctionNamesCalledInClass(string $className): array
    {
        $tokens = $this->createTokensForClass($className);

        $stringTokens = array_filter(
            $tokens->toArray(),
            static fn (Token $token): bool => $token->isGivenKind(T_STRING)
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
        $file = preg_replace('#^PhpCsFixer\\\Tests\\\#', 'tests\\', $file);
        $file = preg_replace('#^PhpCsFixer\\\#', 'src\\', $file);

        return str_replace('\\', \DIRECTORY_SEPARATOR, $file).'.php';
    }

    /**
     * @param class-string $className
     */
    private function getFileContentForClass(string $className): string
    {
        return file_get_contents($this->getFilePathForClass($className));
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
            if (1 === preg_match('/@dataProvider\s+(?P<methodName>\w+)/', $dataProviderAnnotation->getContent(), $matches)) {
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
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $methodName = $tokens[$tokens->getNextTokenOfKind($index, [[T_STRING]])]->getContent();

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
     * @return list<class-string<TestCase>>
     */
    private static function getTestClasses(): array
    {
        static $classes;

        if (null !== $classes) {
            return $classes;
        }

        $finder = Finder::create()
            ->files()
            ->name('*Test.php')
            ->in(__DIR__.'/..')
            ->exclude([
                'Fixtures',
            ])
        ;

        /** @var list<class-string<TestCase>> $classes */
        $classes = array_map(
            static fn (SplFileInfo $file): string => \sprintf(
                'PhpCsFixer\Tests\%s%s%s',
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
