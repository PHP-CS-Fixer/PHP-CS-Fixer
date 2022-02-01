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
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class CaseAnalysis
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var int
     */
    private $colonIndex;

    public function __construct(int $index, int $colonIndex)
    {
        $this->index = $index;
        $this->colonIndex = $colonIndex;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getColonIndex(): int
    {
        return $this->colonIndex;
    }
}
