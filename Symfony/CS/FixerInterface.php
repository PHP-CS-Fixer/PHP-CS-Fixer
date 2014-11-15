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
interface FixerInterface
{
    const PSR0_LEVEL    = 1;
    const PSR1_LEVEL    = 3;
    const PSR2_LEVEL    = 7;
    const SYMFONY_LEVEL = 15;
    const CONTRIB_LEVEL = 32;

    /**
     * Fixes a file.
     *
     * @param \SplFileInfo $file    A \SplFileInfo instance
     * @param string       $content The file content
     *
     * @return string The fixed file content
     */
    public function fix(\SplFileInfo $file, $content);

    /**
     * Returns the description of the fixer.
     *
     * A short one-line description of what the fixer does.
     *
     * @return string The description of the fixer
     */
    public function getDescription();

    /**
     * Returns the level of CS standard.
     *
     * Can be one of:
     *  - self::PSR0_LEVEL,
     *  - self::PSR1_LEVEL,
     *  - self::PSR2_LEVEL,
     *  - self::SYMFONY_LEVEL,
     *  - self::CONTRIB_LEVEL.
     */
    public function getLevel();

    /**
     * Returns the name of the fixer.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the fixer
     */
    public function getName();

    /**
     * Returns the priority of the fixer.
     *
     * The default priority is 0 and higher priorities are executed first.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Returns true if the file is supported by this fixer.
     *
     * @return bool true if the file is supported by this fixer, false otherwise
     */
    public function supports(\SplFileInfo $file);
}
