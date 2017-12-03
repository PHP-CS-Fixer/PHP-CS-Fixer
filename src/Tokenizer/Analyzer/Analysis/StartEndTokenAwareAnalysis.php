<?php

declare(strict_types=1);

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

interface StartEndTokenAwareAnalysis
{
    /**
     * The start index of the analyzed subject inside of the Tokens.
     *
     * @return int
     */
    public function getStartIndex();

    /**
     * The end index of the analyzed subject inside of the Tokens.
     *
     * @return int
     */
    public function getEndIndex();
}
