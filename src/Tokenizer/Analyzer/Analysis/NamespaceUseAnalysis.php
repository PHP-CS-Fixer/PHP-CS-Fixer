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

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class NamespaceUseAnalysis implements StartEndTokenAwareAnalysis
{
    /**
     * The fully qualified use namespace.
     *
     * @var string
     */
    private $fullName;

    /**
     * The short version of use namespace or the alias name in case of aliased use statements.
     *
     * @var string
     */
    private $shortName;

    /**
     * Is the use statement being aliased?
     *
     * @var bool
     */
    private $isAliased;

    /**
     * The start index of the namespace declaration in the analyzed Tokens.
     *
     * @var int
     */
    private $startIndex;

    /**
     * The end index of the namespace declaration in the analyzed Tokens.
     *
     * @var int
     */
    private $endIndex;

    /**
     * @param string $fullName
     * @param string $shortName
     * @param bool   $isAliased
     * @param int    $startIndex
     * @param int    $endIndex
     */
    public function __construct($fullName, $shortName, $isAliased, $startIndex, $endIndex)
    {
        $this->fullName = $fullName;
        $this->shortName = $shortName;
        $this->isAliased = $isAliased;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @return bool
     */
    public function isAliased()
    {
        return $this->isAliased;
    }

    /**
     * @return int
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * @return int
     */
    public function getEndIndex()
    {
        return $this->endIndex;
    }
}
