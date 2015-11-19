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
class PhpdocTypeToVarFixer extends AbstractPhpdocTagsFixer
{
    /**
     * The tag to search for.
     *
     * @var string
     */
    protected static $search = 'type';

    /**
     * The input tag.
     *
     * @var string
     */
    protected static $input = '@type';

    /**
     * The output tag.
     *
     * @var string
     */
    protected static $output = '@var';

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@type should always be written as @var.';
    }
}
