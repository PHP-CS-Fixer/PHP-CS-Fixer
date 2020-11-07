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

namespace PhpCsFixer\RuleSet;

/**
 * @author SpacePossum
 *
 * @internal
 */
interface RuleSetDescriptionInterface
{
    public function __construct();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getName();

    /**
     * Get all rules from rules set.
     *
     * @return array
     */
    public function getRules();

    /**
     * @return bool
     */
    public function isRisky();
}
