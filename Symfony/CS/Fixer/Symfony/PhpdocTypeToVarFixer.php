<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractPhpdocTagsFixer;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocTypeToVarFixer extends AbstractPhpdocTagsFixer
{
    /**
     * {@inheritdoc}
     */
    protected static $search = array('type');

    /**
     * {@inheritdoc}
     */
    protected static $replace = 'var';

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@type should always be written as @var.';
    }
}
