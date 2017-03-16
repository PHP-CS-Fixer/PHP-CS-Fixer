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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer ZWSP, NBSP and other unicode symbols.
 *
 * @author Ivan Boprzenkov <ivan.borzenkov@gmail.com>
 */
final class InvisibleSymbolsFixer extends AbstractFixer
{
    private $symbolsReplace;

    public function __construct()
    {
        parent::__construct();
        $this->symbolsReplace = array(
            pack('CCC', 0xe2, 0x80, 0x8b) => '',
            pack('CCC', 0xe2, 0x80, 0x87) => ' ',
            pack('CCC', 0xe2, 0x80, 0xaf) => ' ',
            pack('CCC', 0xe2, 0x81, 0xa0) => '',
            pack('CC', 0xc2, 0xa0) => ' ',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            $token->setContent(strtr($token->getContent(), $this->symbolsReplace));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Remove ZWSP, NBSP and other unicode symbols.',
            array(
                new CodeSample(
'<?php

echo "'.pack('CCC', 0xe2, 0x80, 0x8b).'Hello'.pack('CCC', 0xe2, 0x80, 0x87).'World'.pack('CC', 0xc2, 0xa0).'!";
'
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run first (at least before Fixers that using Tokens) - for speed reason of whole fixing process
        return 90;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }
}
