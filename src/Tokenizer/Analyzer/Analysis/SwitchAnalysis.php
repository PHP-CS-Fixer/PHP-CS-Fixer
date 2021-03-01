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
final class SwitchAnalysis
{
    /**
     * @var int
     */
    private $casesStart;

    /**
     * @var int
     */
    private $casesEnd;

    /**
     * @var CaseAnalysis[]
     */
    private $cases = [];

    /**
     * @param CaseAnalysis[] $cases
     */
    public function __construct(int $casesStart, int $casesEnd, array $cases)
    {
        $this->casesStart = $casesStart;
        $this->casesEnd = $casesEnd;
        $this->cases = $cases;
    }

    public function getCasesStart(): int
    {
        return $this->casesStart;
    }

    public function getCasesEnd(): int
    {
        return $this->casesEnd;
    }

    /**
     * @return CaseAnalysis[]
     */
    public function getCases(): array
    {
        return $this->cases;
    }
}
