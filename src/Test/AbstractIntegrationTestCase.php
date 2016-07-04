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

namespace PhpCsFixer\Test;

use PhpCsFixer\Differ\SebastianBergmannDiffer;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\NullLinter;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\ShutdownFileRemoval;
use PhpCsFixer\Tokenizer\Transformers;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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
 * --CONFIG--
 * {"@PSR2": true, "strict": true}
 * --SETTINGS--*
 * checkPriority=true
 * --REQUIREMENTS--*
 * php=5.4**
 * hhvm=false***
 * --EXPECT--
 * Expected code after fixing
 * --INPUT--*
 * Code to fix
 *
 *   * Section or any line in it may be omitted.
 *  ** PHP minimum version. Default to current running php version (no effect).
 * *** HHVM compliant flag. Default to true. Set to false to skip test under HHVM.
 *
 * @author SpacePossum <possumfromspace@gmail.com>
 */
abstract class AbstractIntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinterInterface
     */
    protected static $linter;

    /*
     * @var ShutdownFileRemoval
     */
    private static $shutdownFileRemoval;

    public static function setUpBeforeClass()
    {
        static::$linter = getenv('LINT_TEST_CASES') ? new Linter() : new NullLinter();

        $tmpFile = static::getTempFile();
        self::$shutdownFileRemoval = new ShutdownFileRemoval();
        self::$shutdownFileRemoval->attach($tmpFile);

        if (!is_file($tmpFile)) {
            $dir = dirname($tmpFile);

            if (!is_dir($dir)) {
                $fs = new Filesystem();
                $fs->mkdir($dir, 0766);
            }
        }
    }

    public static function tearDownAfterClass()
    {
        $tmpFile = static::getTempFile();

        @unlink($tmpFile);
        self::$shutdownFileRemoval->detach($tmpFile);
    }

    /**
     * @dataProvider getTests
     *
     * @see doTest()
     */
    public function testIntegration(IntegrationCase $case)
    {
        $this->doTest($case);
    }

    /**
     * Creates test data by parsing '.test' files.
     *
     * @return IntegrationCase[][]
     */
    public function getTests()
    {
        Transformers::create();

        $fixturesDir = realpath(static::getFixturesDir());
        if (!is_dir($fixturesDir)) {
            throw new \UnexpectedValueException(sprintf('Given fixture dir "%s" is not a directory.', $fixturesDir));
        }

        $factory = new IntegrationCaseFactory();
        $tests = array();

        foreach (Finder::create()->files()->in($fixturesDir) as $file) {
            if ('test' !== $file->getExtension()) {
                continue;
            }

            $tests[] = array(
                $factory->create(
                    $file->getRelativePathname(),
                    file_get_contents($file->getRealpath())
                ),
            );
        }

        return $tests;
    }

    /**
     * Returns the full path to directory which contains the tests.
     *
     * @return string
     */
    protected static function getFixturesDir()
    {
        throw new \BadMethodCallException('Method "getFixturesDir" must be overridden by the extending class.');
    }

    /**
     * Returns the full path to the temporary file where the test will write to.
     *
     * @return string
     */
    protected static function getTempFile()
    {
        throw new \BadMethodCallException('Method "getTempFile" must be overridden by the extending class.');
    }

    /**
     * Applies the given fixers on the input and checks the result.
     *
     * It will write the input to a temp file. The file will be fixed by a Fixer instance
     * configured with the given fixers. The result is compared with the expected output.
     * It checks if no errors were reported during the fixing.
     *
     * @param IntegrationCase $case
     */
    protected function doTest(IntegrationCase $case)
    {
        if (defined('HHVM_VERSION') && false === $case->getRequirement('hhvm')) {
            $this->markTestSkipped('HHVM is not supported.');
        }

        if (version_compare(PHP_VERSION, $case->getRequirement('php')) < 0) {
            $this->markTestSkipped(sprintf('PHP %s (or later) is required.', $case->getRequirement('php')));
        }

        $input = $case->getInputCode();
        $expected = $case->getExpectedCode();

        $input = $case->hasInputCode() ? $input : $expected;

        $this->assertNull($this->lintSource($input));

        $tmpFile = static::getTempFile();

        if (false === @file_put_contents($tmpFile, $input)) {
            throw new IOException(sprintf('Failed to write to tmp. file "%s".', $tmpFile));
        }

        $errorsManager = new ErrorsManager();

        $configProphecy = $this->prophesize('PhpCsFixer\ConfigInterface');
        $configProphecy->usingCache()->willReturn(false);
        $configProphecy->getCacheFile()->willReturn(null);
        $configProphecy->usingLinter()->willReturn(false);
        $configProphecy->getRules()->willReturn(array());
        $configProphecy->getFinder()->willReturn(new \ArrayIterator(array(new \SplFileInfo($tmpFile))));
        $configProphecy->getFixers()->willReturn($case->getFixers());

        $runner = new Runner(
            $configProphecy->reveal(),
            new SebastianBergmannDiffer(),
            null,
            $errorsManager,
            new NullLinter(),
            false
        );

        $result = $runner->fix();
        $changed = array_pop($result);

        if (!$errorsManager->isEmpty()) {
            $errors = $errorsManager->getExceptionErrors();
            $this->assertEmpty($errors, sprintf('Errors reported during fixing of file "%s": %s', $case->getFileName(), $this->implodeErrors($errors)));

            $errors = $errorsManager->getInvalidErrors();
            $this->assertEmpty($errors, sprintf('Errors reported during linting before fixing file "%s": %s.', $case->getFileName(), $this->implodeErrors($errors)));

            $errors = $errorsManager->getLintErrors();
            $this->assertEmpty($errors, sprintf('Errors reported during linting after fixing file "%s": %s.', $case->getFileName(), $this->implodeErrors($errors)));
        }

        if (!$case->hasInputCode()) {
            $this->assertEmpty(
                $changed,
                sprintf(
                    "Expected no changes made to test \"%s\" in \"%s\".\nFixers applied:\n\"%s\".\nDiff.:\n\"%s\".",
                    $case->getTitle(),
                    $case->getFileName(),
                    $changed === null ? '[None]' : implode(',', $changed['appliedFixers']),
                    $changed === null ? '[None]' : $changed['diff']
                )
            );

            return;
        }

        $this->assertNotEmpty($changed, sprintf('Expected changes made to test "%s" in "%s".', $case->getTitle(), $case->getFileName()));
        $fixedInputCode = file_get_contents($tmpFile);
        $this->assertSame($expected, $fixedInputCode, sprintf('Expected changes do not match result for "%s" in "%s".', $case->getTitle(), $case->getFileName()));

        if ($case->shouldCheckPriority()) {
            $priorities = array_map(
                function (FixerInterface $fixer) {
                    return $fixer->getPriority();
                },
                $case->getFixers()
            );

            $this->assertNotCount(1, array_unique($priorities), sprintf('All used fixers must not have the same priority, integration tests should cover fixers with different priorities. In "%s".', $case->getFileName()));

            $tmpFile = static::getTempFile();
            if (false === @file_put_contents($tmpFile, $input)) {
                throw new IOException(sprintf('Failed to write to tmp. file "%s".', $tmpFile));
            }

            $configProphecy->getFinder()->willReturn(new \ArrayIterator(array(new \SplFileInfo($tmpFile))));
            $configProphecy->getFixers()->willReturn(array_reverse($case->getFixers()));

            $runner->fix();
            $fixedInputCodeWithReversedFixers = file_get_contents($tmpFile);

            $this->assertNotSame(
                $fixedInputCode,
                $fixedInputCodeWithReversedFixers,
                sprintf('Set priorities must be significant. If fixers used in reverse order return same output then the integration test is not sufficient or the priority relation between used fixers should not be set. In "%s".', $case->getFileName())
            );
        }

        // run the test again with the `expected` part, this should always stay the same
        $this->testIntegration(
            $case
                ->setTitle($case->getTitle().' "--EXPECT-- part run"')
                ->setInputCode(null)
        );
    }

    /**
     * @param string $source
     *
     * @return string|null
     */
    protected function lintSource($source)
    {
        if ($this->isLintException($source)) {
            return;
        }

        try {
            static::$linter->lintSource($source)->check();
        } catch (\Exception $e) {
            return $e->getMessage()."\n\nSource:\n$source";
        }
    }

    /**
     * @param string $source
     *
     * @return bool
     */
    protected function isLintException($source)
    {
        return false;
    }

    /**
     * @param Error[] $errors
     *
     * @return string
     */
    private function implodeErrors(array $errors)
    {
        $errorStr = '';
        foreach ($errors as $error) {
            $errorStr .= sprintf("%d: %s\n", $error->getType(), $error->getFilePath());
        }

        return $errorStr;
    }
}
