<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Test;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\CS\Error\Error;
use Symfony\CS\FileCacheManager;
use Symfony\CS\Fixer;
use Symfony\CS\FixerFactory;
use Symfony\CS\FixerInterface;
use Symfony\CS\RuleSet;

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
 * --REQUIREMENTS--
 * php=5.4*
 * hhvm=false**
 * --INPUT--
 * Code to fix***
 * --EXPECT--
 * Expected code after fixing***
 *
 *   * PHP minimum version. Default to current running php version (no effect).
 *  ** HHVM compliant flag. Default to true. Set to false to skip test under HHVM.
 * *** Input part may be omitted
 *
 * @author SpacePossum <possumfromspace@gmail.com>
 */
abstract class AbstractIntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $tmpFile = static::getTempFile();
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
        @unlink(static::getTempFile());
    }

    /**
     * @dataProvider getTests
     *
     * @see doTest()
     */
    public function testIntegration($testFileName, $testTitle, $fixers, array $requirements, $expected, $input = null)
    {
        $this->doTest($testFileName, $testTitle, $fixers, $requirements, $expected, $input);
    }

    /**
     * Creates test data by parsing '.test' files.
     *
     * @return array
     */
    public function getTests()
    {
        $fixturesDir = realpath(static::getFixturesDir());
        if (!is_dir($fixturesDir)) {
            throw new \UnexpectedValueException(sprintf('Given fixture dir "%s" is not a directory.', $fixturesDir));
        }

        $tests = array();

        foreach (Finder::create()->files()->in($fixturesDir) as $file) {
            if ('test' !== $file->getExtension()) {
                continue;
            }

            $test = file_get_contents($file->getRealpath());
            $fileName = $file->getRelativePathname();

            if (!preg_match('/--TEST--[\n](.*?)\s--CONFIG--[\n](.*?)(\s--REQUIREMENTS--[\n](.*?))?\s--EXPECT--[\n](.*?[\n]*)(?:[\n]--INPUT--\s(.*)|$)/s', $test, $match)) {
                throw new \InvalidArgumentException(sprintf('Test format invalid for "%s".', $fileName));
            }

            $tests[] = array($fileName, $match[1], $this->getFixersFromConfig($fileName, $match[2]), $this->getRequirementsFromConfig($fileName, $match[4]), $match[5], isset($match[6]) ? $match[6] : null);
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
     * @param string           $testFileName Filename
     * @param string           $testTitle    Test title
     * @param FixerInterface[] $fixers       Fixers to use
     * @param array            $requirements Env requirements (PHP, HHVM)
     * @param string           $expected     Expected result
     * @param string|null      $input        Code to fix, or null if it should intentionally be equal to the expected result.
     */
    protected function doTest($testFileName, $testTitle, $fixers, array $requirements, $expected, $input = null)
    {
        if (defined('HHVM_VERSION') && false === $requirements['hhvm']) {
            $this->markTestSkipped('HHVM is not supported.');
        }

        if (isset($requirements['php']) && version_compare(PHP_VERSION, $requirements['php']) < 0) {
            $this->markTestSkipped(sprintf('PHP %s (or later) is required.', $requirements['php']));
        }

        $fixer = new Fixer();
        $tmpFile = static::getTempFile();

        if (false === @file_put_contents($tmpFile, null === $input ? $expected : $input)) {
            throw new IOException(sprintf('Failed to write to tmp. file "%s".', $tmpFile));
        }

        $changed = $fixer->fixFile(new \SplFileInfo($tmpFile), $fixers, false, true, new FileCacheManager(false, null, $fixers));

        $errorsManager = $fixer->getErrorsManager();

        if (!$errorsManager->isEmpty()) {
            $errors = $errorsManager->getExceptionErrors();
            $this->assertEmpty($errors, sprintf('Errors reported during fixing: %s', $this->implodeErrors($errors)));

            $errors = $errorsManager->getInvalidErrors();
            $this->assertEmpty($errors, sprintf('Errors reported during linting before fixing: %s.', $this->implodeErrors($errors)));

            $errors = $errorsManager->getLintErrors();
            $this->assertEmpty($errors, sprintf('Errors reported during linting after fixing: %s.', $this->implodeErrors($errors)));
        }

        if (null === $input) {
            $this->assertEmpty($changed, sprintf("Expected no changes made to test \"%s\" in \"%s\".\nFixers applied:\n\"%s\".\nDiff.:\n\"%s\".", $testTitle, $testFileName, $changed === null ? '[None]' : implode(',', $changed['appliedFixers']), $changed === null ? '[None]' : $changed['diff']));

            return;
        }

        $this->assertNotEmpty($changed, sprintf('Expected changes made to test "%s" in "%s".', $testTitle, $testFileName));
        $fixedInputCode = file_get_contents($tmpFile);
        $this->assertSame($expected, $fixedInputCode, sprintf('Expected changes do not match result for "%s" in "%s".', $testTitle, $testFileName));

        $priorities = array_map(
            function (FixerInterface $fixer) {
                return $fixer->getPriority();
            },
            $fixers
        );

        $this->assertNotCount(1, array_unique($priorities), 'All used fixers must not have the same priority, integration tests should cover fixers with different priorities.');

        $tmpFile = static::getTempFile();
        if (false === @file_put_contents($tmpFile, $input)) {
            throw new IOException(sprintf('Failed to write to tmp. file "%s".', $tmpFile));
        }

        $changed = $fixer->fixFile(new \SplFileInfo($tmpFile), array_reverse($fixers), false, true, new FileCacheManager(false, null, $fixers));
        $fixedInputCodeWithReversedFixers = file_get_contents($tmpFile);
        $this->assertNotSame($fixedInputCode, $fixedInputCodeWithReversedFixers, 'Set priorities must be significant. If fixers used in reverse order return same output then the integration test is not sufficient or the priority relation between used fixers should not be set.');

        // run the test again with the `expected` part, this should always stay the same
        $this->testIntegration($testFileName, $testTitle.' "--EXPECT-- part run"', $fixers, $requirements, $expected);
    }

    /**
     * Create fixer factory with all needed fixers registered.
     *
     * @return FixerFactory
     */
    protected function createFixerFactory()
    {
        return FixerFactory::create()->registerBuiltInFixers();
    }

    /**
     * Parses the '--CONFIG--' block of a '.test' file and determines what fixers should be used.
     *
     * @param string $fileName
     * @param string $config
     *
     * @return FixerInterface[]
     */
    protected function getFixersFromConfig($fileName, $config)
    {
        $ruleSet = json_decode($config, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf('Malformed JSON configuration "%s".', $fileName));
        }

        return $this->createFixerFactory()
            ->useRuleSet(new RuleSet($ruleSet))
            ->getFixers()
        ;
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

    /**
     * Parses the '--REQUIREMENTS--' block of a '.test' file and determines requirements.
     *
     * @param string $fileName
     * @param string $config
     *
     * @return array
     */
    protected function getRequirementsFromConfig($fileName, $config)
    {
        $requirements = array('hhvm' => true, 'php' => PHP_VERSION);

        if ('' === $config) {
            return $requirements;
        }

        $lines = explode("\n", $config);
        if (empty($lines)) {
            return $requirements;
        }

        foreach ($lines as $line) {
            $labelValuePair = explode('=', $line);
            if (2 !== count($labelValuePair)) {
                throw new \InvalidArgumentException(sprintf('Invalid requirements line "%s" in "%s".', $line, $fileName));
            }

            $label = strtolower(trim($labelValuePair[0]));
            $value = trim($labelValuePair[1]);

            switch ($label) {
                case 'hhvm':
                    $requirements['hhvm'] = 'false' === $value ? false : true;
                    break;
                case 'php':
                    $requirements['php'] = $value;
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown configuration item "%s" in "%s".', $label, $fileName));
            }
        }

        return $requirements;
    }
}
