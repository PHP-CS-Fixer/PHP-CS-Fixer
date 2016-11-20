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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractAlignFixerHelper;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class BinaryOperatorSpacesFixer extends AbstractFixer
{
    /**
     * @var array<string, bool|null>
     */
    private $configuration;

    private static $defaultConfiguration = array(
        'align_equals' => false,
        'align_double_arrow' => false,
    );

    /**
     * @var AbstractAlignFixerHelper[]
     */
    private $alignFixerHelpers = array();

    /**
     * Key any of; 'align_equals', 'align_double_arrow'.
     * Value 'bool': 'false' do unalign, 'true' do align, or 'null': do not modify.
     *
     * @param array<string, bool|null> $configuration
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        foreach ($configuration as $name => $value) {
            if (!array_key_exists($name, self::$defaultConfiguration)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Unknown configuration option "%s". Expected any of "%s".', $name, implode('", "', array_keys(self::$defaultConfiguration))));
            }

            if (null !== $value && !is_bool($value)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Invalid value type for configuration option "%s". Expected "bool" or "null" got "%s".', $name, is_object($value) ? get_class($value) : gettype($value)));
            }
        }

        $this->configuration = array_merge(self::$defaultConfiguration, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        // last and first tokens cannot be an operator
        for ($index = $tokens->count() - 2; $index >= 0; --$index) {
            if (!$tokensAnalyzer->isBinaryOperator($index)) {
                continue;
            }

            $isDeclare = $this->isDeclareStatement($tokens, $index);
            if (false !== $isDeclare) {
                $index = $isDeclare; // skip `declare(foo ==bar)`, see `declare_equal_normalize`
            } else {
                $this->fixWhiteSpaceAroundOperator($tokens, $index);
            }

            // previous of binary operator is now never an operator / previous of declare statement cannot be an operator
            --$index;
        }

        $this->runHelperFixers($file, $tokens);
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Binary operators should be surrounded by at least one space.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixWhiteSpaceAroundOperator(Tokens $tokens, $index)
    {
        if ($tokens[$index]->isGivenKind(T_DOUBLE_ARROW)) {
            if (true === $this->configuration['align_double_arrow']) {
                if (!isset($this->alignFixerHelpers['align_double_arrow'])) {
                    $this->alignFixerHelpers['align_double_arrow'] = new AlignDoubleArrowFixerHelper();
                }

                return;
            } elseif (null === $this->configuration['align_double_arrow']) {
                return; // configured not to touch the whitespace around the operator
            }
        } elseif ($tokens[$index]->equals('=')) {
            if (true === $this->configuration['align_equals']) {
                if (!isset($this->alignFixerHelpers['align_equals'])) {
                    $this->alignFixerHelpers['align_equals'] = new AlignEqualsFixerHelper();
                }

                return;
            } elseif (null === $this->configuration['align_equals']) {
                return; // configured not to touch the whitespace around the operator
            }
        }

        // fix white space after operator
        if ($tokens[$index + 1]->isWhitespace()) {
            $content = $tokens[$index + 1]->getContent();
            if (' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getNextNonWhitespace($index + 1)]->isComment()) {
                $tokens[$index + 1]->setContent(' ');
            }
        } else {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
        }

        // fix white space before operator
        if ($tokens[$index - 1]->isWhitespace()) {
            $content = $tokens[$index - 1]->getContent();
            if (' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                $tokens[$index - 1]->setContent(' ');
            }
        } else {
            $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return false|int
     */
    private function isDeclareStatement(Tokens $tokens, $index)
    {
        $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_STRING)) {
            $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
            if ($tokens[$prevMeaningfulIndex]->equals('(')) {
                $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
                if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_DECLARE)) {
                    return $prevMeaningfulIndex;
                }
            }
        }

        return false;
    }

    private function runHelperFixers(\SplFileInfo $file, Tokens $tokens)
    {
        /** @var AbstractAlignFixerHelper $helper */
        foreach ($this->alignFixerHelpers as $helper) {
            if ($tokens->isChanged()) {
                $tokens->clearEmptyTokens();
            }

            $helper->fix($file, $tokens);
        }
    }
}
