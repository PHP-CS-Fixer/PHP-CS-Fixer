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
final class FunctionReturnTypeAnalysis implements StartEndTokenAwareAnalysis
{
    /**
     * The function return type.
     *
     * @var string
     */
    private $type;

    /**
     * The start index of the return type in the analyzed Tokens.
     *
     * @var int
     */
    private $startIndex;

    /**
     * The end index of the return type in the analyzed Tokens.
     *
     * @var int
     */
    private $endIndex;

    public function __construct($type, $startIndex, $endIndex)
    {
        $this->type = (string) $type;
        $this->startIndex = (int) $startIndex;
        $this->endIndex = (int) $endIndex;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
