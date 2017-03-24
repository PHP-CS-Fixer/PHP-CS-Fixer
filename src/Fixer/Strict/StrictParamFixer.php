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

namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class StrictParamFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Functions should be used with `$strict` param set to `true`.',
            array(new CodeSample("<?php\n\$a = array_keys(\$b);\n\$a = array_search(\$b, \$c);\n\$a = base64_decode(\$b);\n\$a = in_array(\$b, \$c);\n\$a = mb_detect_encoding(\$b, \$c);\n")),
            'The functions "array_keys", "array_search", "base64_decode", "in_array" and "mb_detect_encoding" should be used with $strict param.',
            null,
            null,
            'Risky when the function fixed is overridden or if the code relies on non-strict usage.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        static $map = null;

        if (null === $map) {
            $trueToken = new Token(array(T_STRING, 'true'));

            $map = array(
                'array_keys' => array(null, null, $trueToken),
                'array_search' => array(null, null, $trueToken),
                'base64_decode' => array(null, $trueToken),
                'in_array' => array(null, null, $trueToken),
                'mb_detect_encoding' => array(null, array(new Token(array(T_STRING, 'mb_detect_order')), new Token('('), new Token(')')), $trueToken),
            );
        }

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_STRING) && isset($map[$token->getContent()])) {
                $this->fixFunction($tokens, $index, $map[$token->getContent()]);
            }
        }
    }

    private function fixFunction(Tokens $tokens, $functionIndex, array $functionParams)
    {
        $startBraceIndex = $tokens->getNextTokenOfKind($functionIndex, array('('));
        $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startBraceIndex);
        $commaCounter = 0;
        $sawParameter = false;

        for ($index = $startBraceIndex + 1; $index < $endBraceIndex; ++$index) {
            $token = $tokens[$index];

            if (!$token->isWhitespace() && !$token->isComment()) {
                $sawParameter = true;
            }

            if ($token->equals('(')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                continue;
            }

            if ($token->equals(',')) {
                ++$commaCounter;
                continue;
            }
        }

        $functionParamsQuantity = count($functionParams);
        $paramsQuantity = ($sawParameter ? 1 : 0) + $commaCounter;

        if ($paramsQuantity === $functionParamsQuantity) {
            return;
        }

        $tokensToInsert = array();
        for ($i = $paramsQuantity; $i < $functionParamsQuantity; ++$i) {
            // function call do not have all params that are required to set useStrict flag, exit from method!
            if (!$functionParams[$i]) {
                return;
            }

            $tokensToInsert[] = new Token(',');
            $tokensToInsert[] = new Token(array(T_WHITESPACE, ' '));

            if (!is_array($functionParams[$i])) {
                $tokensToInsert[] = clone $functionParams[$i];
                continue;
            }

            foreach ($functionParams[$i] as $param) {
                $tokensToInsert[] = clone $param;
            }
        }

        $beforeEndBraceIndex = $tokens->getPrevNonWhitespace($endBraceIndex);
        $tokens->insertAt($beforeEndBraceIndex + 1, $tokensToInsert);
    }
}
