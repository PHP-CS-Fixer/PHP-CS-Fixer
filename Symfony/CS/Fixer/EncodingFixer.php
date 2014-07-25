<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class EncodingFixer implements FixerInterface
{
    private $BOM;

    public function __construct()
    {
        $this->BOM = pack('CCC', 0xef, 0xbb, 0xbf);
    }

    public function fix(\SplFileInfo $file, $content)
    {
        if (0 === strncmp($content, $this->BOM, 3)) {
            return substr($content, 3);
        }

        return $content;
    }

    public function getLevel()
    {
        // defined in PSR1 ¶2.2
        return FixerInterface::PSR1_LEVEL;
    }

    public function getPriority()
    {
        // must run first (at least before Fixers that using Tokens) - for speed reason of whole fixing process
        return 100;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'encoding';
    }

    public function getDescription()
    {
        return 'PHP code MUST use only UTF-8 without BOM (remove BOM).';
    }
}
