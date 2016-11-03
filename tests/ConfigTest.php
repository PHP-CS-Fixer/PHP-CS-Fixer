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
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Finder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * @internal
 */
final class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCustomConfig()
    {
        $customConfigFile = __DIR__.'/Fixtures/.php_cs_custom.php';
        $command = new FixCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'path' => array($customConfigFile),
                '--dry-run' => true,
                '--config' => $customConfigFile,
            ),
            array(
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE,
            )
        );
        $this->assertStringMatchesFormat(
            sprintf('%%ALoaded config custom_config_test from "%s".%%A', $customConfigFile),
            $commandTester->getDisplay(true)
        );
    }

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

        $config = Config::create()->setFinder($finder);

        $iterator = $config->getFinder()->getIterator();
        $this->assertSame(1, count($iterator));
        $iterator->rewind();
        $this->assertSame('somefile.php', $iterator->current()->getFilename());
    }

    public function testThatCustomSymfonyFinderWorks()
    {
        $finder = new SymfonyFinder();
        $finder->in(__DIR__.'/Fixtures/FinderDirectory');

        $config = Config::create()->setFinder($finder);

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
    public function testRegisterCustomFixersWithInvalidArgument()
    {
        $config = new Config();
        $config->registerCustomFixers('foo');
    }

    /**
     * @dataProvider provideRegisterCustomFixersCases
     */
    public function testRegisterCustomFixers($expected, $suite)
    {
        $config = new Config();
        $config->registerCustomFixers($suite);

        $this->assertSame($expected, $config->getCustomFixers());
    }

    /**
     * @return array
     */
    public function provideRegisterCustomFixersCases()
    {
        $fixers = array(
            new \PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer(),
            new \PhpCsFixer\Fixer\ControlStructure\IncludeFixer(),
        );

        $cases = array(
            array($fixers, $fixers),
            array($fixers, new \ArrayIterator($fixers)),
        );

        return $cases;
    }
}
