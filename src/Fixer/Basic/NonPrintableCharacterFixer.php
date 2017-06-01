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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Removes Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
 *
 * @author Ivan Boprzenkov <ivan.borzenkov@gmail.com>
 */
final class NonPrintableCharacterFixer extends AbstractFixer
{
    private $symbolsReplace;

    private static $tokens = array(
        T_STRING_VARNAME,
        T_INLINE_HTML,
        T_VARIABLE,
        T_COMMENT,
        T_ENCAPSED_AND_WHITESPACE,
        T_CONSTANT_ENCAPSED_STRING,
        T_DOC_COMMENT,
    );

    public function __construct()
    {
        parent::__construct();
        $this->symbolsReplace = array(
            pack('CCC', 0xe2, 0x80, 0x8b) => '', // ZWSP
            pack('CCC', 0xe2, 0x80, 0x87) => ' ', // FIGURE SPACE
            pack('CCC', 0xe2, 0x80, 0xaf) => ' ', // NBSP
            pack('CCC', 0xe2, 0x81, 0xa0) => '', // WORD JOINER
            pack('CC', 0xc2, 0xa0) => ' ', // NO-BREAK SPACE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.',
            array(
                new CodeSample(
'<?php

echo "'.pack('CCC', 0xe2, 0x80, 0x8b).'Hello'.pack('CCC', 0xe2, 0x80, 0x87).'World'.pack('CC', 0xc2, 0xa0).'!";
'
                ),
            ),
            null,
            'Risky when strings contain intended invisible characters.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return count($tokens) > 1 && $tokens->isAnyTokenKindsFound(self::$tokens);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (in_array($token->getId(), self::$tokens, true)) {
                $tokens[$index] = new Token(array($token->getId(), strtr($token->getContent(), $this->symbolsReplace)));
            }
        }
    }
}
