<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PSR1;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR1 ¶2.2.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class EncodingFixer extends AbstractFixer
{
    private $BOM;

    public function __construct()
    {
        $this->BOM = pack('CCC', 0xef, 0xbb, 0xbf);
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $token = $tokens[0];
        $content = $token->getContent();

        if (0 === strncmp($content, $this->BOM, 3)) {
            $newContent = substr($content, 3);

            if (false === $newContent) {
                $newContent = ''; // substr returns false rather than an empty string when starting at the end
            }

            $token->setContent($newContent);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP code MUST use only UTF-8 without BOM (remove BOM).';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run first (at least before Fixers that using Tokens) - for speed reason of whole fixing process
        return 100;
    }
}
