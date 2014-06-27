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
    public function fix(\SplFileInfo $file, $content)
    {
        static $supportedEncodings = null;

        if (null === $supportedEncodings) {
            $supportedEncodings = mb_list_encodings();
        }

        $encoding = mb_detect_encoding($content, $supportedEncodings, true);

        if ('UTF-8' === $encoding && 0 === strncmp($content, pack('CCC', 0xef, 0xbb, 0xbf), 3)) {
            $encoding .= ' BOM';
        }

        if (!in_array($encoding, array('ASCII', 'UTF-8', ))) {
            echo '! File '.strtr($file->getRealPath(), '\\', '/').' with incorrect encoding: '.$encoding.PHP_EOL;
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
        return 0;
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
        return 'PHP code MUST use only UTF-8 without BOM (detect only).';
    }
}
