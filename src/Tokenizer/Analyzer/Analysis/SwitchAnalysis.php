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
     * @param int            $casesStart
     * @param int            $casesEnd
     * @param CaseAnalysis[] $cases
     */
    public function __construct($casesStart, $casesEnd, array $cases)
    {
        $this->casesStart = $casesStart;
        $this->casesEnd = $casesEnd;
        $this->cases = $cases;
    }

    /**
     * @return int
     */
    public function getCasesStart()
    {
        return $this->casesStart;
    }

    /**
     * @return int
     */
    public function getCasesEnd()
    {
        return $this->casesEnd;
    }

    /**
     * @return CaseAnalysis[]
     */
    public function getCases()
    {
        return $this->cases;
    }
}
