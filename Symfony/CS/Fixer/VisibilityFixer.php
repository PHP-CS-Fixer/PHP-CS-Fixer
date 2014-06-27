<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class VisibilityFixer implements FixerInterface
{
    private $tokens;

    public function fix(\SplFileInfo $file, $content)
    {
        $this->tokens = token_get_all($content);
        $inClass = false;
        $curlyBracesLevel = 0;
        $bracesLevel = 0;

        for ($i = 0, $max = count($this->tokens); $i < $max; ++$i) {
            $token = &$this->tokens[$i];

            if (!$inClass) {
                $inClass = $this->isTokenClassy($token);
                continue;
            }

            if ('(' === $token) {
                ++$bracesLevel;
                continue;
            }

            if (')' === $token) {
                --$bracesLevel;
                continue;
            }

            if ('{' === $token || (is_array($token) && T_CURLY_OPEN === $token[0])) {
                ++$curlyBracesLevel;
                continue;
            }

            if ('}' === $token) {
                --$curlyBracesLevel;

                if (0 === $curlyBracesLevel) {
                    $inClass = false;
                }

                continue;
            }

            if (1 !== $curlyBracesLevel || !is_array($token)) {
                continue;
            }

            if (T_VARIABLE === $token[0] && 0 === $bracesLevel) {
                $this->applyTokenAttribs($i, $this->grabAttribsBeforePropertyToken($i));
                continue;
            }

            if (T_FUNCTION === $token[0]) {
                $this->applyTokenAttribs($i, $this->grabAttribsBeforeMethodToken($i));

                // force whitespace between function keyword and function name to be single space char
                $this->tokens[++$i] = ' ';
            }
        }

        $code = $this->generateCode();

        $this->clearFixerState();

        return $code;
    }

    /**
     * Apply token attributes.
     * Token at given index is prepended by attributes.
     *
     * @param int   $tokenNo token index
     * @param array $attribs array of token attributes
     */
    private function applyTokenAttribs($tokenNo, $attribs)
    {
        $attribsString = '';

        foreach ($attribs as $attrib) {
            if ($attrib) {
                $attribsString .= $attrib.' ';
            }
        }

        $this->tokens[$tokenNo] = $attribsString.$this->tokens[$tokenNo][1];
    }

    /**
     * Clear fixer state after fixing single file.
     * Release memory.
     */
    private function clearFixerState()
    {
        $this->tokens = null;
    }

    /**
     * Grab attributes before token at gixen index.
     * Grabbed attributes are cleared by overriding them with empty string and should be manually applied with applyTokenAttribs method.
     *
     * @param  int   $tokenNo         token index
     * @param  array $tokenAttribsMap token to attribute name map
     * @param  array $attribs         array of token attributes
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforeToken($tokenNo, $tokenAttribsMap, $attribs)
    {
        while (true) {
            $token = &$this->tokens[--$tokenNo];

            if (!is_array($token)) {
                if (in_array($token, array('{', '}', '(', ')', ))) {
                    break;
                }

                continue;
            }

            // if token is attribute
            if (array_key_exists($token[0], $tokenAttribsMap)) {
                // set token attribute if token map defines attribute name for token
                if ($tokenAttribsMap[$token[0]]) {
                    $attribs[$tokenAttribsMap[$token[0]]] = $token[1];
                }

                // clear the token and whitespaces after it
                $token = '';
                $this->tokens[$tokenNo + 1] = '';

                continue;
            }

            if (in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, ))) {
                continue;
            }

            break;
        }

        return $attribs;
    }

    /**
     * Grab attributes before method token at gixen index.
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param  int   $tokenNo token index
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforeMethodToken($tokenNo)
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
            $tokenNo,
            $tokenAttribsMap,
            array(
                'abstract' => '',
                'final' => '',
                'visibility' => 'public',
                'static' => '',
            )
        );
    }

    /**
     * Grab attributes before property token at gixen index.
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param  int   $tokenNo token index
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforePropertyToken($tokenNo)
    {
        static $tokenAttribsMap = array(
            T_VAR => null, // destroy T_VAR token!
            T_PRIVATE => 'visibility',
            T_PROTECTED => 'visibility',
            T_PUBLIC => 'visibility',
            T_STATIC => 'static',
        );

        return $this->grabAttribsBeforeToken(
            $tokenNo,
            $tokenAttribsMap,
            array(
                'visibility' => 'public',
                'static' => '',
            )
        );
    }

    /**
     * Method check if token is one of classy tokens: T_CLASS, T_INTERFACE or T_TRAIT.
     *
     * $param string|array $token token element generated by token_get_all
     * @return bool
     */
    private function isTokenClassy($token)
    {
        static $classTokens = array('T_CLASS', 'T_INTERFACE', 'T_TRAIT');

        return is_array($token) && in_array(token_name($token[0]), $classTokens);
    }

    /**
     * Generate code from tokens.
     *
     * @return string
     */
    private function generateCode()
    {
        $source = '';

        foreach ($this->tokens as $token) {
            $source .= is_array($token) ? $token[1] : $token;
        }

        return $source;
    }

    public function getLevel()
    {
        // defined in PSR2 ¶4.3, ¶4.5
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'visibility';
    }

    public function getDescription()
    {
        return 'Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.';
    }
}
