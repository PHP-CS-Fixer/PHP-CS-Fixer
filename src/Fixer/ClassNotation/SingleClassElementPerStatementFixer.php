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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶4.2.
 *
 * @author Javier Spagnoletti <phansys@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleClassElementPerStatementFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ClassAttributesSeparationFixer.
     */
    public function getPriority(): int
    {
        return 56;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There MUST NOT be more than one property or constant declared per statement.',
            [
                new CodeSample(
                    '<?php
final class Example
{
    const FOO_1 = 1, FOO_2 = 2;
    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
}
'
                ),
                new CodeSample(
                    '<?php
final class Example
{
    const FOO_1 = 1, FOO_2 = 2;
    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
}
',
                    ['elements' => ['property']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $analyzer = new TokensAnalyzer($tokens);
        $elements = array_reverse($analyzer->getClassyElements(), true);

        foreach ($elements as $index => $element) {
            if (!\in_array($element['type'], $this->configuration['elements'], true)) {
                continue; // not in configuration
            }

            $this->fixElement($tokens, $element['type'], $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $values = ['const', 'property'];

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('elements', 'List of strings which element should be modified.'))
                ->setDefault($values)
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($values)])
                ->getOption(),
        ]);
    }

    private function fixElement(Tokens $tokens, string $type, int $index): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $repeatIndex = $index;

        while (true) {
            $repeatIndex = $tokens->getNextMeaningfulToken($repeatIndex);
            $repeatToken = $tokens[$repeatIndex];

            if ($tokensAnalyzer->isArray($repeatIndex)) {
                if ($repeatToken->isGivenKind(T_ARRAY)) {
                    $repeatIndex = $tokens->getNextTokenOfKind($repeatIndex, ['(']);
                    $repeatIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $repeatIndex);
                } else {
                    $repeatIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $repeatIndex);
                }

                continue;
            }

            if ($repeatToken->equals(';')) {
                return; // no repeating found, no fixing needed
            }

            if ($repeatToken->equals(',')) {
                break;
            }
        }

        $start = $tokens->getPrevTokenOfKind($index, [';', '{', '}']);
        $this->expandElement(
            $tokens,
            $type,
            $tokens->getNextMeaningfulToken($start),
            $tokens->getNextTokenOfKind($index, [';'])
        );
    }

    private function expandElement(Tokens $tokens, string $type, int $startIndex, int $endIndex): void
    {
        $divisionContent = null;

        if ($tokens[$startIndex - 1]->isWhitespace()) {
            $divisionContent = $tokens[$startIndex - 1]->getContent();

            if (Preg::match('#(\n|\r\n)#', $divisionContent, $matches)) {
                $divisionContent = $matches[0].trim($divisionContent, "\r\n");
            }
        }

        // iterate variables to split up
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $token = $tokens[$i];

            if ($token->equals(')')) {
                $i = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i);

                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $i = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i);

                continue;
            }

            if (!$tokens[$i]->equals(',')) {
                continue;
            }

            $tokens[$i] = new Token(';');

            if ($tokens[$i + 1]->isWhitespace()) {
                $tokens->clearAt($i + 1);
            }

            if (null !== $divisionContent && '' !== $divisionContent) {
                $tokens->insertAt($i + 1, new Token([T_WHITESPACE, $divisionContent]));
            }

            // collect modifiers
            $sequence = $this->getModifiersSequences($tokens, $type, $startIndex, $endIndex);
            $tokens->insertAt($i + 2, $sequence);
        }
    }

    /**
     * @return Token[]
     */
    private function getModifiersSequences(Tokens $tokens, string $type, int $startIndex, int $endIndex): array
    {
        if ('property' === $type) {
            $tokenKinds = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_VAR, T_STRING, T_NS_SEPARATOR, CT::T_NULLABLE_TYPE, CT::T_ARRAY_TYPEHINT, CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION];

            if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
                $tokenKinds[] = T_READONLY;
            }
        } else {
            $tokenKinds = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_CONST];
        }

        $sequence = [];

        for ($i = $startIndex; $i < $endIndex - 1; ++$i) {
            if ($tokens[$i]->isComment()) {
                continue;
            }

            if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isGivenKind($tokenKinds)) {
                break;
            }

            $sequence[] = clone $tokens[$i];
        }

        return $sequence;
    }
}
