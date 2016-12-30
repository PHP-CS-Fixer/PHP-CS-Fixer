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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class IsNullFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private static $configurableOptions = array('use_yoda_style');
    private static $defaultConfiguration = array('use_yoda_style' => true);

    /**
     * @var array<string, bool>
     */
    private $configuration;

    /**
     * 'use_yoda_style' can be configured with a boolean value.
     *
     * @param string[]|null $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        $this->configuration = array();
        /** @var $option string */
        foreach ($configuration as $option => $value) {
            if (!in_array($option, self::$configurableOptions, true)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Unknown configuration item "%s", expected any of "%s".', $option, implode('", "', self::$configurableOptions)));
            }

            if (!is_bool($value)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Expected boolean got "%s".', is_object($value) ? get_class($value) : gettype($value)));
            }

            $this->configuration[$option] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        static $sequenceNeeded = array(array(T_STRING, 'is_null'), '(');

        $currIndex = 0;
        while (null !== $currIndex) {
            $matches = $tokens->findSequence($sequenceNeeded, $currIndex, $tokens->count() - 1, false);

            // stop looping if didn't find any new matches
            if (null === $matches) {
                break;
            }

            // 0 and 1 accordingly are "is_null", "(" tokens
            $matches = array_keys($matches);

            // move the cursor just after the sequence
            list($isNullIndex, $currIndex) = $matches;

            // skip all expressions which are not a function reference
            $inversionCandidateIndex = $prevTokenIndex = $tokens->getPrevMeaningfulToken($matches[0]);
            $prevToken = $tokens[$prevTokenIndex];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            // handle function references with namespaces
            if ($prevToken->isGivenKind(T_NS_SEPARATOR)) {
                $inversionCandidateIndex = $twicePrevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                /** @var Token $twicePrevToken */
                $twicePrevToken = $tokens[$twicePrevTokenIndex];
                if ($twicePrevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION, T_STRING, CT::T_NAMESPACE_OPERATOR))) {
                    continue;
                }

                // get rid of the root namespace when it used and check if the inversion operator provided
                $tokens->removeTrailingWhitespace($prevTokenIndex);
                $tokens[$prevTokenIndex]->clear();
            }

            // check if inversion being used, text comparison is due to not existing constant
            $isInvertedNullCheck = false;
            if ($tokens[$inversionCandidateIndex]->equals('!')) {
                $isInvertedNullCheck = true;

                // get rid of inverting for proper transformations
                $tokens->removeTrailingWhitespace($inversionCandidateIndex);
                $tokens[$inversionCandidateIndex]->clear();
            }

            /* before getting rind of `()` around a parameter, ensure it's not assignment/ternary invariant */
            $referenceEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $matches[1]);
            $isContainingDangerousConstructs = false;
            for ($paramTokenIndex = $matches[1]; $paramTokenIndex <= $referenceEnd; ++$paramTokenIndex) {
                if (in_array($tokens[$paramTokenIndex]->getContent(), array('?', '?:', '='), true)) {
                    $isContainingDangerousConstructs = true;
                    break;
                }
            }

            if (!$isContainingDangerousConstructs) {
                // closing parenthesis removed with leading spaces
                $tokens->removeLeadingWhitespace($referenceEnd);
                $tokens[$referenceEnd]->clear();

                // opening parenthesis removed with trailing spaces
                $tokens->removeLeadingWhitespace($matches[1]);
                $tokens->removeTrailingWhitespace($matches[1]);
                $tokens[$matches[1]]->clear();
            }

            // sequence which we'll use as a replacement
            $replacement = array(
                new Token(array(T_STRING, 'null')),
                new Token(array(T_WHITESPACE, ' ')),
                new Token($isInvertedNullCheck ? array(T_IS_NOT_IDENTICAL, '!==') : array(T_IS_IDENTICAL, '===')),
                new Token(array(T_WHITESPACE, ' ')),
            );

            if (true === $this->configuration['use_yoda_style']) {
                $tokens->overrideRange($isNullIndex, $isNullIndex, $replacement);
            } else {
                $replacement = array_reverse($replacement);
                if ($isContainingDangerousConstructs) {
                    array_unshift($replacement, new Token(array(')')));
                }

                $tokens[$isNullIndex]->clear();
                $tokens->removeTrailingWhitespace($referenceEnd);
                $tokens->overrideRange($referenceEnd, $referenceEnd, $replacement);
            }

            // nested is_null calls support
            $currIndex = $isNullIndex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replaces is_null(parameter) expression with `null === parameter`.',
            array(
                new CodeSample("<?php\n\$a = is_null(\$b);"),
                new CodeSample("<?php\n\$a = is_null(\$b);", array('use_yoda_style' => false)),
            ),
            null,
            'The following can be configured: `use_yoda_style => boolean`',
            self::$defaultConfiguration,
            'Risky when the function `is_null()` is overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }
}
