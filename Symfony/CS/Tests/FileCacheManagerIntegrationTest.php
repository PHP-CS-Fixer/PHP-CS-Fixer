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

namespace Symfony\CS\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use Symfony\CS\FileCacheManager;
use Symfony\CS\Fixer;

class FileCacheManagerIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->fs = new Filesystem();
        $this->rootDir = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FileCacheManagerIntegrationTest';

        $this->fs->mkdir($this->rootDir);
        $this->isCacheAvailable = $this->isCacheAvailable();

        // Never ever rely on last test results
        $this->clearFileCacheManagerIntegrationTestFiles();

        $this->customConfigDir = $this->rootDir.DIRECTORY_SEPARATOR.'custom_config_dir';

        $this->files = array(
            'one' => $this->rootDir.DIRECTORY_SEPARATOR.'path_one'.DIRECTORY_SEPARATOR.'CommonFilename.php',
            'two' => $this->rootDir.DIRECTORY_SEPARATOR.'path_two'.DIRECTORY_SEPARATOR.'CommonFilename.php',
        );

        $this->fs->mkdir($this->rootDir);
        $this->fs->mkdir($this->customConfigDir);

        $fs = $this->fs;
        array_walk($this->files, function ($filename) use ($fs) {
            $fs->mkdir(dirname($filename));
            file_put_contents($filename, "<?\n// {$filename}\n");
        });
    }

    protected function tearDown()
    {
        $this->clearFileCacheManagerIntegrationTestFiles();
    }

    private function clearFileCacheManagerIntegrationTestFiles()
    {
        $this->fs->remove($this->rootDir);
    }

    private function isCacheAvailable()
    {
        $fileCacheManager = new FileCacheManager(true, $this->rootDir, array());
        $reflectionIsCacheAvailable = new \ReflectionMethod($fileCacheManager, 'isCacheAvailable');
        $reflectionIsCacheAvailable->setAccessible(true);
        $isCacheAvailable = $reflectionIsCacheAvailable->invoke($fileCacheManager);
        $reflectionIsCacheAvailable->setAccessible(false);

        // We don't want FileCacheManager::__destruct() interferences
        unset($reflectionIsCacheAvailable, $fileCacheManager);

        return $isCacheAvailable;
    }

    /**
     * @dataProvider provideScenarios
     */
    public function testCacheScenarios($insideCacheFileDir)
    {
        if (!$this->isCacheAvailable) {
            $this->markTestSkipped('Cache functionality not supported');
        }

        $configDir = $this->rootDir;
        if (!$insideCacheFileDir) {
            $configDir = $this->customConfigDir;
        }

        $fixer = new Fixer();
        $fixer->addFixer(new Fixer\PSR1\ShortTagFixer());
        $fixers = $fixer->getFixers();

        $files = array_map(function ($filename) {
            return new FinderSplFileInfo(
                $filename,
                dirname($filename),
                basename($filename)
            );
        }, $this->files);

        $fileCacheManager = new FileCacheManager(true, $configDir, $fixers);
        $this->assertNotNull($fixer->fixFile($files['one'], $fixers, false, false, $fileCacheManager));
        $this->assertNotNull($fixer->fixFile($files['two'], $fixers, false, false, $fileCacheManager));

        // This unset() calls FileCacheManager::saveToFile() and is needed for the test
        unset($fileCacheManager);

        $fileCacheManager = new FileCacheManager(true, $configDir, $fixers);
        $this->assertFalse($fileCacheManager->needFixing($files['one']->getRealpath(), file_get_contents($files['one']->getRealpath())));
        $this->assertFalse($fileCacheManager->needFixing($files['two']->getRealpath(), file_get_contents($files['two']->getRealpath())));

        // This unset() calls FileCacheManager::saveToFile() and let the tearDown clean
        // everything after the test has ended; otherwise FileCacheManager::__destruct()
        // would leave some files after the run
        unset($fileCacheManager);
    }

    public function provideScenarios()
    {
        return array(
            'cache-stored-with-pathname-relative-to-cachefile-if-in-subfolder' => array(true),
            'cache-stored-with-common-pathname-relative-to-cachefile-if-outside-cachefile-folder' => array(false),
        );
    }
}
