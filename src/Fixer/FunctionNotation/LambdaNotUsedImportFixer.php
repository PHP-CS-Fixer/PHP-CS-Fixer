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
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\VariableAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author SpacePossum
 */
final class LambdaNotUsedImportFixer extends AbstractFixer
{
    /**
     * @var ArgumentsAnalyzer
     */
    private $argumentsAnalyzer;

    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;

    /**
     * @var VariableAnalyzer
     */
    private $variableAnalyzer;

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Lambda must not import variables it doesn\'t use.',
            [new CodeSample("<?php\n\$foo = function() use (\$bar) {};\n")]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoSpacesInsideParenthesisFixer.
     */
    public function getPriority()
    {
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, CT::T_USE_LAMBDA]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->argumentsAnalyzer = new ArgumentsAnalyzer();
        $this->tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->variableAnalyzer = new VariableAnalyzer();

        for ($index = $tokens->count() - 4; $index > 0; --$index) {
            $lambdaUseIndex = $this->getLambdaUseIndex($tokens, $index);

            if (false !== $lambdaUseIndex) {
                $this->fixLambda($tokens, $lambdaUseIndex);
            }
        }
    }

    /**
     * @param int $lambdaUseIndex
     */
    private function fixLambda(Tokens $tokens, $lambdaUseIndex)
    {
        $lambdaUseOpenBraceIndex = $tokens->getNextTokenOfKind($lambdaUseIndex, ['(']);
        $lambdaUseCloseBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $lambdaUseOpenBraceIndex);
        $arguments = $this->argumentsAnalyzer->getArguments($tokens, $lambdaUseOpenBraceIndex, $lambdaUseCloseBraceIndex);

        $imports = $this->filterArguments($tokens, $arguments);

        if (0 === \count($imports)) {
            return; // no imports to remove
        }

        // figure out where the lambda starts ...
        $lambdaOpenIndex = $tokens->getNextTokenOfKind($lambdaUseCloseBraceIndex, ['{']);
        $lambdaEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $lambdaOpenIndex);

        $notUsedImports = $this->variableAnalyzer->filterVariablePossiblyUsed($tokens, $lambdaOpenIndex, $lambdaEndIndex, $imports);
        $notUsedImportsCount = \count($notUsedImports);

        if (0 === $notUsedImportsCount) {
            return; // no not used imports found
        }

        if ($notUsedImportsCount === \count($arguments)) {
            $this->clearImportsAndUse($tokens, $lambdaUseIndex, $lambdaUseCloseBraceIndex); // all imports are not used

            return;
        }

        $this->clearImports($tokens, array_reverse($notUsedImports));
    }

    /**
     * @param int $index
     *
     * @return false|int
     */
    private function getLambdaUseIndex(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind(T_FUNCTION) || !$this->tokensAnalyzer->isLambda($index)) {
            return false;
        }

        $lambdaUseIndex = $tokens->getNextMeaningfulToken($index); // we are @ '(' or '&' after this

        if ($tokens[$lambdaUseIndex]->isGivenKind(CT::T_RETURN_REF)) {
            $lambdaUseIndex = $tokens->getNextMeaningfulToken($lambdaUseIndex);
        }

        $lambdaUseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $lambdaUseIndex); // we are @ ')' after this
        $lambdaUseIndex = $tokens->getNextMeaningfulToken($lambdaUseIndex);

        if (!$tokens[$lambdaUseIndex]->isGivenKind(CT::T_USE_LAMBDA)) {
            return false;
        }

        return $lambdaUseIndex;
    }

    /**
     * @return array
     */
    private function filterArguments(Tokens $tokens, array $arguments)
    {
        $imports = [];

        foreach ($arguments as $start => $end) {
            $info = $this->argumentsAnalyzer->getArgumentInfo($tokens, $start, $end);
            $argument = $info->getNameIndex();

            if ($tokens[$tokens->getPrevMeaningfulToken($argument)]->equals('&')) {
                continue;
            }

            $imports[$tokens[$argument]->getContent()] = $argument;
        }

        return $imports;
    }

    private function clearImports(Tokens $tokens, array $imports)
    {
        foreach ($imports as $content => $removeIndex) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($removeIndex);
            $previousRemoveIndex = $tokens->getPrevMeaningfulToken($removeIndex);

            if ($tokens[$previousRemoveIndex]->equals(',')) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($previousRemoveIndex);
            } elseif ($tokens[$previousRemoveIndex]->equals('(')) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($tokens->getNextMeaningfulToken($removeIndex)); // next is always ',' here
            }
        }
    }

    /**
     * Remove `use` and all imported variables.
     *
     * @param int $lambdaUseIndex
     * @param int $lambdaUseCloseBraceIndex
     */
    private function clearImportsAndUse(Tokens $tokens, $lambdaUseIndex, $lambdaUseCloseBraceIndex)
    {
        for ($i = $lambdaUseCloseBraceIndex; $i >= $lambdaUseIndex; --$i) {
            if ($tokens[$i]->isComment()) {
                continue;
            }

            if ($tokens[$i]->isWhitespace()) {
                $previousIndex = $tokens->getPrevNonWhitespace($i);

                if ($tokens[$previousIndex]->isComment()) {
                    continue;
                }
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($i);
        }
    }
}
