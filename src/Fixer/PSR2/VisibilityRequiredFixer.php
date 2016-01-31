<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PSR2;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class VisibilityRequiredFixer extends AbstractFixer
{
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
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $element) {
            if ('method' === $element['type']) {
                $this->overrideAttribs($tokens, $index, $this->grabAttribsBeforeMethodToken($tokens, $index));

                // force whitespace between function keyword and function name to be single space char
                $afterToken = $tokens[++$index];
                if ($afterToken->isWhitespace()) {
                    $afterToken->setContent(' ');
                }
            } elseif ('property' === $element['type']) {
                $prevIndex = $tokens->getPrevTokenOfKind($index, array(';', ',', '{'));

                if (!$prevIndex || !$tokens[$prevIndex]->equals(',')) {
                    $this->overrideAttribs($tokens, $index, $this->grabAttribsBeforePropertyToken($tokens, $index));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.';
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
