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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

/**
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class VisibilityRequiredFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.',
            array(
                new CodeSample(
'<?php
class Sample
{
    var $a;
    static protected $var_foo2;

    function A()
    {
    }
}
'
                ),
                new VersionSpecificCodeSample(
'<?php
class Sample
{
    const SAMPLE = 1;
}
',
                    new VersionSpecification(70100),
                    array('elements' => array('const'))
                ),
            )
        );
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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $element) {
            if (!in_array($element['type'], $this->configuration['elements'], true)) {
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
    protected function createConfigurationDefinition()
    {
        $generator = new FixerOptionValidatorGenerator();

        $elements = new FixerOptionBuilder('elements', 'The structural elements to fix (PHP >= 7.1 required for `const`).');
        $elements = $elements
            ->setAllowedTypes(array('array'))
            ->setAllowedValues(array(
                $generator->allowedValueIsSubsetOf(array('property', 'method', 'const')),
            ))
            ->setNormalizer(function (Options $options, $value) {
                if (PHP_VERSION_ID < 70100 && in_array('const', $value, true)) {
                    throw new InvalidOptionsException('"const" option can only be enabled with PHP 7.1+.');
                }

                return $value;
            })
            ->setDefault(array('property', 'method'))
            ->getOption()
        ;

        return new FixerConfigurationResolverRootless('elements', array($elements));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixMethodVisibility(Tokens $tokens, $index)
    {
        $this->overrideAttribs($tokens, $index, $this->grabAttribsBeforeMethodToken($tokens, $index));

        // force whitespace between function keyword and function name to be single space char
        if ($tokens[$index + 1]->isWhitespace()) {
            $tokens[$index + 1] = new Token(array(T_WHITESPACE, ' '));
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
