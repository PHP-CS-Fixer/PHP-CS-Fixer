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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Config;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Tests\RuleSet\SampleRulesBad;
use PhpCsFixer\Tests\RuleSet\SampleRulesOk;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Config
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ConfigTest extends TestCase
{
    public function testFutureMode(): void
    {
        $futureMode = getenv('PHP_CS_FIXER_FUTURE_MODE');
        putenv('PHP_CS_FIXER_FUTURE_MODE=1');

        $config = new Config('test');
        self::assertSame('test (future mode)', $config->getName());
        self::assertArrayHasKey('@PER-CS', $config->getRules());

        putenv('PHP_CS_FIXER_FUTURE_MODE='.$futureMode);
    }

    public function testConfigRulesUsingSeparateMethod(): void
    {
        $config = new Config();
        $configResolver = new ConfigurationResolver(
            $config,
            [
                'rules' => 'cast_spaces,statement_indentation',
            ],
            (string) getcwd(),
            new ToolInfo()
        );

        self::assertSame(
            [
                'cast_spaces' => true,
                'statement_indentation' => true,
            ],
            $configResolver->getRules()
        );
    }

    public function testConfigRulesUsingJsonMethod(): void
    {
        $config = new Config();
        $configResolver = new ConfigurationResolver(
            $config,
            [
                'rules' => '{"array_syntax": {"syntax": "short"}, "cast_spaces": true}',
            ],
            (string) getcwd(),
            new ToolInfo()
        );

        self::assertSame(
            [
                'array_syntax' => [
                    'syntax' => 'short',
                ],
                'cast_spaces' => true,
            ],
            $configResolver->getRules()
        );
    }

    public function testConfigRulesUsingInvalidJson(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = new Config();
        $configResolver = new ConfigurationResolver(
            $config,
            [
                'rules' => '{blah',
            ],
            (string) getcwd(),
            new ToolInfo()
        );
        $configResolver->getRules();
    }

    public function testCustomConfig(): void
    {
        $customConfigFile = __DIR__.'/Fixtures/.php-cs-fixer.custom.php';

        $application = new Application();
        $application->add(new FixCommand(new ToolInfo()));

        $commandTester = new CommandTester($application->find('fix'));

        $commandTester->execute(
            [
                'path' => [$customConfigFile],
                '--dry-run' => true,
                '--config' => $customConfigFile,
            ],
            [
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE,
            ]
        );
        self::assertStringMatchesFormat(
            \sprintf('%%ALoaded config custom_config_test from "%s".%%A', $customConfigFile),
            $commandTester->getDisplay(true)
        );
    }

    public function testThatFinderWorksWithDirSetOnConfig(): void
    {
        $config = new Config();

        $items = iterator_to_array(
            $config->getFinder()->in(__DIR__.'/Fixtures/FinderDirectory'),
            false
        );

        self::assertCount(1, $items);

        $item = reset($items);
        self::assertSame('somefile.php', $item->getFilename());
    }

    public function testThatCustomFinderWorks(): void
    {
        $finder = new Finder();
        $finder->in(__DIR__.'/Fixtures/FinderDirectory');

        $config = (new Config())->setFinder($finder);

        $items = iterator_to_array(
            $config->getFinder(),
            false
        );

        self::assertCount(1, $items);
        self::assertSame('somefile.php', $items[0]->getFilename());
    }

    public function testThatCustomSymfonyFinderWorks(): void
    {
        $finder = new SymfonyFinder();
        $finder->in(__DIR__.'/Fixtures/FinderDirectory');

        $config = (new Config())->setFinder($finder);

        $items = iterator_to_array(
            $config->getFinder(),
            false
        );

        self::assertCount(1, $items);
        self::assertSame('somefile.php', $items[0]->getFilename());
    }

    public function testThatCacheFileHasDefaultValue(): void
    {
        $config = new Config();

        self::assertSame('.php-cs-fixer.cache', $config->getCacheFile());
    }

    public function testThatCacheFileCanBeMutated(): void
    {
        $cacheFile = 'some-directory/some.file';

        $config = new Config();
        $config->setCacheFile($cacheFile);

        self::assertSame($cacheFile, $config->getCacheFile());
    }

    public function testThatMutatorHasFluentInterface(): void
    {
        $config = new Config();

        self::assertSame($config, $config->setCacheFile('some-directory/some.file'));
    }

    /**
     * @param list<FixerInterface>     $expected
     * @param iterable<FixerInterface> $suite
     *
     * @dataProvider provideRegisterCustomFixersCases
     */
    public function testRegisterCustomFixers(array $expected, iterable $suite): void
    {
        $config = new Config();
        $config->registerCustomFixers($suite);

        self::assertSame($expected, $config->getCustomFixers());
    }

    /**
     * @return iterable<int, array{list<FixerInterface>, iterable<FixerInterface>}>
     */
    public static function provideRegisterCustomFixersCases(): iterable
    {
        $fixers = [
            new NoWhitespaceBeforeCommaInArrayFixer(),
            new IncludeFixer(),
        ];

        yield [$fixers, $fixers];

        yield [$fixers, new \ArrayIterator($fixers)];
    }

    /**
     * @dataProvider provideRegisterCustomRuleSetsCases
     */
    public function testRegisterCustomRuleSets(?string $expectedException, array $ruleSets): void
    {
        if (!empty($expectedException)) {
            $this->expectException($expectedException);
        }

        $config = new Config();
        $config->registerCustomRuleSets($ruleSets);

        if (empty($expectedException)) {
            static::assertContains(array_keys($ruleSets)[0], RuleSets::getSetDefinitionNames());
        }
    }

    public function testConfigDefault(): void
    {
        $config = new Config();

        self::assertSame('.php-cs-fixer.cache', $config->getCacheFile());
        self::assertSame([], $config->getCustomFixers());
        self::assertSame('txt', $config->getFormat());
        self::assertFalse($config->getHideProgress());
        self::assertSame('    ', $config->getIndent());
        self::assertSame("\n", $config->getLineEnding());
        self::assertSame('default', $config->getName());
        self::assertNull($config->getPhpExecutable());
        self::assertFalse($config->getRiskyAllowed());
        self::assertSame(['@PSR12' => true], $config->getRules());
        self::assertTrue($config->getUsingCache());
        self::assertSame(filter_var(getenv('PHP_CS_FIXER_IGNORE_ENV'), \FILTER_VALIDATE_BOOL), $config->getUnsupportedPhpVersionAllowed());

        $finder = $config->getFinder();
        self::assertInstanceOf(Finder::class, $finder);

        $config->setFormat('xml');
        self::assertSame('xml', $config->getFormat());

        $config->setHideProgress(true);
        self::assertTrue($config->getHideProgress());

        $config->setIndent("\t");
        self::assertSame("\t", $config->getIndent());

        $finder = new Finder();
        $config->setFinder($finder);
        self::assertSame($finder, $config->getFinder());

        $config->setLineEnding("\r\n");
        self::assertSame("\r\n", $config->getLineEnding());

        $config->setPhpExecutable(null);
        self::assertNull($config->getPhpExecutable());

        $config->setUsingCache(false);
        self::assertFalse($config->getUsingCache());

        $config->setUnsupportedPhpVersionAllowed(true);
        self::assertTrue($config->getUnsupportedPhpVersionAllowed());
    }

    public function provideRegisterCustomRuleSetsCases(): array
    {
        $ruleSetGood = [
            '@RulesOk' => SampleRulesOk::class,
        ];

        $ruleSetBad = [
            '@RulesBad' => SampleRulesBad::class,
        ];

        return [
            [null, $ruleSetGood],
            [\InvalidArgumentException::class, $ruleSetBad],
        ];
    }

    public function testConfigConstructorWithName(): void
    {
        $anonymousConfig = new Config();
        $namedConfig = new Config('foo');

        self::assertSame($anonymousConfig->getName(), 'default');
        self::assertSame($namedConfig->getName(), 'foo');
    }

    public function testConfigWithDefaultParallelConfig(): void
    {
        $config = new Config();

        self::assertSame(1, $config->getParallelConfig()->getMaxProcesses());
    }

    public function testConfigWithExplicitParallelConfig(): void
    {
        $config = new Config();
        $config->setParallelConfig(new ParallelConfig(5, 10, 15));

        self::assertSame(5, $config->getParallelConfig()->getMaxProcesses());
        self::assertSame(10, $config->getParallelConfig()->getFilesPerProcess());
        self::assertSame(15, $config->getParallelConfig()->getProcessTimeout());
    }
}
