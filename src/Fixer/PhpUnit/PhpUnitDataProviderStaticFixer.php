<?php

declare(strict_types=1);

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

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tokenizer\Analyzer\DataProviderAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class PhpUnitDataProviderStaticFixer extends AbstractPhpUnitFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Data providers must be static.',
            [
                new CodeSample(
                    '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideSomethingCases
     */
    public function testSomething($expected, $actual) {}
    public function provideSomethingCases() {}
}
'
                ),
            ],
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $dataProviderAnalyzer = new DataProviderAnalyzer();
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $inserts = [];
        foreach ($dataProviderAnalyzer->getDataProviders($tokens, $startIndex, $endIndex) as $dataProviderAnalysis) {
            $methodStartIndex = $tokens->getNextTokenOfKind($dataProviderAnalysis->getNameIndex(), ['{']);
            if (null !== $methodStartIndex) {
                $methodEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $methodStartIndex);

                if (null !== $tokens->findSequence([[T_VARIABLE, '$this']], $methodStartIndex, $methodEndIndex)) {
                    continue;
                }
            }
            $functionIndex = $tokens->getPrevTokenOfKind($dataProviderAnalysis->getNameIndex(), [[T_FUNCTION]]);
            \assert(\is_int($functionIndex));

            $methodAttributes = $tokensAnalyzer->getMethodAttributes($functionIndex);
            if (false !== $methodAttributes['static']) {
                continue;
            }

            $inserts[$functionIndex] = [new Token([T_STATIC, 'static']), new Token([T_WHITESPACE, ' '])];
        }
        $tokens->insertSlices($inserts);
    }
}
