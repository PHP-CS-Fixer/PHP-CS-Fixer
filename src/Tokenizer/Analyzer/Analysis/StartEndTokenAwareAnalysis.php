<?php

declare(strict_types=1);

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
interface StartEndTokenAwareAnalysis
{
    /**
     * The start index of the analyzed subject inside of the Tokens.
     */
    public function getStartIndex(): int;

    /**
     * The end index of the analyzed subject inside of the Tokens.
     */
    public function getEndIndex(): int;
}
