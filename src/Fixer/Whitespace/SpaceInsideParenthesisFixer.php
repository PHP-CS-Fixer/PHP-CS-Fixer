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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * There must be a space inside the parenthesis.
 *
 * @author Tareq Hasan <tareq@wedevs.com>
 */
final class SpaceInsideParenthesisFixer extends AbstractFixer
{
    private $singleLineWhitespaceOptions = " \t";

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There MUST be a space after the opening parenthesis and a space before the closing parenthesis.',
            [
                new CodeSample(
                    '<?php

class Foo
{
    public static function bar($baz , $foo)
    {
        return false;
    }
}

function  foo( $bar, $baz )
{
    return false;
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals('(')) {
                continue;
            }

            // don't process if the next token is `)`
            $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken($index);
            if (')' === $tokens[$nextMeaningfulTokenIndex]->getContent()) {
                continue;
            }

            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

            $afterParenthesisIndex = $tokens->getNextNonWhitespace($endParenthesisIndex);
            $afterParenthesisToken = $tokens[$afterParenthesisIndex];

            if ($afterParenthesisToken->isGivenKind(CT::T_USE_LAMBDA)) {
                $useStartParenthesisIndex = $tokens->getNextTokenOfKind($afterParenthesisIndex, ['(']);
                $useEndParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $useStartParenthesisIndex);

                // add single-line edge whitespaces inside use parentheses
                $this->fixParenthesisInnerEdge($tokens, $useStartParenthesisIndex, $useEndParenthesisIndex);
            }

            // add single-line edge whitespaces inside parameters list parentheses
            $this->fixParenthesisInnerEdge($tokens, $index, $endParenthesisIndex);
        }
    }

    private function fixParenthesisInnerEdge(Tokens $tokens, $start, $end)
    {
        // add single-line whitespace before )
        if (!$tokens[$end - 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens->ensureWhitespaceAtIndex($end, 0, ' ');
        }

        // add single-line whitespace after (
        if (!$tokens[$start + 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens->ensureWhitespaceAtIndex($start, 1, ' ');
        }
    }
}
