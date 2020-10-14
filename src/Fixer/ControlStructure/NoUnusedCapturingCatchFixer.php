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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\VariableAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author SpacePossum
 */
final class NoUnusedCapturingCatchFixer extends AbstractFixer
{
    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;

    /**
     * @var VariableAnalyzer
     */
    private $variableAnalyzer;

    public function getDefinition()
    {
        return new FixerDefinition(
            'Remove not used captured exception variables. Requires PHP >= 8.0.',
            [
                new VersionSpecificCodeSample(
                    "<?php\ntry {    foo();\n} catch (\\Exception \$e) {\n    // ignore exception\n}\n",
                    new VersionSpecification(80000)
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 80000 && $tokens->isAllTokenKindsFound([T_CATCH, T_VARIABLE]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->variableAnalyzer = new VariableAnalyzer();

        for ($index = \count($tokens) - 1; $index > 1; --$index) {
            if ($tokens[$index]->isGivenKind(T_CATCH)) {
                $this->fixCatch($tokens, $index);
            }
        }
    }

    /**
     * @param int $index
     */
    private function fixCatch(Tokens $tokens, $index)
    {
        $index = $startIndex = $tokens->getNextTokenOfKind($index, ['(']);

        do {
            $index = $tokens->getNextMeaningfulToken($index);
        } while ($tokens[$index]->isGivenKind([T_NS_SEPARATOR, T_STRING, CT::T_TYPE_ALTERNATION]));

        if (!$tokens[$index]->isGivenKind(T_VARIABLE)) {
            return;
        }

        if ($this->tokensAnalyzer->isSuperGlobal($index)) {
            return;
        }

        $startIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        $startIndex = $tokens->getNextTokenOfKind($startIndex, ['{']);
        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);

        $filtered = $this->variableAnalyzer->filterVariablePossiblyUsed($tokens, $startIndex, $endIndex, [$tokens[$index]->getContent() => $index]);

        if (1 !== \count($filtered)) {
            return;
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($index);

        if ($tokens[$index - 1]->isWhitespace() && !$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index - 1);
        }
    }
}
