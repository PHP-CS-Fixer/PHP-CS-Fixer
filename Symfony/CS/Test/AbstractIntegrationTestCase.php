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
 * Example test description
 * --CONFIG--
 * {"@PSR2": true, "strict": true}
 * --INPUT--
 * Code to fix
 * --EXPECT--
 * Expected code after fixing*
 *
 * * When the expected block is omitted the input is expected not to
 *     be changed by the fixers.
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
    public function testIntegration($testFileName, $testTitle, $fixers, $input, $expected = null)
    {
        $this->doTest($testFileName, $testTitle, $fixers, $input, $expected);
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

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($fixturesDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if (!preg_match('/\.test$/', $file)) {
                continue;
            }

            $test = file_get_contents($file->getRealpath());
            $fileName = $file->getFileName();
            if (!preg_match('/--TEST--[\n](.*?)\s--CONFIG--[\n](.*?)\s--INPUT--[\n](.*?[\n]*)(?:[\n]--EXPECT--\s(.*)|$)/s', $test, $match)) {
                throw new \InvalidArgumentException(sprintf('Test format invalid for "%s".', $fileName));
            }

            $tests[] = array($fileName, $match[1], $this->getFixersFromConfig($fileName, $match[2]), $match[3], isset($match[4]) ? $match[4] : null);
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
     * @param string           $input        Code to fix
     * @param string|null      $expected     Expected result or null if the input is expected not to change
     */
    protected function doTest($testFileName, $testTitle, $fixers, $input, $expected = null)
    {
        $fixer = new Fixer();
        $tmpFile = static::getTempFile();

        if (false === @file_put_contents($tmpFile, $input)) {
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

        if (null === $expected) {
            $this->assertEmpty($changed, sprintf("Expected no changes made to test \"%s\" in \"%s\".\nFixers applied:\n\"%s\".\nDiff.:\n\"%s\".", $testTitle, $testFileName, $changed === null ? '[None]' : implode(',', $changed['appliedFixers']), $changed === null ? '[None]' : $changed['diff']));

            return;
        }

        $this->assertNotEmpty($changed, sprintf('Expected changes made to test "%s" in "%s".', $testTitle, $testFileName));
        $this->assertSame($expected, file_get_contents($tmpFile), sprintf('Expected changes do not match result for "%s" in "%s".', $testTitle, $testFileName));

        // run the test again with the `expected` part, this should always stay the same
        $this->testIntegration($testFileName, $testTitle.' "--EXPECT-- part run"', $fixers, $expected);
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
}
