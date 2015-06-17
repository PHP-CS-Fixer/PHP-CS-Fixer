<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Traversable;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ConfigInterface
{
    /**
     * Returns the name of the configuration.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the configuration
     */
    public function getName();

    /**
     * Returns the description of the configuration.
     *
     * A short one-line description for the configuration.
     *
     * @return string The description of the configuration
     */
    public function getDescription();

    /**
     * Returns an iterator of files to scan.
     *
     * @return Traversable A \Traversable instance that returns \SplFileInfo instances
     */
    public function getFinder();

    /**
     * Returns the level to run.
     *
     * @return int A level
     */
    public function getLevel();

    /**
     * Set the fixers to be used.
     *
     * @param string[] $fixers
     *
     * @return ConfigInterface
     */
    public function fixers(array $fixers);

    /**
     * Returns the fixers.
     *
     * @return FixerInterface[] A list of fixers
     */
    public function getFixers();

    /**
     * Sets the root directory of the project.
     *
     * @param string $dir The project root directory
     *
     * @return ConfigInterface The same instance
     */
    public function setDir($dir);

    /**
     * Returns the root directory of the project.
     *
     * @return string The project root directory
     */
    public function getDir();

    /**
     * Returns true if progress should be hidden.
     *
     * @return bool
     */
    public function getHideProgress();

    /**
     * Adds an instance of a custom fixer.
     *
     * @param FixerInterface $fixer
     */
    public function addCustomFixer(FixerInterface $fixer);

    /**
     * Returns the custom fixers to use.
     *
     * @return FixerInterface[]
     */
    public function getCustomFixers();

    /**
     * Returns true if caching should be enabled.
     *
     * @return bool
     */
    public function usingCache();

    /**
     * Returns true if linter should be enabled.
     *
     * @return bool
     */
    public function usingLinter();

    /**
     * Sets the path to the cache file.
     *
     * @param string $cacheFile
     *
     * @return ConfigInterface
     */
    public function setCacheFile($cacheFile);

    /**
     * Sets if caching should be enabled.
     *
     * @param bool $usingCache
     *
     * @return ConfigInterface
     */
    public function setUsingCache($usingCache);

    /**
     * Returns the path to the cache file.
     *
     * @return string
     */
    public function getCacheFile();

    /**
     * Get configured PHP executable, if any.
     *
     * @return string|null
     */
    public function getPhpExecutable();

    /**
     * Set the finder to be used.
     *
     * @param Traversable $finder
     *
     * @return ConfigInterface
     */
    public function finder(Traversable $finder);
}
