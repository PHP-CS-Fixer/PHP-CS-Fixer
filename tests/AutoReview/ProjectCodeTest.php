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

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class ProjectCodeTest extends TestCase
{
    /**
     * This structure contains older classes that are not yet covered by tests.
     *
     * It may only shrink, never add anything to it.
     *
     * @var string[]
     */
    private static $classesWithoutTests = [
        \PhpCsFixer\Console\Command\DocumentationCommand::class,
        \PhpCsFixer\Console\SelfUpdate\GithubClient::class,
        \PhpCsFixer\Doctrine\Annotation\Tokens::class,
        \PhpCsFixer\Documentation\DocumentationGenerator::class,
        \PhpCsFixer\Runner\FileCachingLintingIterator::class,
    ];

    public function testThatClassesWithoutTestsVarIsProper(): void
    {
        $unknownClasses = array_filter(
            self::$classesWithoutTests,
            static function (string $class) { return !class_exists($class) && !trait_exists($class); }
        );

        static::assertSame([], $unknownClasses);
    }

    /**
     * @dataProvider provideSrcConcreteClassCases
     */
    public function testThatSrcClassHaveTestClass(string $className): void
    {
        $testClassName = 'PhpCsFixer\\Tests'.substr($className, 10).'Test';

        if (\in_array($className, self::$classesWithoutTests, true)) {
            static::assertFalse(class_exists($testClassName), sprintf('Class "%s" already has tests, so it should be removed from "%s::$classesWithoutTests".', $className, __CLASS__));
            static::markTestIncomplete(sprintf('Class "%s" has no tests yet, please help and add it.', $className));
        }

        static::assertTrue(class_exists($testClassName), sprintf('Expected test class "%s" for "%s" not found.', $testClassName, $className));
        static::assertTrue(is_subclass_of($testClassName, TestCase::class), sprintf('Expected test class "%s" to be a subclass of "\PhpCsFixer\Tests\TestCase".', $testClassName));
    }

    /**
     * @dataProvider provideSrcClassesNotAbuseInterfacesCases
     */
    public function testThatSrcClassesNotAbuseInterfaces(string $className): void
    {
        $rc = new \ReflectionClass($className);

        $allowedMethods = array_map(
            function (\ReflectionClass $interface) {
                return $this->getPublicMethodNames($interface);
            },
            $rc->getInterfaces()
        );

        if (\count($allowedMethods)) {
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
        ];

        $definedMethods = $this->getPublicMethodNames($rc);

        $extraMethods = array_diff(
            $definedMethods,
            $allowedMethods,
            $exceptionMethods
        );

        sort($extraMethods);

        static::assertEmpty(
            $extraMethods,
            sprintf(
                "Class '%s' should not have public methods that are not part of implemented interfaces.\nViolations:\n%s",
                $className,
                implode("\n", array_map(static function (string $item) {
                    return " * {$item}";
                }, $extraMethods))
            )
        );
    }

    /**
     * @dataProvider provideSrcClassCases
     */
    public function testThatSrcClassesNotExposeProperties(string $className): void
    {
        $rc = new \ReflectionClass($className);

        static::assertEmpty(
            $rc->getProperties(\ReflectionProperty::IS_PUBLIC),
            sprintf('Class \'%s\' should not have public properties.', $className)
        );

        if ($rc->isFinal()) {
            return;
        }

        $allowedProps = [];
        $definedProps = $rc->getProperties(\ReflectionProperty::IS_PROTECTED);

        if (false !== $rc->getParentClass()) {
            $allowedProps = $rc->getParentClass()->getProperties(\ReflectionProperty::IS_PROTECTED);
        }

        $allowedProps = array_map(static function (\ReflectionProperty $item) {
            return $item->getName();
        }, $allowedProps);

        $definedProps = array_map(static function (\ReflectionProperty $item) {
            return $item->getName();
        }, $definedProps);

        $exceptionPropsPerClass = [
            \PhpCsFixer\AbstractPhpdocTypesFixer::class => ['tags'],
            \PhpCsFixer\AbstractFixer::class => ['configuration', 'configurationDefinition', 'whitespacesConfig'],
            \PhpCsFixer\AbstractProxyFixer::class => ['proxyFixers'],
        ];

        $extraProps = array_diff(
            $definedProps,
            $allowedProps,
            $exceptionPropsPerClass[$className] ?? []
        );

        sort($extraProps);

        static::assertEmpty(
            $extraProps,
            sprintf(
                "Class '%s' should not have protected properties.\nViolations:\n%s",
                $className,
                implode("\n", array_map(static function (string $item) {
                    return " * {$item}";
                }, $extraProps))
            )
        );
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testThatTestClassesAreTraitOrAbstractOrFinal(string $testClassName): void
    {
        $rc = new \ReflectionClass($testClassName);

        static::assertTrue(
            $rc->isTrait() || $rc->isAbstract() || $rc->isFinal(),
            sprintf('Test class %s should be trait, abstract or final.', $testClassName)
        );
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testThatTestClassesAreInternal(string $testClassName): void
    {
        $rc = new \ReflectionClass($testClassName);
        $doc = new DocBlock($rc->getDocComment());

        static::assertNotEmpty(
            $doc->getAnnotationsOfType('internal'),
            sprintf('Test class %s should have internal annotation.', $testClassName)
        );
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testThatPublicMethodsAreCorrectlyNamed(string $testClassName): void
    {
        $reflectionClass = new \ReflectionClass($testClassName);

        $publicMethods = array_filter(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
            static function (\ReflectionMethod $reflectionMethod) use ($reflectionClass) {
                return $reflectionMethod->getDeclaringClass()->getName() === $reflectionClass->getName();
            }
        );

        if ([] === $publicMethods) {
            $this->addToAssertionCount(1); // no methods to test, all good!
        }

        foreach ($publicMethods as $method) {
            static::assertMatchesRegularExpression(
                '/^(test|expect|provide|setUpBeforeClass$|tearDownAfterClass$)/',
                $method->getName(),
                sprintf('Public method "%s::%s" is not properly named.', $reflectionClass->getName(), $method->getName())
            );
        }
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testThatDataProvidersAreCorrectlyNamed(string $testClassName): void
    {
        $usedDataProviderMethodNames = $this->getUsedDataProviderMethodNames($testClassName);

        if (empty($usedDataProviderMethodNames)) {
            $this->addToAssertionCount(1); // no data providers to test, all good!

            return;
        }

        foreach ($usedDataProviderMethodNames as $dataProviderMethodName) {
            static::assertMatchesRegularExpression('/^provide[A-Z]\S+Cases$/', $dataProviderMethodName, sprintf(
                'Data provider in "%s" with name "%s" is not correctly named.',
                $testClassName,
                $dataProviderMethodName
            ));
        }
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testThatDataProvidersAreUsed(string $testClassName): void
    {
        $reflectionClass = new \ReflectionClass($testClassName);

        $definedDataProviders = array_filter(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
            static function (\ReflectionMethod $reflectionMethod) use ($reflectionClass) {
                return $reflectionMethod->getDeclaringClass()->getName() === $reflectionClass->getName()
                    && 'provide' === substr($reflectionMethod->getName(), 0, 7);
            }
        );

        if ([] === $definedDataProviders) {
            $this->addToAssertionCount(1); // no methods to test, all good!
        }

        $usedDataProviderMethodNames = $this->getUsedDataProviderMethodNames($testClassName);

        foreach ($definedDataProviders as $definedDataProvider) {
            static::assertContains(
                $definedDataProvider->getName(),
                $usedDataProviderMethodNames,
                sprintf('Data provider in "%s" with name "%s" is not used.', $definedDataProvider->getDeclaringClass()->getName(), $definedDataProvider->getName())
            );
        }
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testThatTestClassCoversAreCorrect(string $testClassName): void
    {
        $reflectionClass = new \ReflectionClass($testClassName);

        if ($reflectionClass->isAbstract() || $reflectionClass->isInterface()) {
            self::addToAssertionCount(1);

            return;
        }

        $doc = $reflectionClass->getDocComment();
        static::assertNotFalse($doc);

        if (1 === Preg::match('/@coversNothing/', $doc, $matches)) {
            self::addToAssertionCount(1);

            return;
        }

        $covers = Preg::match('/@covers (\S*)/', $doc, $matches);
        static::assertNotFalse($covers, sprintf('Missing @covers in PHPDoc of test class "%s".', $testClassName));

        array_shift($matches);
        $class = '\\'.str_replace('PhpCsFixer\Tests\\', 'PhpCsFixer\\', substr($testClassName, 0, -4));
        $parentClass = (new \ReflectionClass($class))->getParentClass();
        $parentClassName = false === $parentClass ? null : '\\'.$parentClass->getName();

        foreach ($matches as $match) {
            if ($match === $class || $parentClassName === $match) {
                $this->addToAssertionCount(1);

                continue;
            }

            static::fail(sprintf('Unexpected @covers "%s" for "%s".', $match, $testClassName));
        }
    }

    /**
     * @dataProvider provideClassesWherePregFunctionsAreForbiddenCases
     */
    public function testThereIsNoPregFunctionUsedDirectly(string $className): void
    {
        $rc = new \ReflectionClass($className);
        $tokens = Tokens::fromCode(file_get_contents($rc->getFileName()));
        $stringTokens = array_filter(
            $tokens->toArray(),
            static function (Token $token) {
                return $token->isGivenKind(T_STRING);
            }
        );

        $strings = array_map(
            static function (Token $token) {
                return $token->getContent();
            },
            $stringTokens
        );

        $strings = array_unique($strings);
        $message = sprintf('Class %s must not use preg_*, it shall use Preg::* instead.', $className);
        static::assertNotContains('preg_filter', $strings, $message);
        static::assertNotContains('preg_grep', $strings, $message);
        static::assertNotContains('preg_match', $strings, $message);
        static::assertNotContains('preg_match_all', $strings, $message);
        static::assertNotContains('preg_replace', $strings, $message);
        static::assertNotContains('preg_replace_callback', $strings, $message);
        static::assertNotContains('preg_split', $strings, $message);
    }

    /**
     * @dataProvider provideTestClassCases
     */
    public function testExpectedInputOrder(string $testClassName): void
    {
        $reflectionClass = new \ReflectionClass($testClassName);

        $publicMethods = array_filter(
            $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC),
            static function (\ReflectionMethod $reflectionMethod) use ($reflectionClass) {
                return $reflectionMethod->getDeclaringClass()->getName() === $reflectionClass->getName();
            }
        );

        if ([] === $publicMethods) {
            $this->addToAssertionCount(1); // no methods to test, all good!

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

            $expected = array_filter($expected);

            if (\count($expected) < 2) {
                $this->addToAssertionCount(1); // not enough parameters to test, all good!

                continue;
            }

            static::assertLessThan(
                $expected['input'],
                $expected['expected'],
                sprintf('Public method "%s::%s" has parameter \'input\' before \'expected\'.', $reflectionClass->getName(), $method->getName())
            );
        }
    }

    /**
     * @dataProvider provideSrcClassCases
     * @dataProvider provideTestClassCases
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

        $rc = new \ReflectionClass($className);
        $file = $rc->getFileName();
        $tokens = Tokens::fromCode(file_get_contents($file));
        $classyIndex = null;

        static::assertTrue($tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds()), sprintf('File "%s" should contains a classy.', $file));

        foreach ($tokens as $index => $token) {
            if ($token->isClassy()) {
                $classyIndex = $index;

                break;
            }

            if (!$token->isGivenKind($headerTypes) && !$token->equalsAny([';', '=', '(', ')'])) {
                static::fail(sprintf('File "%s" should only contains single classy, found "%s" @ %d.', $file, $token->toJson(), $index));
            }
        }

        static::assertNotNull($classyIndex, sprintf('File "%s" does not contain a classy.', $file));

        $nextTokenOfKind = $tokens->getNextTokenOfKind($classyIndex, ['{']);

        if (!\is_int($nextTokenOfKind)) {
            throw new \UnexpectedValueException('Classy without {} - braces.');
        }

        $classyEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $nextTokenOfKind);

        static::assertNull($tokens->getNextNonWhitespace($classyEndIndex), sprintf('File "%s" should only contains a single classy.', $file));
    }

    /**
     * @dataProvider provideSrcClassCases
     */
    public function testThereIsNoTriggerErrorUsedDirectly(string $className): void
    {
        if (Utils::class === $className) {
            $this->addToAssertionCount(1); // This is where "trigger_error" should be

            return;
        }

        $rc = new \ReflectionClass($className);
        $tokens = Tokens::fromCode(file_get_contents($rc->getFileName()));

        $triggerErrors = array_filter(
            $tokens->toArray(),
            static function (Token $token) {
                return $token->equals([T_STRING, 'trigger_error'], false);
            }
        );

        static::assertCount(
            0,
            $triggerErrors,
            sprintf('Class "%s" must not use "trigger_error", it shall use "Util::triggerDeprecation" instead.', $className)
        );
    }

    /**
     * @dataProvider provideSrcClassCases
     */
    public function testInheritdocIsNotAbused(string $className): void
    {
        $rc = new \ReflectionClass($className);

        $allowedMethods = array_map(
            function (\ReflectionClass $interface) {
                return $this->getPublicMethodNames($interface);
            },
            $rc->getInterfaces()
        );

        if (\count($allowedMethods)) {
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
            static function (\ReflectionMethod $rm) {
                return false !== $rm->getDocComment() && stripos($rm->getDocComment(), '@inheritdoc');
            }
        );

        $methodsWithInheritdoc = array_map(
            static function (\ReflectionMethod $rm) {
                return $rm->getName();
            },
            $methodsWithInheritdoc
        );

        $extraMethods = array_diff($methodsWithInheritdoc, $allowedMethods);

        static::assertEmpty(
            $extraMethods,
            sprintf(
                "Class '%s' should not have methods with '@inheritdoc' in PHPDoc that are not inheriting PHPDoc.\nViolations:\n%s",
                $className,
                implode("\n", array_map(static function ($item) {
                    return " * {$item}";
                }, $extraMethods))
            )
        );
    }

    public function provideSrcClassCases(): array
    {
        return array_map(
            static function (string $item) {
                return [$item];
            },
            $this->getSrcClasses()
        );
    }

    public function provideSrcClassesNotAbuseInterfacesCases(): array
    {
        return array_map(
            static function (string $item) {
                return [$item];
            },
            array_filter($this->getSrcClasses(), static function (string $className) {
                $rc = new \ReflectionClass($className);

                $doc = false !== $rc->getDocComment()
                    ? new DocBlock($rc->getDocComment())
                    : null;

                if (
                    $rc->isInterface()
                    || ($doc && \count($doc->getAnnotationsOfType('internal')))
                    || \in_array($className, [
                        \PhpCsFixer\Finder::class,
                        \PhpCsFixer\Tests\Test\AbstractFixerTestCase::class,
                        \PhpCsFixer\Tests\Test\AbstractIntegrationTestCase::class,
                        \PhpCsFixer\Tokenizer\Tokens::class,
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

                    if ('Stringable' === $interface->getName()) {
                        return false;
                    }
                }

                return true;
            })
        );
    }

    public function provideSrcConcreteClassCases(): array
    {
        return array_map(
            static function (string $item) { return [$item]; },
            array_filter(
                $this->getSrcClasses(),
                static function (string $className) {
                    $rc = new \ReflectionClass($className);

                    return !$rc->isAbstract() && !$rc->isInterface();
                }
            )
        );
    }

    public function provideTestClassCases(): array
    {
        return array_map(
            static function (string $item) {
                return [$item];
            },
            $this->getTestClasses()
        );
    }

    public function provideClassesWherePregFunctionsAreForbiddenCases(): array
    {
        return array_map(
            static function (string $item) {
                return [$item];
            },
            array_filter(
                $this->getSrcClasses(),
                static function (string $className) {
                    return Preg::class !== $className;
                }
            )
        );
    }

    /**
     * @dataProvider providePhpUnitFixerExtendsAbstractPhpUnitFixerCases
     */
    public function testPhpUnitFixerExtendsAbstractPhpUnitFixer(string $className): void
    {
        $reflection = new \ReflectionClass($className);

        static::assertTrue($reflection->isSubclassOf(\PhpCsFixer\Fixer\AbstractPhpUnitFixer::class));
    }

    public function providePhpUnitFixerExtendsAbstractPhpUnitFixerCases(): \Generator
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        foreach ($factory->getFixers() as $fixer) {
            if (0 !== strpos($fixer->getName(), 'php_unit_')) {
                continue;
            }

            // this one fixes usage of PHPUnit classes
            if ($fixer instanceof \PhpCsFixer\Fixer\PhpUnit\PhpUnitNamespacedFixer) {
                continue;
            }

            if ($fixer instanceof \PhpCsFixer\AbstractProxyFixer) {
                continue;
            }

            yield [\get_class($fixer)];
        }
    }

    /**
     * @dataProvider provideSrcClassCases
     * @dataProvider provideTestClassCases
     */
    public function testConstantsAreInUpperCase(string $className): void
    {
        $rc = new \ReflectionClass($className);

        $reflectionClassConstants = $rc->getReflectionConstants();

        if (\count($reflectionClassConstants) < 1) {
            $this->addToAssertionCount(1);

            return;
        }

        foreach ($reflectionClassConstants as $constant) {
            $constantName = $constant->getName();
            static::assertSame(strtoupper($constantName), $constantName, $className);
        }
    }

    private function getUsedDataProviderMethodNames(string $testClassName): array
    {
        $dataProviderMethodNames = [];
        $tokens = Tokens::fromCode(file_get_contents(
            str_replace('\\', \DIRECTORY_SEPARATOR, preg_replace('#^PhpCsFixer\\\Tests#', 'tests', $testClassName)).'.php'
        ));

        foreach ($tokens as $token) {
            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $docBlock = new DocBlock($token->getContent());
                $dataProviderAnnotations = $docBlock->getAnnotationsOfType('dataProvider');

                foreach ($dataProviderAnnotations as $dataProviderAnnotation) {
                    if (1 === preg_match('/@dataProvider\s+(?P<methodName>\w+)/', $dataProviderAnnotation->getContent(), $matches)) {
                        $dataProviderMethodNames[] = $matches['methodName'];
                    }
                }
            }
        }

        return array_unique($dataProviderMethodNames);
    }

    private function getSrcClasses()
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

        $classes = array_map(
            static function (SplFileInfo $file) {
                return sprintf(
                    '%s\\%s%s%s',
                    'PhpCsFixer',
                    strtr($file->getRelativePath(), \DIRECTORY_SEPARATOR, '\\'),
                    $file->getRelativePath() ? '\\' : '',
                    $file->getBasename('.'.$file->getExtension())
                );
            },
            iterator_to_array($finder, false)
        );

        sort($classes);

        return $classes;
    }

    private function getTestClasses()
    {
        static $classes;

        if (null !== $classes) {
            return $classes;
        }

        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__.'/..')
            ->exclude([
                'Fixtures',
            ])
        ;

        $classes = array_map(
            static function (SplFileInfo $file) {
                return sprintf(
                    'PhpCsFixer\\Tests\\%s%s%s',
                    strtr($file->getRelativePath(), \DIRECTORY_SEPARATOR, '\\'),
                    $file->getRelativePath() ? '\\' : '',
                    $file->getBasename('.'.$file->getExtension())
                );
            },
            iterator_to_array($finder, false)
        );

        $classes = array_filter($classes, static function (string $class): bool {
            // @phpstan-ignore-next-line due to false positive reported in https://github.com/phpstan/phpstan/issues/5369
            return is_subclass_of($class, TestCase::class);
        });

        sort($classes);

        return $classes;
    }

    /**
     * @return string[]
     */
    private function getPublicMethodNames(\ReflectionClass $rc): array
    {
        return array_map(
            static function (\ReflectionMethod $rm) {
                return $rm->getName();
            },
            $rc->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
    }
}
