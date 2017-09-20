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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\AbstractFunctionReferenceFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Indicator\PhpUnitIndicator;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitExpectationFixer extends AbstractFunctionReferenceFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The `->setExpectedExpception` MUST be replaced by `->expectException*` methods.',
            [
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    function testFunction()
    {
        $this->setExpectedException("RuntimeException", "Msg.", 123);
    }
}
'
                ),
            ],
            null,
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CLASS);
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
        $argumentsAnalyzer = new ArgumentsAnalyzer();
        $phpUnitIndicator = new PhpUnitIndicator();

        $oldMethodSequence = [
            new Token([T_VARIABLE, '$this']),
            new Token([T_OBJECT_OPERATOR, '->']),
            new Token([T_STRING, 'setExpectedException']),
        ];

        $inPhpUnitClass = false;

        for ($index = 0, $limit = $tokens->count() - 1; $index < $limit; ++$index) {
            if (!$inPhpUnitClass && $tokens[$index]->isGivenKind(T_CLASS) && $phpUnitIndicator->isPhpUnitClass($tokens, $index)) {
                $inPhpUnitClass = true;
            }

            if (!$inPhpUnitClass) {
                continue;
            }

            $match = $tokens->findSequence($oldMethodSequence, $index);

            if (null === $match) {
                return;
            }

            $thisIndex = array_keys($match)[0];
            $index = array_keys($match)[2];

            $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

            $arguments = $argumentsAnalyzer->getArguments($tokens, $openIndex, $closeIndex);
            $argumentsCnt = count($arguments);

            $argumentsReplacements = ['expectException', 'expectExceptionMessage', 'expectExceptionCode']; // TODO option for MessageRegExp for phpunit 5.6

            $indent = "\n".$this->detectIndent($tokens, $thisIndex); // TODO whitespaceaware shit

            $isMultilineWhitespace = false;

            for ($cnt = $argumentsCnt - 1; $cnt >= 1; --$cnt) {
                $argStart = array_keys($arguments)[$cnt];
                $argBefore = $tokens->getPrevMeaningfulToken($argStart);
                $isMultilineWhitespace = $isMultilineWhitespace || ($tokens[$argStart]->isWhitespace() && !$tokens[$argStart]->isWhitespace(" \t"));

                $tokensOverrideArgStart = [
                    new Token([T_WHITESPACE, $indent]),
                    new Token([T_VARIABLE, '$this']),
                    new Token([T_OBJECT_OPERATOR, '->']),
                    new Token([T_STRING, $argumentsReplacements[$cnt]]),
                    new Token('('),
                ];
                $tokensOverrideArgBefore = [
                    new Token(')'),
                    new Token(';'),
                ];

                if ($isMultilineWhitespace) {
                    array_push($tokensOverrideArgStart, new Token([T_WHITESPACE, $indent.'    '])); // TODO whitespaceaware shit
                    array_unshift($tokensOverrideArgBefore, new Token([T_WHITESPACE, $indent]));
                }

                if ($tokens[$argStart]->isWhitespace()) {
                    $tokens->overrideRange($argStart, $argStart, $tokensOverrideArgStart);
                } else {
                    $tokens->insertAt($argStart, $tokensOverrideArgStart);
                }

                $tokens->overrideRange($argBefore, $argBefore, $tokensOverrideArgBefore);

                $limit = $tokens->count();
            }

            $tokens[$index] = new Token([T_STRING, 'expectException']);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return string
     */
    private function detectIndent(Tokens $tokens, $index)
    {
        if (!$tokens[$index - 1]->isWhitespace()) {
            return ''; // cannot detect indent
        }

        $explodedContent = explode("\n", $tokens[$index - 1]->getContent());

        return end($explodedContent);
    }
}
