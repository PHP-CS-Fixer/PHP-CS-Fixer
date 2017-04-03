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
final class ProjectCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This structure contains older classes that are not yet covered by tests.
     *
     * It may only shrink, never add anything to it.
     *
     * @var string[]
     */
    private static $classesWithoutTests = array(
        'PhpCsFixer\ConfigurationException\InvalidConfigurationException',
        'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
        'PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException',
        'PhpCsFixer\Console\Command\CommandHelp',
        'PhpCsFixer\Console\Command\DescribeNameNotFoundException',
        'PhpCsFixer\Console\Command\ReadmeCommand',
        'PhpCsFixer\Console\Command\SelfUpdateCommand',
        'PhpCsFixer\Console\Output\NullOutput',
        'PhpCsFixer\Differ\DiffConsoleFormatter',
        'PhpCsFixer\Differ\NullDiffer',
        'PhpCsFixer\Differ\SebastianBergmannDiffer',
        'PhpCsFixer\Doctrine\Annotation\Token',
        'PhpCsFixer\Doctrine\Annotation\Tokens',
        'PhpCsFixer\FileRemoval',
        'PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator',
        'PhpCsFixer\FixerDefinition\FileSpecificCodeSample',
        'PhpCsFixer\FixerFileProcessedEvent',
        'PhpCsFixer\Fixer\Operator\AlignDoubleArrowFixerHelper',
        'PhpCsFixer\Fixer\Operator\AlignEqualsFixerHelper',
        'PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer',
        'PhpCsFixer\Linter\LintingException',
        'PhpCsFixer\Linter\ProcessLintingResult',
        'PhpCsFixer\Linter\TokenizerLintingResult',
        'PhpCsFixer\Linter\UnavailableLinterException',
        'PhpCsFixer\Report\ReportSummary',
        'PhpCsFixer\Runner\FileCachingLintingIterator',
        'PhpCsFixer\Runner\FileFilterIterator',
        'PhpCsFixer\Runner\FileLintingIterator',
        'PhpCsFixer\StdinFileInfo',
        'PhpCsFixer\Test\IntegrationCaseFactory',
        'PhpCsFixer\Tokenizer\Transformers',
    );

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
        $this->assertTrue(is_subclass_of($testClassName, '\PHPUnit_Framework_TestCase'), sprintf('Expected test class "%s" to be a subclass of "\PHPUnit_Framework_TestCase".', $testClassName));
    }

    /**
     * @param string $className
     *
     * @dataProvider provideSrcClasses
     * @requires PHP 5.4
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
            || in_array($className, array(
                'PhpCsFixer\Finder',
                'PhpCsFixer\Test\AbstractFixerTestCase',
                'PhpCsFixer\Test\AbstractIntegrationTestCase',
                'PhpCsFixer\Tokenizer\Tokens',
            ), true)
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
            $allowedMethods = array_unique(call_user_func_array('array_merge', $allowedMethods));
        }

        $allowedMethods[] = '__construct';
        $allowedMethods[] = '__destruct';
        $allowedMethods[] = '__wakeup';

        $exceptionMethods = array(
            'configure', // due to AbstractFixer::configure
            'getConfigurationDefinition', // due to AbstractFixer::getDefaultConfiguration
            'getDefaultConfiguration', // due to AbstractFixer::getDefaultConfiguration
            'setWhitespacesConfig', // due to AbstractFixer::setWhitespacesConfig
        );

        // @TODO: should be removed at 3.0
        $exceptionMethodsPerClass = array(
            'PhpCsFixer\Config' => array('create'),
            'PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer' => array('fixSpace'),
            'PhpCsFixer\Fixer\Import\OrderedImportsFixer' => array('sortingCallBack'),
        );

        $definedMethods = $this->getPublicMethodNames($rc);

        $extraMethods = array_diff(
            $definedMethods,
            $allowedMethods,
            $exceptionMethods,
            isset($exceptionMethodsPerClass[$className]) ? $exceptionMethodsPerClass[$className] : array()
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

        if ('PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer' === $className) {
            $this->markTestIncomplete('Public properties of fixer \'PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer\' will be remove on 3.0.');
        }

        $this->assertEmpty(
            $rc->getProperties(\ReflectionProperty::IS_PUBLIC),
            sprintf('Class \'%s\' should not have public properties.', $className)
        );

        if ($rc->isFinal()) {
            return;
        }

        $allowedProps = array();
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

        $exceptionPropsPerClass = array(
            'PhpCsFixer\AbstractPhpdocTypesFixer' => array('tags'),
            'PhpCsFixer\AbstractAlignFixerHelper' => array('deepestLevel'),
            'PhpCsFixer\AbstractFixer' => array('configuration', 'configurationDefinition', 'whitespacesConfig'),
            'PhpCsFixer\AbstractProxyFixer' => array('proxyFixer'),
            'PhpCsFixer\Test\AbstractFixerTestCase' => array('fixer', 'linter'),
            'PhpCsFixer\Test\AbstractIntegrationTestCase' => array('linter'),
        );

        $extraProps = array_diff(
            $definedProps,
            $allowedProps,
            isset($exceptionPropsPerClass[$className]) ? $exceptionPropsPerClass[$className] : array()
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
                return array($item);
            },
            $this->getSrcClasses()
        );
    }

    public function provideSrcConcreteClasses()
    {
        return array_map(
            function ($item) { return array($item); },
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
                return array($item);
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
            ->exclude(array(
                'Resources',
            ))
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
            ->exclude(array(
                'Fixtures',
            ))
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
