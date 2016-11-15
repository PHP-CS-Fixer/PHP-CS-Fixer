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

namespace PhpCsFixer\Tests;

use PhpCsFixer\DocBlock\DocBlock;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
    public function testThatTestClassesAreAbstractOrFinal($className)
    {
        $rc = new \ReflectionClass($className);

        $this->assertTrue(
            $rc->isAbstract() || $rc->isFinal(),
            sprintf('Test class %s should be abstract or final.', $className)
        );
    }

    /**
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

    public function provideTestClasses()
    {
        return array_map(
            function ($item) {
                return array($item);
            },
            $this->getTestClasses()
        );
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
            ->in(__DIR__)
            ->exclude(array(
                'Fixtures',
            ))
        ;

        $files = array_map(
            function (SplFileInfo $file) {
                return sprintf(
                    '%s\\%s%s%s',
                    __NAMESPACE__,
                    strtr($file->getRelativePath(), DIRECTORY_SEPARATOR, '\\'),
                    $file->getRelativePath() ? '\\' : '',
                    $file->getBasename('.'.$file->getExtension())
                );
            },
            iterator_to_array($finder)
        );

        return $files;
    }
}
