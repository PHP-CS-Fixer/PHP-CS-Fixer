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
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class CaseAnalysis
{
    /**
     * @var int
     */
    private $colonIndex;

    /**
     * @param int $colonIndex
     */
    public function __construct($colonIndex)
    {
        $this->colonIndex = $colonIndex;
    }

    /**
     * @return int
     */
    public function getColonIndex()
    {
        return $this->colonIndex;
    }
}
