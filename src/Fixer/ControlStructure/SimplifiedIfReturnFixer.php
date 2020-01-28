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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class SimplifiedIfReturnFixer extends AbstractFixer
{
    private $sequences = [
        [
            'isNegative' => false,
            'sequence' => [
                '{', [T_RETURN], [T_STRING, 'true'], ';', '}',
                [T_RETURN], [T_STRING, 'false'], ';',
            ],
        ],
        [
            'isNegative' => true,
            'sequence' => [
                '{', [T_RETURN], [T_STRING, 'false'], ';', '}',
                [T_RETURN], [T_STRING, 'true'], ';',
            ],
        ],
        [
            'isNegative' => false,
            'sequence' => [
                [T_RETURN], [T_STRING, 'true'], ';',
                [T_RETURN], [T_STRING, 'false'], ';',
            ],
        ],
        [
            'isNegative' => true,
            'sequence' => [
                [T_RETURN], [T_STRING, 'false'], ';',
                [T_RETURN], [T_STRING, 'true'], ';',
            ],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Simplify `if` control structures that return the boolean result of their condition.',
            [new CodeSample("<?php\nif (\$foo) { return true; } return false;\n")]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoMultilineWhitespaceBeforeSemicolonsFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer.
     * Must run after NoSuperfluousElseifFixer, NoUnneededCurlyBracesFixer, NoUselessElseFixer, SemicolonAfterInstructionFixer.
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([T_IF, T_RETURN, T_STRING]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($ifIndex = $tokens->count() - 1; 0 <= $ifIndex; --$ifIndex) {
            $ifToken = $tokens[$ifIndex];

            if (!$ifToken->isGivenKind([T_IF, T_ELSEIF])) {
                continue;
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($ifIndex, ['(']);
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
            $firstCandidateIndex = $tokens->getNextMeaningfulToken($endParenthesisIndex);

            foreach ($this->sequences as $sequenceSpec) {
                $sequenceFound = $tokens->findSequence($sequenceSpec['sequence'], $firstCandidateIndex);

                if (null === $sequenceFound) {
                    continue;
                }

                $firstSequenceIndex = key($sequenceFound);

                if ($firstSequenceIndex !== $firstCandidateIndex) {
                    continue;
                }

                $indexesToClear = array_keys($sequenceFound);
                array_pop($indexesToClear); // Preserve last semicolon
                rsort($indexesToClear);

                foreach ($indexesToClear as $index) {
                    $tokens->clearTokenAndMergeSurroundingWhitespace($index);
                }

                $newTokens = [
                    new Token([T_RETURN, 'return']),
                    new Token([T_WHITESPACE, ' ']),
                ];

                if ($sequenceSpec['isNegative']) {
                    $newTokens[] = new Token('!');
                } else {
                    $newTokens[] = new Token([T_BOOL_CAST, '(bool)']);
                }

                $tokens->overrideRange($ifIndex, $ifIndex, $newTokens);
            }
        }
    }
}
