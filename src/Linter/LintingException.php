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

namespace PhpCsFixer\Linter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class LintingException extends \RuntimeException
{
    /**
     * @var array
     */
    private $appliedFixers = [];

    /**
     * @var null|string
     */
    private $diff;

    /**
     * @param array $appliedFixers List of applied fixers, if Error::TYPE_LINT
     */
    public function setAppliedFixers(array $appliedFixers)
    {
        $this->appliedFixers = $appliedFixers;
    }

    /**
     * @return array
     */
    public function getAppliedFixers()
    {
        return $this->appliedFixers;
    }

    /**
     * @param string $diff Diff of applied fixers, if Error::TYPE_LINT
     */
    public function setDiff($diff)
    {
        $this->diff = $diff;
    }

    /**
     * @return null|string
     */
    public function getDiff()
    {
        return $this->diff;
    }
}
