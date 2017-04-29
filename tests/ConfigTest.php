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
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Config
 */
final class ConfigTest extends TestCase
{
    public function testConfigRulesUsingSeparateMethod()
    {
        $config = new Config();
        $configResolver = new ConfigurationResolver(
            $config, array(
                'rules' => 'cast_spaces,braces',
            ),
            getcwd()
        );

        $this->assertArraySubset(
            array(
                'cast_spaces' => true,
                'braces' => true,
            ),
            $configResolver->getRules()
        );
    }

    public function testConfigRulesUsingJsonMethod()
    {
        $config = new Config();
        $configResolver = new ConfigurationResolver(
            $config, array(
                'rules' => '{"array_syntax": {"syntax": "short"}, "cast_spaces": true}',
            ),
            getcwd()
        );

        $this->assertArraySubset(
            array(
                'array_syntax' => array(
                    'syntax' => 'short',
                ),
                'cast_spaces' => true,
            ),
            $configResolver->getRules()
        );
    }

    public function testConfigRulesUsingInvalidJson()
    {
        $this->setExpectedException('PhpCsFixer\ConfigurationException\InvalidConfigurationException');

        $config = new Config();
        $configResolver = new ConfigurationResolver(
            $config, array(
                'rules' => '{blah',
            ),
            getcwd()
        );
        $configResolver->getRules();
    }

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

        $items = iterator_to_array(
            $config->getFinder()->in(__DIR__.'/Fixtures/FinderDirectory'),
            false
        );

        $this->assertCount(1, $items);
        $this->assertSame('somefile.php', $items[0]->getFilename());
    }

    public function testThatCustomFinderWorks()
    {
        $finder = new Finder();
        $finder->in(__DIR__.'/Fixtures/FinderDirectory');

        $config = Config::create()->setFinder($finder);

        $items = iterator_to_array(
            $config->getFinder(),
            false
        );

        $this->assertCount(1, $items);
        $this->assertSame('somefile.php', $items[0]->getFilename());
    }

    public function testThatCustomSymfonyFinderWorks()
    {
        $finder = new SymfonyFinder();
        $finder->in(__DIR__.'/Fixtures/FinderDirectory');

        $config = Config::create()->setFinder($finder);

        $items = iterator_to_array(
            $config->getFinder(),
            false
        );

        $this->assertCount(1, $items);
        $this->assertSame('somefile.php', $items[0]->getFilename());
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

    public function testRegisterCustomFixersWithInvalidArgument()
    {
        $this->setExpectedExceptionRegExp(
            'InvalidArgumentException',
            '/^Argument must be an array or a Traversable, got "\w+"\.$/'
        );

        $config = new Config();
        $config->registerCustomFixers('foo');
    }

    /**
     * @param FixerInterface[] $expected
     * @param iterable         $suite
     *
     * @dataProvider provideRegisterCustomFixersCases
     */
    public function testRegisterCustomFixers(array $expected, $suite)
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
