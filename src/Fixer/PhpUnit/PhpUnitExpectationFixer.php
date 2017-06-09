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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Indicator\PhpUnitIndicator;

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

            $arguments = $this->getArguments($tokens, $openIndex, $closeIndex);
            $argumentsCnt = count($arguments);

            $argumentsReplacements = ['expectException', 'expectExceptionMessage', 'expectExceptionCode']; // TODO option for MessageRegExp

            $indent = "\n".$this->detectIndent($tokens, $thisIndex); // TODO whitespaceaware shit

            for ($cnt = $argumentsCnt - 1; $cnt >= 1; --$cnt) {
                $argStart = array_keys($arguments)[$cnt];
                $argBefore = $tokens->getPrevMeaningfulToken($argStart);

                if ($tokens[$argStart]->isWhitespace()) {
                    $tokens->overrideRange($argStart, $argStart, [
                        new Token([T_WHITESPACE, $indent]),
                        new Token([T_VARIABLE, '$this']),
                        new Token([T_OBJECT_OPERATOR, '->']),
                        new Token([T_STRING, $argumentsReplacements[$cnt]]),
                        new Token('('),
                    ]);
                } else {
                    $tokens->insertAt($argStart, [
                        new Token([T_WHITESPACE, $indent]),
                        new Token([T_VARIABLE, '$this']),
                        new Token([T_OBJECT_OPERATOR, '->']),
                        new Token([T_STRING, $argumentsReplacements[$cnt]]),
                        new Token('('),
                    ]);
                }

                $tokens->overrideRange($argBefore, $argBefore, [
                    new Token(')'),
                    new Token(';'),
                ]);

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
