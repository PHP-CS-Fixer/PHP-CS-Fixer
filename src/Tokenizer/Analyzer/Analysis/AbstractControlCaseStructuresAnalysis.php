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
abstract class AbstractControlCaseStructuresAnalysis
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var int
     */
    private $open;

    /**
     * @var int
     */
    private $close;

    public function __construct(int $index, int $open, int $close)
    {
        $this->index = $index;
        $this->open = $open;
        $this->close = $close;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getOpenIndex(): int
    {
        return $this->open;
    }

    public function getCloseIndex(): int
    {
        return $this->close;
    }
}
