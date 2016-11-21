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

namespace PhpCsFixer;

/**
 * Temporary, short Fixer definition until all fixers will be described.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ShortFixerDefinition implements FixerDefinitionInterface
{
    private $summary;

    /**
     * @param string $summary
     */
    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getDescription()
    {
        return null;
    }

    public function getConfigurationDescription()
    {
        return null;
    }

    public function getDefaultConfiguration()
    {
        return null;
    }

    public function getRiskyDescription()
    {
        return null;
    }

    public function getCodeSamples()
    {
        return array();
    }
}
