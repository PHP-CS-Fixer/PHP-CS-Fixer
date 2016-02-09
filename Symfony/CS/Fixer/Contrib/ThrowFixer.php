<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractBlankLineAboveFixer;

/**
 * @author Lucas Michot <lucas@semalead.com>
 */
class ThrowFixer extends AbstractBlankLineAboveFixer
{
    /**
     * {@inheritdoc}
     */
    protected static $token = T_THROW;

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'An empty line feed should precede a throw statement.';
    }
}
