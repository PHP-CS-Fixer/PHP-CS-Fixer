<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class StrictParamFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        static $map = null;

        if (null === $map) {
            $token = array(
                'true' => new Token(array(T_STRING, 'true')),
                'null' => new Token(array(T_STRING, 'null')),
            );

            $map = array(
                'in_array' => array(null, null, $token['true']),
                'base64_decode' => array(null, $token['true']),
                'array_search' => array(null, null, $token['true']),
                'array_keys' => array(null, null, $token['true']),
                'mb_detect_encoding' => array(null, array(new Token(array(T_STRING, 'mb_detect_order')), new Token('('), new Token(')')), $token['true']),
            );
        }

        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_STRING) && isset($map[$token->getContent()])) {
                $this->fixFunction($tokens, $index, $map[$token->getContent()]);
            }
        }

        return $tokens->generateCode();
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

            if ($token->equals('[')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $index);
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

        $beforeEndBraceIndex = $tokens->getPrevNonWhitespace($endBraceIndex, array());
        $tokens->insertAt($beforeEndBraceIndex + 1, $tokensToInsert);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Functions should be used with $strict param. Warning! This could change code behavior.';
    }
}
