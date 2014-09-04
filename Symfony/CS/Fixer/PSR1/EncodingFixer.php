<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR1;

use Symfony\CS\AbstractFixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class EncodingFixer extends AbstractFixer
{
    private $BOM;

    public function __construct()
    {
        $this->BOM = pack('CCC', 0xef, 0xbb, 0xbf);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        if (0 === strncmp($content, $this->BOM, 3)) {
            return substr($content, 3);
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run first (at least before Fixers that using Tokens) - for speed reason of whole fixing process
        return 100;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP code MUST use only UTF-8 without BOM (remove BOM).';
    }
}
