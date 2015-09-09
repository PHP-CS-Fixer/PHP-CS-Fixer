<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\Component\Finder\Finder;
use Symfony\CS\DocBlock\DocBlock;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ProjectCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestClasses
     */
    public function testThatTestClassesAreAbstractOrFinal(\ReflectionClass $rc)
    {
        $this->assertTrue(
            $rc->isAbstract() || $rc->isFinal(),
            sprintf('Test class %s should be abstract or final.', $rc->getName())
        );
    }

    /**
     * @dataProvider provideTestClasses
     */
    public function testThatTestClassesAreInternal(\ReflectionClass $rc)
    {
        $doc = new DocBlock($rc->getDocComment());

        $this->assertNotEmpty(
            $doc->getAnnotationsOfType('internal'),
            sprintf('Test class %s should have internal annotation.', $rc->getName())
        );
    }

    public function provideTestClasses()
    {
        return array_map(
            function ($item) {
                return array(new \ReflectionClass($item));
            },
            $this->getClasses('Symfony\\CS\\Tests\\')
        );
    }

    private function getClasses($prefix, array $classes = null)
    {
        static $projectClasses = null;

        if (null === $classes && null === $projectClasses) {
            $this->registerAllProjectClasses();
            $projectClasses = $this->getClasses('Symfony\\CS\\', get_declared_classes());
        }

        if (null === $classes) {
            $classes = $projectClasses;
        }

        $prefixLength = strlen($prefix);

        return array_filter(
            $classes,
            function ($item) use ($prefix, $prefixLength) {
                return $prefix === substr($item, 0, $prefixLength);
            }
        );
    }

    private function registerAllProjectClasses()
    {
        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__.'/..')
            ->exclude(array(
                'Resources',
                'Tests/Fixtures',
            ))
        ;

        foreach ($finder as $file) {
            include_once $file;
        }
    }
}
