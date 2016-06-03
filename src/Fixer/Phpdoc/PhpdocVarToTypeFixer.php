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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractProxyFixer;

/**
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocVarToTypeFixer extends AbstractProxyFixer
{
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixer()
    {
        $fixer = new GeneralPhpdocAnnotationRenameFixer();
        $fixer->configure(array(
            'var' => 'type',
        ));

        return $fixer;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@var should always be written as @type.';
    }
}
