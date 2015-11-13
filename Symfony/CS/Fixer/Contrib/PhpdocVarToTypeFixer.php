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

use Symfony\CS\AbstractPhpdocTagsFixer;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocVarToTypeFixer extends AbstractPhpdocTagsFixer
{
    /**
     * The tag to search for.
     *
     * @var string
     */
    protected static $search = 'var';

    /**
     * The input tag.
     *
     * @var string
     */
    protected static $input = '@var';

    /**
     * The output tag.
     *
     * @var string
     */
    protected static $output = '@type';

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@var should always be written as @type.';
    }
}
