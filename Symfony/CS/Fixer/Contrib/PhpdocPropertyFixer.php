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
class PhpdocPropertyFixer extends AbstractPhpdocTagsFixer
{
    /**
     * The tags to search for.
     *
     * @var string[]
     */
    protected static $search = array('property-read', 'property-write');

    /**
     * The input tag.
     *
     * @var string[]
     */
    protected static $input = array('@property-read', '@property-write');

    /**
     * The output tag.
     *
     * @var string
     */
    protected static $output = '@property';

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@property tags should be used rather than other variants.';
    }
}
