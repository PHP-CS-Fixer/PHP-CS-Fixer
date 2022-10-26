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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FileRemoval;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Linter\CachingLinter;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\ProcessLinter;
use PhpCsFixer\PhpunitConstraintIsIdenticalString\Constraint\IsIdenticalString;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Integration test base class.
 *
 * This test searches for '.test' fixture files in the given directory.
 * Each fixture file will be parsed and tested against the expected result.
 *
 * Fixture files have the following format:
 *
 * --TEST--
 * Example test description.
 * --RULESET--
 * {"@PSR2": true, "strict": true}
 * --CONFIG--*
 * {"indent": "    ", "lineEnding": "\n"}
 * --SETTINGS--*
 * {"key": "value"} # optional extension point for custom IntegrationTestCase class
 * --EXPECT--
 * Expected code after fixing
 * --INPUT--*
 * Code to fix
 *
 *   * Section or any line in it may be omitted.
 *  ** PHP minimum version. Default to current running php version (no effect).
 *
 * @internal
 */
abstract class AbstractIntegrationTestCase extends TestCase
{
    /**
     * @var null|LinterInterface
     */
    protected $linter;

    /**
     * @var null|FileRemoval
     */
    private static $fileRemoval;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $tmpFile = static::getTempFile();
        self::$fileRemoval = new FileRemoval();
        self::$fileRemoval->observe($tmpFile);

        if (!is_file($tmpFile)) {
            $dir = \dirname($tmpFile);

            if (!is_dir($dir)) {
                $fs = new Filesystem();
                $fs->mkdir($dir, 0766);
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $tmpFile = static::getTempFile();

        self::$fileRemoval->delete($tmpFile);
        self::$fileRemoval = null;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->linter = $this->getLinter();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->linter = null;
    }

    /**
     * @dataProvider provideIntegrationCases
     *
     * @see doTest()
     *
     * @large
     *
     * @group legacy
     */
    public function testIntegration(IntegrationCase $case): void
    {
        foreach ($case->getSettings()['deprecations'] as $deprecation) {
            $this->expectDeprecation($deprecation);
        }

        $this->doTest($case);
    }

    /**
     * Creates test data by parsing '.test' files.
     *
     * @return IntegrationCase[][]
     */
    public function provideIntegrationCases(): array
    {
        $dir = static::getFixturesDir();
        $fixturesDir = realpath($dir);

        if (!is_dir($fixturesDir)) {
            throw new \UnexpectedValueException(sprintf('Given fixture dir "%s" is not a directory.', \is_string($fixturesDir) ? $fixturesDir : $dir));
        }

        $factory = static::createIntegrationCaseFactory();
        $tests = [];

        /** @var SplFileInfo $file */
        foreach (Finder::create()->files()->in($fixturesDir) as $file) {
            if ('test' !== $file->getExtension()) {
                continue;
            }

            $tests[$file->getPathname()] = [
                $factory->create($file),
            ];
        }

        return $tests;
    }

    protected static function createIntegrationCaseFactory(): IntegrationCaseFactoryInterface
    {
        return new IntegrationCaseFactory();
    }

    /**
     * Returns the full path to directory which contains the tests.
     */
    protected static function getFixturesDir(): string
    {
        throw new \BadMethodCallException('Method "getFixturesDir" must be overridden by the extending class.');
    }

    /**
     * Returns the full path to the temporary file where the test will write to.
     */
    protected static function getTempFile(): string
    {
        throw new \BadMethodCallException('Method "getTempFile" must be overridden by the extending class.');
    }

    /**
     * Applies the given fixers on the input and checks the result.
     *
     * It will write the input to a temp file. The file will be fixed by a Fixer instance
     * configured with the given fixers. The result is compared with the expected output.
     * It checks if no errors were reported during the fixing.
     */
    protected function doTest(IntegrationCase $case): void
    {
        if (\PHP_VERSION_ID < $case->getRequirement('php')) {
            static::markTestSkipped(sprintf('PHP %d (or later) is required for "%s", current "%d".', $case->getRequirement('php'), $case->getFileName(), \PHP_VERSION_ID));
        }

        $input = $case->getInputCode();
        $expected = $case->getExpectedCode();

        $input = $case->hasInputCode() ? $input : $expected;

        $tmpFile = static::getTempFile();

        if (false === @file_put_contents($tmpFile, $input)) {
            throw new IOException(sprintf('Failed to write to tmp. file "%s".', $tmpFile));
        }

        $errorsManager = new ErrorsManager();
        $fixers = static::createFixers($case);
        $runner = new Runner(
            new \ArrayIterator([new \SplFileInfo($tmpFile)]),
            $fixers,
            new UnifiedDiffer(),
            null,
            $errorsManager,
            $this->linter,
            false,
            new NullCacheManager()
        );

        Tokens::clearCache();
        $result = $runner->fix();
        $changed = array_pop($result);

        if (!$errorsManager->isEmpty()) {
            $errors = $errorsManager->getExceptionErrors();
            static::assertEmpty($errors, sprintf('Errors reported during fixing of file "%s": %s', $case->getFileName(), $this->implodeErrors($errors)));

            $errors = $errorsManager->getInvalidErrors();
            static::assertEmpty($errors, sprintf('Errors reported during linting before fixing file "%s": %s.', $case->getFileName(), $this->implodeErrors($errors)));

            $errors = $errorsManager->getLintErrors();
            static::assertEmpty($errors, sprintf('Errors reported during linting after fixing file "%s": %s.', $case->getFileName(), $this->implodeErrors($errors)));
        }

        if (!$case->hasInputCode()) {
            static::assertEmpty(
                $changed,
                sprintf(
                    "Expected no changes made to test \"%s\" in \"%s\".\nFixers applied:\n%s.\nDiff.:\n%s.",
                    $case->getTitle(),
                    $case->getFileName(),
                    null === $changed ? '[None]' : implode(',', $changed['appliedFixers']),
                    null === $changed ? '[None]' : $changed['diff']
                )
            );

            return;
        }

        static::assertNotEmpty($changed, sprintf('Expected changes made to test "%s" in "%s".', $case->getTitle(), $case->getFileName()));
        $fixedInputCode = file_get_contents($tmpFile);
        static::assertThat(
            $fixedInputCode,
            new IsIdenticalString($expected),
            sprintf(
                "Expected changes do not match result for \"%s\" in \"%s\".\nFixers applied:\n%s.",
                $case->getTitle(),
                $case->getFileName(),
                implode(',', $changed['appliedFixers'])
            )
        );

        if (1 < \count($fixers)) {
            $tmpFile = static::getTempFile();
            if (false === @file_put_contents($tmpFile, $input)) {
                throw new IOException(sprintf('Failed to write to tmp. file "%s".', $tmpFile));
            }

            $runner = new Runner(
                new \ArrayIterator([new \SplFileInfo($tmpFile)]),
                array_reverse($fixers),
                new UnifiedDiffer(),
                null,
                $errorsManager,
                $this->linter,
                false,
                new NullCacheManager()
            );

            Tokens::clearCache();
            $runner->fix();
            $fixedInputCodeWithReversedFixers = file_get_contents($tmpFile);

            static::assertRevertedOrderFixing($case, $fixedInputCode, $fixedInputCodeWithReversedFixers);
        }

        // run the test again with the `expected` part, this should always stay the same
        $this->testIntegration(
            new IntegrationCase(
                $case->getFileName(),
                $case->getTitle().' "--EXPECT-- part run"',
                $case->getSettings(),
                $case->getRequirements(),
                $case->getConfig(),
                $case->getRuleset(),
                $case->getExpectedCode(),
                null
            )
        );
    }

    protected static function assertRevertedOrderFixing(IntegrationCase $case, string $fixedInputCode, string $fixedInputCodeWithReversedFixers): void
    {
        // If output is different depends on rules order - we need to verify that the rules are ordered by priority.
        // If not, any order is valid.
        if ($fixedInputCode !== $fixedInputCodeWithReversedFixers) {
            static::assertGreaterThan(
                1,
                \count(array_unique(array_map(
                    static function (FixerInterface $fixer): int {
                        return $fixer->getPriority();
                    },
                    static::createFixers($case)
                ))),
                sprintf(
                    'Rules priorities are not differential enough. If rules would be used in reverse order then final output would be different than the expected one. For that, different priorities must be set up for used rules to ensure stable order of them. In "%s".',
                    $case->getFileName()
                )
            );
        }
    }

    /**
     * @return FixerInterface[]
     */
    private static function createFixers(IntegrationCase $case): array
    {
        $config = $case->getConfig();

        return (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet($case->getRuleset())
            ->setWhitespacesConfig(
                new WhitespacesFixerConfig($config['indent'], $config['lineEnding'])
            )
            ->getFixers()
        ;
    }

    /**
     * @param Error[] $errors
     */
    private function implodeErrors(array $errors): string
    {
        $errorStr = '';
        foreach ($errors as $error) {
            $source = $error->getSource();
            $errorStr .= sprintf("%d: %s%s\n", $error->getType(), $error->getFilePath(), null === $source ? '' : ' '.$source->getMessage()."\n\n".$source->getTraceAsString());
        }

        return $errorStr;
    }

    private function getLinter(): LinterInterface
    {
        static $linter = null;

        if (null === $linter) {
            $linter = new CachingLinter(
                getenv('FAST_LINT_TEST_CASES') ? new Linter() : new ProcessLinter()
            );
        }

        return $linter;
    }
}
