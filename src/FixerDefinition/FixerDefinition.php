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

namespace PhpCsFixer\FixerDefinition;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class FixerDefinition implements FixerDefinitionInterface
{
    private $riskyDescription;
    private $codeSamples;
    private $summary;
    private $description;

    /**
     * @param string                $summary
     * @param CodeSampleInterface[] $codeSamples      array of samples, where single sample is [code, configuration]
     * @param null|string           $description
     * @param null|string           $riskyDescription null for non-risky fixer
     */
    public function __construct(
        $summary,
        array $codeSamples,
        $description = null,
        $riskyDescription = null
    ) {
        $this->summary = $summary;
        $this->codeSamples = $codeSamples;
        $this->description = $description;
        $this->riskyDescription = $riskyDescription;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getRiskyDescription()
    {
        return $this->riskyDescription;
    }

    public function getCodeSamples()
    {
        return $this->codeSamples;
    }
}
