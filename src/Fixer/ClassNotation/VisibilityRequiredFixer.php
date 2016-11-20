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
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class VisibilityRequiredFixer extends AbstractFixer
{
    private $options = array('property', 'method', 'const');
    private static $defaultConfiguration = array('property', 'method');
    private $configuration;

    /**
     * Any of the class elements 'property', 'method' or 'const' can be configured.
     *
     * Note: the 'const' configuration is only valid when running on PHP >= 7.1
     * Use 'null' for default configuration ('property', 'method').
     *
     * @param string[]|null $configuration
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        $this->configuration = array();
        foreach ($configuration as $item) {
            if (!is_string($item)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Expected string got "%s".', is_object($item) ? get_class($item) : gettype($item)));
            }

            if (!in_array($item, $this->options, true)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Unknown configuration item "%s", expected any of "%s".', $item, implode('", "', $this->options)));
            }

            if ('const' === $item && PHP_VERSION_ID < 70100) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Invalid configuration item "%s" for PHP "%s".', $item, phpversion()));
            }

            $this->configuration[] = $item;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $element) {
            if (!in_array($element['type'], $this->configuration, true)) {
                continue;
            }

            switch ($element['type']) {
                case 'method':
                    $this->fixMethodVisibility($tokens, $index);

                    break;
                case 'property':
                    $this->fixPropertyVisibility($tokens, $index);

                    break;
                case 'const':
                    $this->fixConstVisibility($tokens, $index);

                    break;
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
    protected function getDescription()
    {
        return 'Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixMethodVisibility(Tokens $tokens, $index)
    {
        $this->overrideAttribs($tokens, $index, $this->grabAttribsBeforeMethodToken($tokens, $index));

        // force whitespace between function keyword and function name to be single space char
        $afterToken = $tokens[++$index];
        if ($afterToken->isWhitespace()) {
            $afterToken->setContent(' ');
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixPropertyVisibility(Tokens $tokens, $index)
    {
        $prevIndex = $tokens->getPrevTokenOfKind($index, array(';', ',', '{'));

        if (null === $prevIndex || !$tokens[$prevIndex]->equals(',')) {
            $this->overrideAttribs($tokens, $index, $this->grabAttribsBeforePropertyToken($tokens, $index));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixConstVisibility(Tokens $tokens, $index)
    {
        $prev = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prev]->isGivenKind(array(T_PRIVATE, T_PROTECTED, T_PUBLIC))) {
            return;
        }

        $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
        $tokens->insertAt($index, new Token(array(T_PUBLIC, 'public')));
    }

    /**
     * Grab attributes before method token at given index.
     *
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param Tokens $tokens Tokens collection
     * @param int    $index  token index
     *
     * @return array map of grabbed attributes, key is attribute name and value is array of index and clone of Token
     */
    private function grabAttribsBeforeMethodToken(Tokens $tokens, $index)
    {
        static $tokenAttribsMap = array(
            T_PRIVATE => 'visibility',
            T_PROTECTED => 'visibility',
            T_PUBLIC => 'visibility',
            T_ABSTRACT => 'abstract',
            T_FINAL => 'final',
            T_STATIC => 'static',
        );

        return $this->grabAttribsBeforeToken(
            $tokens,
            $index,
            $tokenAttribsMap,
            array(
                'abstract' => null,
                'final' => null,
                'visibility' => array('index' => null, 'token' => new Token(array(T_PUBLIC, 'public'))),
                'static' => null,
            )
        );
    }

    /**
     * Apply token attributes.
     *
     * Token at given index is prepended by attributes.
     *
     * @param Tokens $tokens      Tokens collection
     * @param int    $memberIndex token index
     * @param array  $attribs     map of grabbed attributes, key is attribute name and value is array of index and clone of Token
     */
    private function overrideAttribs(Tokens $tokens, $memberIndex, array $attribs)
    {
        $toOverride = array();
        $firstAttribIndex = $memberIndex;

        foreach ($attribs as $attrib) {
            if (null === $attrib) {
                continue;
            }

            if (null !== $attrib['index']) {
                $firstAttribIndex = min($firstAttribIndex, $attrib['index']);
            }

            if (!$attrib['token']->isGivenKind(T_VAR) && '' !== $attrib['token']->getContent()) {
                $toOverride[] = $attrib['token'];
                $toOverride[] = new Token(array(T_WHITESPACE, ' '));
            }
        }

        if (!empty($toOverride)) {
            $tokens->overrideRange($firstAttribIndex, $memberIndex - 1, $toOverride);
        }
    }

    /**
     * Grab attributes before property token at given index.
     *
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param Tokens $tokens Tokens collection
     * @param int    $index  token index
     *
     * @return array map of grabbed attributes, key is attribute name and value is array of index and clone of Token
     */
    private function grabAttribsBeforePropertyToken(Tokens $tokens, $index)
    {
        static $tokenAttribsMap = array(
            T_VAR => 'var',
            T_PRIVATE => 'visibility',
            T_PROTECTED => 'visibility',
            T_PUBLIC => 'visibility',
            T_STATIC => 'static',
        );

        return $this->grabAttribsBeforeToken(
            $tokens,
            $index,
            $tokenAttribsMap,
            array(
                'visibility' => array('index' => null, 'token' => new Token(array(T_PUBLIC, 'public'))),
                'static' => null,
            )
        );
    }

    /**
     * Grab info about attributes before token at given index.
     *
     * @param Tokens $tokens          Tokens collection
     * @param int    $index           token index
     * @param array  $tokenAttribsMap token to attribute name map
     * @param array  $attribs         array of token attributes
     *
     * @return array map of grabbed attributes, key is attribute name and value is array of index and clone of Token
     */
    private function grabAttribsBeforeToken(Tokens $tokens, $index, array $tokenAttribsMap, array $attribs)
    {
        while (true) {
            $token = $tokens[--$index];

            if (!$token->isArray()) {
                if ($token->equalsAny(array('{', '}', '(', ')'))) {
                    break;
                }

                continue;
            }

            // if token is attribute, set token attribute name
            if (isset($tokenAttribsMap[$token->getId()])) {
                $attribs[$tokenAttribsMap[$token->getId()]] = array(
                    'token' => clone $token,
                    'index' => $index,
                );

                continue;
            }

            if ($token->isGivenKind(array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
                continue;
            }

            break;
        }

        return $attribs;
    }
}
