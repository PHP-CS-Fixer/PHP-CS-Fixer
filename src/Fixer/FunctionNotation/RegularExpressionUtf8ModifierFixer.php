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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class RegularExpressionUtf8ModifierFixer extends AbstractFixer
{
    // What to fix: $name => [$string, $arrayValues, $arrayKeys]
    const FUNCTIONS = [
        'preg_filter' => [true, true, false],
        'preg_grep' => [true, false, false],
        'preg_match_all' => [true, false, false],
        'preg_match' => [true, false, false],
        'preg_replace_callback_array' => [false, false, true],
        'preg_replace_callback' => [true, true, false],
        'preg_replace' => [true, true, false],
        'preg_split' => [true, false, false],
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Modifier `u` (pattern and subject strings are treated as UTF-8) should be used in PCRE Functions.',
            [new CodeSample("<?php preg_match('/foo/', 'bar');\n")]
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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $whatToFix = $this->getWhatToFix($tokens, $index);
            if (null === $whatToFix) {
                continue;
            }

            $this->fixFunction($tokens, $index, $whatToFix[0], $whatToFix[1], $whatToFix[2]);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return null|array
     */
    private function getWhatToFix(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind(T_STRING)) {
            return null;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        if ($tokens[$prevIndex]->isGivenKind([T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR])) {
            return null;
        }

        if ($tokens[$prevIndex]->isGivenKind(T_NS_SEPARATOR)) {
            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            if ($tokens[$prevPrevIndex]->isGivenKind([T_NEW, T_STRING])) {
                return null;
            }
        }

        $lowercaseContent = strtolower($tokens[$index]->getContent());
        if (!array_key_exists($lowercaseContent, self::FUNCTIONS)) {
            return null;
        }

        return self::FUNCTIONS[$lowercaseContent];
    }

    /**
     * @param Tokens $tokens
     * @param int    $functionIndex
     * @param bool   $fixString
     * @param bool   $fixArrayValues
     * @param bool   $fixArrayKeys
     */
    private function fixFunction(Tokens $tokens, $functionIndex, $fixString, $fixArrayValues, $fixArrayKeys)
    {
        $openingParametersIndex = $tokens->getNextTokenOfKind($functionIndex, ['(']);

        $parameterStartIndex = $tokens->getNextMeaningfulToken($openingParametersIndex);

        $tokensAnalyzer = new TokensAnalyzer($tokens);

        if ($fixString && !$tokensAnalyzer->isArray($parameterStartIndex)) {
            $parameterEndIndex = $this->getFirstParameterEndIndex($tokens, $openingParametersIndex);
            $this->fixExpression($tokens, $parameterEndIndex);
        } elseif (($fixArrayValues || $fixArrayKeys) && $tokensAnalyzer->isArray($parameterStartIndex)) {
            $this->fixArrayElements($tokens, $parameterStartIndex, $fixArrayValues, $fixArrayKeys);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixExpression(Tokens $tokens, $index)
    {
        if ($tokens[$index]->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            $fixedPattern = $this->fixPattern($tokens[$index]->getContent());
            $tokens[$index] = new Token([T_CONSTANT_ENCAPSED_STRING, $fixedPattern]);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     *
     * @return int
     */
    private function getFirstParameterEndIndex(Tokens $tokens, $startIndex)
    {
        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);

        $parameterEndIndex = $tokens->getPrevMeaningfulToken($endIndex);

        for ($i = $parameterEndIndex; $i > $startIndex; --$i) {
            $i = $this->skipComplexElements($tokens, $i);
            if ($tokens[$i]->equals(',')) {
                $parameterEndIndex = $tokens->getPrevMeaningfulToken($i);
            }
        }

        return $parameterEndIndex;
    }

    /**
     * Method to move index over complex elements like function calls or function declarations.
     *
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int New index
     */
    private function skipComplexElements(Tokens $tokens, $index)
    {
        if ($tokens[$index]->equals('}')) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index, false);
        }

        if ($tokens[$index]->equals(')')) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index, false);
        }

        return $index;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param bool   $fixArrayValues
     * @param bool   $fixArrayKeys
     */
    private function fixArrayElements(Tokens $tokens, $index, $fixArrayValues, $fixArrayKeys)
    {
        if ($tokens[$index]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
            $startIndex = $index;
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
        } else {
            $startIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        }

        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $i = $this->skipComplexElements($tokens, $i);
            $currentToken = $tokens[$i];
            if (!$currentToken->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }
            $nextIndex = $tokens->getNextMeaningfulToken($i);
            if ($fixArrayValues && $tokens[$nextIndex]->equalsAny([',', ')', [CT::T_ARRAY_SQUARE_BRACE_CLOSE]])) {
                $fixedPattern = $this->fixPattern($currentToken->getContent());
                $tokens[$i] = new Token([T_CONSTANT_ENCAPSED_STRING, $fixedPattern]);
            }
            if ($fixArrayKeys && $tokens[$nextIndex]->isGivenKind(T_DOUBLE_ARROW)) {
                $fixedPattern = $this->fixPattern($currentToken->getContent());
                $tokens[$i] = new Token([T_CONSTANT_ENCAPSED_STRING, $fixedPattern]);
            }
        }
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    private function fixPattern($pattern)
    {
        $modifiers = preg_replace('/(.*[^a-zA-Z0-1\\\\\s])/u', '', substr($pattern, 1, -1));

        if (false !== strpos($modifiers, 'u')) {
            return $pattern;
        }

        return substr($pattern, 0, -1).'u'.$pattern[strlen($pattern) - 1];
    }
}
