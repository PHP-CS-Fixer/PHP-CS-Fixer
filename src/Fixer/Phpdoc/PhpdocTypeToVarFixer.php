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
 * @author Graham Campbell <graham@mineuk.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocTypeToVarFixer extends AbstractProxyFixer
{
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixer()
    {
        $fixer = new GeneralPhpdocAnnotationRenameFixer();
        $fixer->configure(array(
            'type' => 'var',
        ));

        return $fixer;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@type should always be written as @var.';
    }
}
