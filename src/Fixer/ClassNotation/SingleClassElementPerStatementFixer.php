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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶4.2.
 *
 * @author Javier Spagnoletti <phansys@gmail.com>
 * @author SpacePossum
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleClassElementPerStatementFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigurationDefinition()
    {
        $configurationDefinition = new FixerConfigurationResolver();

        $values = array('const', 'property');

        $elements = new FixerOption('elements', 'List of strings which element should be modified.');
        $elements
            ->setDefault($values)
            ->setAllowedValueIsSubsetOf($values)
        ;

        return $configurationDefinition
            ->addOption($elements)
            ->mapRootConfigurationTo('elements')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $analyzer = new TokensAnalyzer($tokens);
        $elements = array_reverse($analyzer->getClassyElements(), true);

        foreach ($elements as $index => $element) {
            if (!in_array($element['type'], $this->configuration['elements'], true)) {
                continue; // not in configuration
            }

            $this->fixElement($tokens, $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There MUST NOT be more than one property or constant declared per statement.',
            array(
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
                    array('elements' => array('property'))
                ),
            )
        );
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixElement(Tokens $tokens, $index)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $repeatIndex = $index;

        while (true) {
            $repeatIndex = $tokens->getNextMeaningfulToken($repeatIndex);
            $repeatToken = $tokens[$repeatIndex];

            if ($tokensAnalyzer->isArray($repeatIndex)) {
                if ($repeatToken->isGivenKind(T_ARRAY)) {
                    $repeatIndex = $tokens->getNextTokenOfKind($repeatIndex, array('('));
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

        $start = $tokens->getPrevTokenOfKind($index, array(';', '{', '}'));
        $this->expandElement(
            $tokens,
            $tokens->getNextMeaningfulToken($start),
            $tokens->getNextTokenOfKind($index, array(';'))
        );
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function expandElement(Tokens $tokens, $startIndex, $endIndex)
    {
        $divisionContent = null;
        if ($tokens[$startIndex - 1]->isWhitespace()) {
            $divisionContent = $tokens[$startIndex - 1]->getContent();
            if (preg_match('#(\n|\r\n)#', $divisionContent, $matches)) {
                $divisionContent = $matches[0].trim($divisionContent, "\r\n");
            }
        }

        // iterate variables to split up
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $token = $tokens[$i];

            if ($token->equals(')')) {
                $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i, false);
                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i, false);
                continue;
            }

            if (!$tokens[$i]->equals(',')) {
                continue;
            }

            $token->setContent(';');
            if ($tokens[$i + 1]->isWhitespace()) {
                $tokens[$i + 1]->clear();
            }

            if ($divisionContent) {
                $tokens->insertAt($i + 1, new Token(array(T_WHITESPACE, $divisionContent)));
            }

            // collect modifiers
            $sequence = $this->getModifiersSequences($tokens, $startIndex, $endIndex);
            $tokens->insertAt($i + 2, $sequence);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     *
     * @return Token[]
     */
    private function getModifiersSequences(Tokens $tokens, $startIndex, $endIndex)
    {
        $sequence = array();
        for ($i = $startIndex; $i < $endIndex - 1; ++$i) {
            if ($tokens[$i]->isWhitespace() || $tokens[$i]->isComment()) {
                continue;
            }

            if (!$tokens[$i]->isGivenKind(array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_CONST, T_VAR))) {
                break;
            }

            $sequence[] = clone $tokens[$i];
            $sequence[] = new Token(array(T_WHITESPACE, ' '));
        }

        return $sequence;
    }
}
