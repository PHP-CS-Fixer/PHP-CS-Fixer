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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface FixerDefinitionInterface
{
    public function getSummary();

    public function getDescription();

    public function getConfigurationDescription();

    public function getDefaultConfiguration();

    public function getRiskyDescription();

    public function getCodeSamples();
}
