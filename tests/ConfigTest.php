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

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * @internal
 */
final class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testThatFinderWorksWithDirSetOnConfig()
    {
        $config = new Config();

        $iterator = $config->getFinder()->in(__DIR__.'/Fixtures/FinderDirectory')->getIterator();
        $this->assertSame(1, count($iterator));
        $iterator->rewind();
        $this->assertSame('somefile.php', $iterator->current()->getFilename());
    }

    public function testThatCustomFinderWorks()
    {
        $finder = new Finder();
        $finder->in(__DIR__.'/Fixtures/FinderDirectory');

        $config = Config::create()->finder($finder);

        $iterator = $config->getFinder()->getIterator();
        $this->assertSame(1, count($iterator));
        $iterator->rewind();
        $this->assertSame('somefile.php', $iterator->current()->getFilename());
    }

    public function testThatCustomSymfonyFinderWorks()
    {
        $finder = new SymfonyFinder();
        $finder->in(__DIR__.'/Fixtures/FinderDirectory');

        $config = Config::create()->finder($finder);

        $iterator = $config->getFinder()->getIterator();
        $this->assertSame(1, count($iterator));
        $iterator->rewind();
        $this->assertSame('somefile.php', $iterator->current()->getFilename());
    }

    public function testThatCacheFileHasDefaultValue()
    {
        $config = new Config();

        $this->assertSame('.php_cs.cache', $config->getCacheFile());
    }

    public function testThatCacheFileCanBeMutated()
    {
        $cacheFile = 'some-directory/some.file';

        $config = new Config();
        $config->setCacheFile($cacheFile);

        $this->assertSame($cacheFile, $config->getCacheFile());
    }

    public function testThatMutatorHasFluentInterface()
    {
        $config = new Config();

        $this->assertSame($config, $config->setCacheFile('some-directory/some.file'));
    }

    /**
     * @expectedException              \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^Argument must be an array or a Traversable, got "\w+"\.$/
     */
    public function testAddCustomFixersWithInvalidArgument()
    {
        $config = new Config();
        $config->addCustomFixers('foo');
    }

    /**
     * @dataProvider provideAddCustomFixersCases
     */
    public function testAddCustomFixers($expected, $suite)
    {
        $config = new Config();
        $config->addCustomFixers($suite);

        $this->assertSame($expected, $config->getCustomFixers());
    }

    /**
     * @return array
     */
    public function provideAddCustomFixersCases()
    {
        $fixers = array(
            new \PhpCsFixer\Fixer\Symfony\NoWhitespaceBeforeCommaInArrayFixer(),
            new \PhpCsFixer\Fixer\ControlStructure\IncludeFixer(),
        );

        $cases = array(
            array($fixers, $fixers),
            array($fixers, new \ArrayIterator($fixers)),
        );

        return $cases;
    }
}
