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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\CS\Error\Error;
use Symfony\CS\FileCacheManager;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

/**
 * Integration test helper.
 *
 * @author SpacePossum
 */
abstract class AbstractIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTests
     */
    public function testIntegration($testFileName, $testTitle, $fixers, $input, $expected = null)
    {
        $this->doTestIntegration($testFileName, $testTitle, $fixers, $input, $expected);
    }

    public function getTests()
    {
        $fixturesDir = realpath($this->getFixturesDir());
        $this->assertTrue(is_dir($fixturesDir), sprintf('Given fixture dir "%s" is not a directory.', $fixturesDir));

        $tests = array();

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($fixturesDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if (!preg_match('/\.test$/', $file)) {
                continue;
            }

            $test = file_get_contents($file->getRealpath());
            $fileName = $file->getFileName();
            if (preg_match('/--TEST--[\r\n](.*?)\s--CONFIG--[\r\n](.*?)\s--INPUT--[\r\n](.*?[\r\n]*)(?:[\r\n]--EXPECT--\s(.*)|$)/s', $test, $match)) {
                $testTitle = $match[1];
                $config = $this->getFixersFromConfig($fileName, $match[2]);
                $input = $match[3];
                $expected = isset($match[4]) ? $match[4] : null;
                $tests[] = array($fileName, $testTitle, $config, $input, $expected);
            } else {
                throw new InvalidArgumentException(sprintf('Test "%s" is not valid.', $fileName));
            }
        }

        $this->assertNotEmpty($tests, sprintf('No tests found in fixtures dir "%s".', $fixturesDir));

        return $tests;
    }

    /**
     * Returns the full path to directory which contains the tests.
     *
     * @return string
     */
    abstract protected function getFixturesDir();

    /**
     * Returns the full path to directory where the tests will writes the temporary file.
     *
     * @return string
     */
    abstract protected function getTempDir();

    /**
     * @param string           $testFileName
     * @param string           $testTitle
     * @param FixerInterface[] $fixers
     * @param string           $input
     * @param string|null      $expected
     */
    protected function doTestIntegration($testFileName, $testTitle, $fixers, $input, $expected = null)
    {
        $fixer = new Fixer();
        $fs = new Filesystem();

        $dir = $this->getTempDir();
        $tmpDir = new \SplFileInfo($dir);
        if (!$tmpDir->isDir()) {
            $fs->mkdir($tmpDir);
        }

        $this->assertTrue($tmpDir->isDir(), sprintf('Given temp dir "%s" is not a directory.', $dir));
        $this->assertTrue($tmpDir->isWritable(), sprintf('Given temp dir "%s" is not writable.', $dir));

        $tmpFile = $tmpDir->getRealPath().'/tmp.php';
        $this->assertNotFalse(@file_put_contents($tmpFile, $input), sprintf('Failed to write to tmp. file "%s".', $tmpFile));

        $changed = $fixer->fixFile(new \SplFileInfo($tmpFile), $fixers, false, true, new FileCacheManager(false, null, $fixers));

        if (null !== $expected) {
            // read back the changed file content
            $newContent = file_get_contents($tmpFile);
        }

        $fs->remove($tmpFile);

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
            if (null !== $changed) {
                $this->assertEmpty($changed, sprintf("Expected no changes made to test \"%s\" in \"%s\".\nFixers applied:\n\"%s\".\nDiff.:\n\"%s\".", $testTitle, $testFileName, implode(',', $changed['appliedFixers']), $changed['diff']));
            }

            return;
        }

        $this->assertNotEmpty($changed, sprintf('Expected changes made to test "%s" in "%s".', $testTitle, $testFileName));
        $this->assertSame($expected, $newContent, sprintf('Expected changes do not match result, for "%s" in "%s".', $testTitle, $testFileName));

        // run the test again with the `expected` part, this should always stay the same
        $this->testIntegration($testFileName, $testTitle.' "--EXPECT-- part run"', $fixers, $expected);
    }

    /**
     * @param string $fileName
     * @param string $config
     *
     * @return FixerInterface[]
     */
    protected function getFixersFromConfig($fileName, $config)
    {
        static $levelMap = array(
            'none' => FixerInterface::NONE_LEVEL,
            'psr1' => FixerInterface::PSR1_LEVEL,
            'psr2' => FixerInterface::PSR2_LEVEL,
            'symfony' => FixerInterface::SYMFONY_LEVEL,
        );

        $lines = explode("\n", $config);
        if (count($lines) < 1) {
            throw new InvalidArgumentException(sprintf('No configuration options found in "%s".', $fileName));
        }

        $config = array('level' => null, 'fixers' => array(), '--fixers' => array());

        foreach ($lines as $line) {
            $labelValuePair = explode('=', $line);
            if (2 !== count($labelValuePair)) {
                throw new InvalidArgumentException(sprintf('Invalid configuration line "%s" in "%s".', $line, $fileName));
            }

            $label = strtolower(trim($labelValuePair[0]));
            $value = trim($labelValuePair[1]);

            switch ($label) {
                case 'level' : {
                    if (!array_key_exists($value, $levelMap)) {
                        throw new InvalidArgumentException(sprintf('Unknown level "%s" set in configuration in "%s", expected any of "%s".', $value, $fileName, implode(', ', array_keys($levelMap))));
                    }

                    if (null !== $config['level']) {
                        throw new InvalidArgumentException(sprintf('Cannot use multiple levels in configuration in "%s".', $fileName));
                    }

                    $config['level'] = $value;
                    break;
                }
                case 'fixers' : {
                    $fixers = explode(',', $value);
                    foreach ($fixers as $fixer) {
                        $config['fixers'][] = strtolower(trim($fixer));
                    }

                    break;
                }
                case '--fixers' : {
                    $fixers = explode(',', $value);
                    foreach ($fixers as $fixer) {
                        $config['--fixers'][] = strtolower(trim($fixer));
                    }

                    break;
                }
                default : {
                    throw new InvalidArgumentException(sprintf('Unknown configuration item "%s" in "%s".', $label, $fileName));
                }
            }
        }

        if (null === $config['level']) {
            throw new InvalidArgumentException(sprintf('Level not set in configuration "%s".', $fileName));
        }

        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();
        $allFixers = $fixer->getFixers();

        $fixers = array();
        for ($i = count($allFixers) - 1; $i >= 0; --$i) {
            /** @var FixerInterface $fixer */
            $fixer = $allFixers[$i];
            $fixerName = $fixer->getName();
            if ('psr0' === $fixer->getName()) {
                // File based fixer won't work
                continue;
            }

            if ($fixer->getLevel() !== ($fixer->getLevel() & $levelMap[$config['level']])) {
                if (false !== $key = array_search($fixerName, $config['fixers'], true)) {
                    $fixers[] = $fixer;
                    unset($config['fixers'][$key]);
                }
                continue;
            }

            if (false !== $key = array_search($fixerName, $config['--fixers'], true)) {
                unset($config['--fixers'][$key]);
                continue;
            }

            if (in_array($fixerName, $config['fixers'], true)) {
                throw new InvalidArgumentException(sprintf('Additional fixer "%s" configured, but is already part of the level.', $fixerName));
            }

            $fixers[] = $fixer;
        }

        if (!empty($config['fixers'])) {
            throw new InvalidArgumentException(sprintf('Unknown "fixers" configured to be used "%s".', implode(',', $config['fixers'])));
        }

        if (!empty($config['--fixers'])) {
            throw new InvalidArgumentException(sprintf('Unknown "--fixers" configured to be removed "%s".', implode(',', $config['--fixers'])));
        }

        return $fixers;
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
