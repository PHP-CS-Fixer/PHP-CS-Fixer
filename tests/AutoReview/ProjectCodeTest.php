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

use PhpCsFixer\DocBlock\DocBlock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
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
        \PhpCsFixer\Console\Command\SelfUpdateCommand::class,
        \PhpCsFixer\Console\Output\NullOutput::class,
        \PhpCsFixer\Differ\DiffConsoleFormatter::class,
        \PhpCsFixer\Doctrine\Annotation\Tokens::class,
        \PhpCsFixer\FileRemoval::class,
        \PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator::class,
        \PhpCsFixer\FixerFileProcessedEvent::class,
        \PhpCsFixer\Fixer\Operator\AlignDoubleArrowFixerHelper::class,
        \PhpCsFixer\Fixer\Operator\AlignEqualsFixerHelper::class,
        \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer::class,
        \PhpCsFixer\Indicator\PhpUnitIndicator::class,
        \PhpCsFixer\Linter\ProcessLintingResult::class,
        \PhpCsFixer\Linter\TokenizerLintingResult::class,
        \PhpCsFixer\Report\ReportSummary::class,
        \PhpCsFixer\Runner\FileCachingLintingIterator::class,
        \PhpCsFixer\Runner\FileFilterIterator::class,
        \PhpCsFixer\Runner\FileLintingIterator::class,
        \PhpCsFixer\StdinFileInfo::class,
        \PhpCsFixer\Test\Assert\AssertTokensTrait::class,
        \PhpCsFixer\Test\IntegrationCaseFactory::class,
        \PhpCsFixer\Tokenizer\Transformers::class,
    ];

    public function testThatClassesWithoutTestsVarIsProper()
    {
        $unknownClasses = array_filter(
            self::$classesWithoutTests,
            function ($class) { return !class_exists($class) && !trait_exists($class); }
        );
        $this->assertSame([], $unknownClasses);
    }

    /**
     * @param string $className
     *
     * @dataProvider provideSrcConcreteClasses
     */
    public function testThatSrcClassHaveTestClass($className)
    {
        $testClassName = str_replace('PhpCsFixer', 'PhpCsFixer\\Tests', $className).'Test';

        if (in_array($className, self::$classesWithoutTests, true)) {
            $this->assertFalse(class_exists($testClassName), sprintf('Class "%s" already has tests, so it should be removed from "%s::$classesWithoutTests".', $className, __CLASS__));
            $this->markTestIncomplete(sprintf('Class "%s" has no tests yet, please help and add it.', $className));
        }

        $this->assertTrue(class_exists($testClassName), sprintf('Expected test class "%s" for "%s" not found.', $testClassName, $className));
        $this->assertTrue(is_subclass_of($testClassName, TestCase::class), sprintf('Expected test class "%s" to be a subclass of "\PHPUnit\Framework\TestCase".', $testClassName));
    }

    /**
     * @param string $className
     *
     * @dataProvider provideSrcClasses
     */
    public function testThatSrcClassesNotAbuseInterfaces($className)
    {
        // HHVM knows better which interfaces you implements
        // https://github.com/facebook/hhvm/issues/5890
        if (defined('HHVM_VERSION') && interface_exists('Stringish')) {
            $this->markTestSkipped('Skipped as HHVM violate inheritance tree with `Stringish` interface.');
        }

        $rc = new \ReflectionClass($className);

        $doc = false !== $rc->getDocComment()
            ? new DocBlock($rc->getDocComment())
            : null;

        if (
            $rc->isInterface()
            || ($doc && count($doc->getAnnotationsOfType('internal')))
            || 0 === count($rc->getInterfaces())
            || in_array($className, [
                \PhpCsFixer\Finder::class,
                \PhpCsFixer\Test\AbstractFixerTestCase::class,
                \PhpCsFixer\Test\AbstractIntegrationTestCase::class,
                \PhpCsFixer\Tokenizer\Tokens::class,
            ], true)
        ) {
            return;
        }

        $allowedMethods = array_map(
            function (\ReflectionClass $interface) {
                return $this->getPublicMethodNames($interface);
            },
            $rc->getInterfaces()
        );

        if (count($allowedMethods)) {
            $allowedMethods = array_unique(array_merge(...array_values($allowedMethods)));
        }

        $allowedMethods[] = '__construct';
        $allowedMethods[] = '__destruct';
        $allowedMethods[] = '__wakeup';

        $exceptionMethods = [
            'configure', // due to AbstractFixer::configure
            'getConfigurationDefinition', // due to AbstractFixer::getDefaultConfiguration
            'getDefaultConfiguration', // due to AbstractFixer::getDefaultConfiguration
            'setWhitespacesConfig', // due to AbstractFixer::setWhitespacesConfig
        ];

        // @TODO: should be removed at 3.0
        $exceptionMethodsPerClass = [
            \PhpCsFixer\Config::class => ['create'],
            \PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer::class => ['fixSpace'],
        ];

        $definedMethods = $this->getPublicMethodNames($rc);

        $extraMethods = array_diff(
            $definedMethods,
            $allowedMethods,
            $exceptionMethods,
            isset($exceptionMethodsPerClass[$className]) ? $exceptionMethodsPerClass[$className] : []
        );

        sort($extraMethods);

        $this->assertEmpty(
            $extraMethods,
            sprintf(
                "Class '%s' should not have public methods that are not part of implemented interfaces.\nViolations:\n%s",
                $className,
                implode("\n", array_map(function ($item) {
                    return " * $item";
                }, $extraMethods))
            )
        );
    }

    /**
     * @param string $className
     *
     * @dataProvider provideSrcClasses
     */
    public function testThatSrcClassesNotExposeProperties($className)
    {
        $rc = new \ReflectionClass($className);

        if (\PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer::class === $className) {
            $this->markTestIncomplete(sprintf(
                'Public properties of fixer `%s` will be remove on 3.0.',
                \PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer::class
            ));
        }

        $this->assertEmpty(
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

        $allowedProps = array_map(function (\ReflectionProperty $item) {
            return $item->getName();
        }, $allowedProps);
        $definedProps = array_map(function (\ReflectionProperty $item) {
            return $item->getName();
        }, $definedProps);

        $exceptionPropsPerClass = [
            \PhpCsFixer\AbstractPhpdocTypesFixer::class => ['tags'],
            \PhpCsFixer\AbstractAlignFixerHelper::class => ['deepestLevel'],
            \PhpCsFixer\AbstractFixer::class => ['configuration', 'configurationDefinition', 'whitespacesConfig'],
            \PhpCsFixer\AbstractProxyFixer::class => ['proxyFixer'],
            \PhpCsFixer\Test\AbstractFixerTestCase::class => ['fixer', 'linter'],
            \PhpCsFixer\Test\AbstractIntegrationTestCase::class => ['linter'],
        ];

        $extraProps = array_diff(
            $definedProps,
            $allowedProps,
            isset($exceptionPropsPerClass[$className]) ? $exceptionPropsPerClass[$className] : []
        );

        sort($extraProps);

        $this->assertEmpty(
            $extraProps,
            sprintf(
                "Class '%s' should not have protected properties.\nViolations:\n%s",
                $className,
                implode("\n", array_map(function ($item) {
                    return " * $item";
                }, $extraProps))
            )
        );
    }

    /**
     * @param string $className
     *
     * @dataProvider provideTestClasses
     */
    public function testThatTestClassesAreAbstractOrFinal($className)
    {
        $rc = new \ReflectionClass($className);

        $this->assertTrue(
            $rc->isAbstract() || $rc->isFinal(),
            sprintf('Test class %s should be abstract or final.', $className)
        );
    }

    /**
     * @param string $className
     *
     * @dataProvider provideTestClasses
     */
    public function testThatTestClassesAreInternal($className)
    {
        $rc = new \ReflectionClass($className);
        $doc = new DocBlock($rc->getDocComment());

        $this->assertNotEmpty(
            $doc->getAnnotationsOfType('internal'),
            sprintf('Test class %s should have internal annotation.', $className)
        );
    }

    public function provideSrcClasses()
    {
        return array_map(
            function ($item) {
                return [$item];
            },
            $this->getSrcClasses()
        );
    }

    public function provideSrcConcreteClasses()
    {
        return array_map(
            function ($item) { return [$item]; },
            array_filter(
                $this->getSrcClasses(),
                function ($className) {
                    $rc = new \ReflectionClass($className);

                    return !$rc->isAbstract() && !$rc->isInterface();
                }
            )
        );
    }

    public function provideTestClasses()
    {
        return array_map(
            function ($item) {
                return [$item];
            },
            $this->getTestClasses()
        );
    }

    private function getSrcClasses()
    {
        static $files;

        if (null !== $files) {
            return $files;
        }

        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__.'/../../src')
            ->exclude([
                'Resources',
            ])
        ;

        $names = array_map(
            function (SplFileInfo $file) {
                return sprintf(
                    '%s\\%s%s%s',
                    'PhpCsFixer',
                    strtr($file->getRelativePath(), DIRECTORY_SEPARATOR, '\\'),
                    $file->getRelativePath() ? '\\' : '',
                    $file->getBasename('.'.$file->getExtension())
                );
            },
            iterator_to_array($finder, false)
        );

        sort($names);

        return $names;
    }

    private function getTestClasses()
    {
        static $files;

        if (null !== $files) {
            return $files;
        }

        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__.'/..')
            ->exclude([
                'Fixtures',
            ])
        ;

        $names = array_map(
            function (SplFileInfo $file) {
                return sprintf(
                    'PhpCsFixer\\Tests\\%s%s%s',
                    strtr($file->getRelativePath(), DIRECTORY_SEPARATOR, '\\'),
                    $file->getRelativePath() ? '\\' : '',
                    $file->getBasename('.'.$file->getExtension())
                );
            },
            iterator_to_array($finder, false)
        );

        sort($names);

        return $names;
    }

    /**
     * @param \ReflectionClass $rc
     *
     * @return string[]
     */
    private function getPublicMethodNames(\ReflectionClass $rc)
    {
        return array_map(
            function (\ReflectionMethod $rm) {
                return $rm->getName();
            },
            $rc->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
    }
}
