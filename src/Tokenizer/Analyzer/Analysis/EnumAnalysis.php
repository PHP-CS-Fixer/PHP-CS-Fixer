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
final class EnumAnalysis extends AbstractControlCaseStructuresAnalysis
{
    /**
     * @var CaseAnalysis[]
     */
    private $cases;

    /**
     * @param CaseAnalysis[] $cases
     */
    public function __construct(int $index, int $open, int $close, array $cases)
    {
        parent::__construct($index, $open, $close);

        $this->cases = $cases;
    }

    /**
     * @return CaseAnalysis[]
     */
    public function getCases(): array
    {
        return $this->cases;
    }
}
