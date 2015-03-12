<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class VisibilityFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $elements = $tokens->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $element) {
            if ('method' === $element['type']) {
                $this->applyAttribs($tokens, $index, $this->grabAttribsBeforeMethodToken($tokens, $index));

                // force whitespace between function keyword and function name to be single space char
                $tokens[++$index]->setContent(' ');
            } elseif ('property' === $element['type']) {
                $prevIndex = $tokens->getPrevTokenOfKind($index, array(';', ','));
                $nextIndex = $tokens->getNextTokenOfKind($index, array(';', ',', '='));

                if (
                    (!$prevIndex || !$tokens[$prevIndex]->equals(',')) &&
                    (!$nextIndex || !$tokens[$nextIndex]->equals(','))
                ) {
                    $this->applyAttribs($tokens, $index, $this->grabAttribsBeforePropertyToken($tokens, $index));
                }
            }
        }

        return $tokens->generateCode();
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
     * @param Tokens $tokens  Tokens collection
     * @param int    $index   token index
     * @param array  $attribs array of token attributes
     */
    private function applyAttribs(Tokens $tokens, $index, array $attribs)
    {
        $toInsert = array();

        foreach ($attribs as $attrib) {
            if (null !== $attrib && '' !== $attrib->getContent()) {
                $toInsert[] = $attrib;
                $toInsert[] = new Token(array(T_WHITESPACE, ' '));
            }
        }

        if (!empty($toInsert)) {
            $tokens->insertAt($index, $toInsert);
        }
    }

    /**
     * Grab attributes before method token at gixen index.
     *
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param Tokens $tokens Tokens collection
     * @param int    $index  token index
     *
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforeMethodToken(Tokens $tokens, $index)
    {
        static $tokenAttribsMap = array(
            T_PRIVATE   => 'visibility',
            T_PROTECTED => 'visibility',
            T_PUBLIC    => 'visibility',
            T_ABSTRACT  => 'abstract',
            T_FINAL     => 'final',
            T_STATIC    => 'static',
        );

        return $this->grabAttribsBeforeToken(
            $tokens,
            $index,
            $tokenAttribsMap,
            array(
                'abstract' => null,
                'final' => null,
                'visibility' => new Token(array(T_PUBLIC, 'public')),
                'static' => null,
            )
        );
    }

    /**
     * Grab attributes before property token at gixen index.
     *
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param Tokens $tokens Tokens collection
     * @param int    $index  token index
     *
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforePropertyToken(Tokens $tokens, $index)
    {
        static $tokenAttribsMap = array(
            T_VAR       => null, // destroy T_VAR token!
            T_PRIVATE   => 'visibility',
            T_PROTECTED => 'visibility',
            T_PUBLIC    => 'visibility',
            T_STATIC    => 'static',
        );

        return $this->grabAttribsBeforeToken(
            $tokens,
            $index,
            $tokenAttribsMap,
            array(
                'visibility' => new Token(array(T_PUBLIC, 'public')),
                'static' => null,
            )
        );
    }

    /**
     * Grab attributes before token at gixen index.
     *
     * Grabbed attributes are cleared by overriding them with empty string and should be manually applied with applyTokenAttribs method.
     *
     * @param Tokens $tokens          Tokens collection
     * @param int    $index           token index
     * @param array  $tokenAttribsMap token to attribute name map
     * @param array  $attribs         array of token attributes
     *
     * @return array array of grabbed attributes
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

            // if token is attribute
            if (array_key_exists($token->getId(), $tokenAttribsMap)) {
                // set token attribute if token map defines attribute name for token
                if ($tokenAttribsMap[$token->getId()]) {
                    $attribs[$tokenAttribsMap[$token->getId()]] = clone $token;
                }

                // clear the token and whitespaces after it
                $tokens[$index]->clear();
                $tokens[$index + 1]->clear();

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
