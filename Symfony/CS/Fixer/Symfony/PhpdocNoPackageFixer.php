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

use Symfony\CS\AbstractAnnotationRemovalFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocNoPackageFixer extends AbstractAnnotationRemovalFixer
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
