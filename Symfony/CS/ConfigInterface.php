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
     * @return \Traversable A \Traversable instance that returns \SplFileInfo instances
     */
    public function getFinder();

    /**
     * Returns the level to run.
     *
     * @return int A level
     */
    public function getLevel();

    /**
     * Returns the fixers to run.
     *
     * @return array A list of fixer names
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
}
