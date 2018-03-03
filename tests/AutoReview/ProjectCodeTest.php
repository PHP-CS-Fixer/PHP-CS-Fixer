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
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
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
    private static $classesWithoutTests = array(
        'PhpCsFixer\Console\SelfUpdate\GithubClient',
        'PhpCsFixer\Console\WarningsDetector',
        'PhpCsFixer\Doctrine\Annotation\Tokens',
        'PhpCsFixer\FileReader',
        'PhpCsFixer\FileRemoval',
        'PhpCsFixer\Fixer\Operator\AlignDoubleArrowFixerHelper',
        'PhpCsFixer\Fixer\Operator\AlignEqualsFixerHelper',
        'PhpCsFixer\Runner\FileCachingLintingIterator',
        'PhpCsFixer\Runner\FileLintingIterator',
        'PhpCsFixer\StdinFileInfo',
        'PhpCsFixer\Tokenizer\Transformers',
    );

    public function testThatClassesWithoutTestsVarIsProper()
    {
        $unknownClasses = array_filter(self::$classesWithoutTests, function ($class) { return !class_exists($class); });
        $this->assertSame(array(), $unknownClasses);
    }

    /**
     * @param string $className
     *
     * @dataProvider provideSrcConcreteClassCases
     */
    public function testThatSrcClassHaveTestClass($className)
    {
        $testClassName = str_replace('PhpCsFixer', 'PhpCsFixer\\Tests', $className).'Test';

        if (in_array($className, self::$classesWithoutTests, true)) {
            $this->assertFalse(class_exists($testClassName), sprintf('Class "%s" already has tests, so it should be removed from "%s::$classesWithoutTests".', $className, __CLASS__));
            $this->markTestIncomplete(sprintf('Class "%s" has no tests yet, please help and add it.', $className));
        }

        $this->assertTrue(class_exists($testClassName), sprintf('Expected test class "%s" for "%s" not found.', $testClassName, $className));
        $this->assertTrue(is_subclass_of($testClassName, '\PhpCsFixer\Tests\TestCase'), sprintf('Expected test class "%s" to be a subclass of "\PhpCsFixer\Tests\TestCase".', $testClassName));
    }

    /**
     * @param string $className
     *
     * @dataProvider provideSrcClassesNotAbuseInterfacesCases
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
            'getConfigurationDefinition', // due to AbstractFixer::getConfigurationDefinition
            'getDefaultConfiguration', // due to AbstractFixer::getDefaultConfiguration
            'setWhitespacesConfig', // due to AbstractFixer::setWhitespacesConfig
        );

        // @TODO: should be removed at 3.0
        $exceptionMethodsPerClass = array(
            'PhpCsFixer\Config' => array('create'),
            'PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer' => array('fixSpace'),
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
                    return " * ${item}";
                }, $extraMethods))
            )
        );
    }

    /**
     * @param string $className
     *
     * @dataProvider provideSrcClassCases
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
                    return " * ${item}";
                }, $extraProps))
            )
        );
    }

    /**
     * @param string $className
     *
     * @dataProvider provideTestClassCases
     */
    public function testThatTestClassesAreAbstractOrFinal($className)
    {
        $rc = new \ReflectionClass($className);

        $this->assertTrue(
            $rc->isInterface() || // due to hhvm only, @TODO remove me whem hhvm support is dropped
            $rc->isAbstract() || $rc->isFinal(),
            sprintf('Test class %s should be abstract or final.', $className)
        );
    }

    /**
     * @param string $className
     *
     * @dataProvider provideTestClassCases
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

    /**
     * @dataProvider provideDataProviderMethodNameCases
     *
     * @param string $testClassName
     * @param string $dataProviderMethodName
     */
    public function testThatDataProvidersAreCorrectlyNamed($testClassName, $dataProviderMethodName)
    {
        $this->assertRegExp('/^provide[A-Z]\S+Cases$/', $dataProviderMethodName, sprintf(
            'Data provider in "%s" with name "%s" is not correctly named.',
            $testClassName,
            $dataProviderMethodName
        ));
    }

    /**
     * @dataProvider provideClassesWherePregFunctionsAreForbiddenCases
     *
     * @param string $className
     */
    public function testThereIsNoPregFunctionUsedDirectly($className)
    {
        $rc = new \ReflectionClass($className);
        $tokens = Tokens::fromCode(file_get_contents($rc->getFileName()));
        $stringTokens = array_filter(
            $tokens->toArray(),
            function (Token $token) {
                return $token->isGivenKind(T_STRING);
            }
        );
        $strings = array_map(
            function (Token $token) {
                return $token->getContent();
            },
            $stringTokens
        );
        $strings = array_unique($strings);
        $message = sprintf('Class %s must not use preg_*, it shall use Preg::* instead.', $className);
        $this->assertNotContains('preg_filter', $strings, $message);
        $this->assertNotContains('preg_grep', $strings, $message);
        $this->assertNotContains('preg_match', $strings, $message);
        $this->assertNotContains('preg_match_all', $strings, $message);
        $this->assertNotContains('preg_replace', $strings, $message);
        $this->assertNotContains('preg_replace_callback', $strings, $message);
        $this->assertNotContains('preg_split', $strings, $message);
    }

    public function provideSrcClassCases()
    {
        return array_map(
            function ($item) {
                return array($item);
            },
            $this->getSrcClasses()
        );
    }

    public function provideSrcClassesNotAbuseInterfacesCases()
    {
        return array_map(
            function ($item) {
                return array($item);
            },
            array_filter($this->getSrcClasses(), function ($className) {
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
                    return false;
                }

                return true;
            })
        );
    }

    public function provideSrcConcreteClassCases()
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

    public function provideTestClassCases()
    {
        return array_map(
            function ($item) {
                return array($item);
            },
            $this->getTestClasses()
        );
    }

    public function provideDataProviderMethodNameCases()
    {
        if (extension_loaded('xdebug') && false === getenv('CI')) {
            $this->markTestSkipped('Data provider too slow when Xdebug is loaded.');
        }

        $data = array();

        $testClassNames = $this->getTestClasses();

        foreach ($testClassNames as $testClassName) {
            $dataProviderMethodNames = array();
            $tokens = Tokens::fromCode(file_get_contents(
                str_replace('\\', DIRECTORY_SEPARATOR, preg_replace('#^PhpCsFixer\\\Tests#', 'tests', $testClassName)).'.php'
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

            $dataProviderMethodNames = array_unique($dataProviderMethodNames);

            foreach ($dataProviderMethodNames as $dataProviderMethodName) {
                $data[] = array(
                    $testClassName,
                    $dataProviderMethodName,
                );
            }
        }

        return $data;
    }

    public function provideClassesWherePregFunctionsAreForbiddenCases()
    {
        if (extension_loaded('xdebug') && false === getenv('CI')) {
            $this->markTestSkipped('Test too slow when Xdebug is loaded.');
        }

        return array_map(
            function ($item) {
                return array($item);
            },
            array_filter(
                $this->getSrcClasses(),
                function ($className) {
                    return 'PhpCsFixer\\Preg' !== $className;
                }
            )
        );
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
            ->exclude(array(
                'Resources',
            ))
        ;

        $classes = array_map(
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
            ->exclude(array(
                'Fixtures',
            ))
        ;

        $classes = array_map(
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

        sort($classes);

        return $classes;
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
