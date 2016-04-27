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
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.2.
 *
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class SinglePropertyPerStatementFixer extends AbstractFixer
{
    /**
     * @var string[] Default target/configuration
     */
    private static $defaultTargets = array(
        'property',
        'constant',
    );

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $configuration = self::$defaultTargets;
        } else {
            foreach ($configuration as $name) {
                if (!in_array($name, self::$defaultTargets, true)) {
                    throw new InvalidFixerConfigurationException($this->getName(), sprintf('Unknown configuration option "%s"', $name));
                }
            }
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
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($i = 1, $count = $tokens->count(); $i < $count; ++$i) {
            if (!$tokens[$i]->isClassy()) {
                continue;
            }

            $i = $tokens->getNextTokenOfKind($i, array('{'));

            if (!$elements = $this->getElements($tokens, $i)) {
                continue;
            }

            $lastElement = $elements[count($elements) - 1];
            $i = $lastElement['end'];
            if ($lastElement['expand']) {
                $this->expandTokens($tokens, $elements);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST NOT be more than one property declared per statement.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     *
     * @return array[]
     */
    private function getElements(Tokens $tokens, $startIndex)
    {
        ++$startIndex;
        $elements = array();

        while (true) {
            $element = array(
                'start' => $startIndex,
                'declaration_start' => null,
                'declaration_end' => null,
                'expand' => null,
            );

            for ($i = $startIndex; ; ++$i) {
                /* @var $token \PhpCsFixer\Tokenizer\Token */
                $token = $tokens[$i];

                // class end
                if ($token->equals('}')) {
                    return $elements;
                }

                if ($token->isGivenKind(T_STATIC)) {
                    if (null === $element['declaration_start'] || $i < $element['declaration_start']) {
                        $element['declaration_start'] = $i - 1;
                    }
                    if (null === $element['declaration_end'] || $i > $element['declaration_end']) {
                        $element['declaration_end'] = $i;
                    }
                    continue;
                }

                if ($token->isGivenKind(array(T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE))) {
                    if (null === $element['declaration_start'] || $i < $element['declaration_start']) {
                        $element['declaration_start'] = $i - 1;
                    }
                    if (null === $element['declaration_end'] || $i >= $element['declaration_end']) {
                        $element['declaration_end'] = $i;
                    }
                    continue;
                }

                if ($token->isGivenKind(T_FUNCTION)) {
                    $element['expand'] = false;
                }

                if (!$token->isGivenKind(T_VARIABLE)) {
                    continue;
                }

                if (null === $element['expand'] && $token->isGivenKind(T_VARIABLE)) {
                    $element['expand'] = true;
                }

                $element['end'] = $this->findElementEnd($tokens, $i);
                $element['declaration_end'] = $element['end'];
                break;
            }

            $elements[] = $element;
            $startIndex = $element['end'] + 1;
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function findElementEnd(Tokens $tokens, $index)
    {
        $index = $tokens->getNextTokenOfKind($index, array('{', ';'));

        if ($tokens[$index]->equals('{')) {
            $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        for (++$index; $tokens[$index]->isWhitespace(" \t") || $tokens[$index]->isComment(); ++$index);

        --$index;

        return $tokens[$index]->isWhitespace() ? $index - 1 : $index;
    }

    /**
     * @param Tokens  $tokens
     * @param array[] $elements
     */
    private function expandTokens(Tokens $tokens, array $elements)
    {
        foreach ($elements as $element) {
            $propertyModifiers = array();
            $collectPropertyModifiers = true;
            /* @var $token \PhpCsFixer\Tokenizer\Token */
            for ($i = $element['declaration_start']; $i <= $element['declaration_end']; ++$i) {
                $token = $tokens[$i];

                if ($collectPropertyModifiers && !$token->isGivenKind(T_VARIABLE)) {
                    $propertyModifiers[] = clone $token;
                } else {
                    $collectPropertyModifiers = false;
                }

                if ($token->equals(',', false)) {
                    $token->override(';');
                    $nextIndex = $i + 1;
                    $tokens->overrideRange($nextIndex, $nextIndex, $propertyModifiers);
                }
            }
        }
    }
}
