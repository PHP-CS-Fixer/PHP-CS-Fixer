<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractAnnotationRemovalFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocNoPackageFixer extends AbstractAnnotationRemovalFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->removeAnnotations($tokens, array('package', 'subpackage'));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@package and @subpackage annotations should be omitted from phpdocs.';
    }
}
