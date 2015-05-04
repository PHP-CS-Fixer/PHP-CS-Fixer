<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Utils;

/**
 * @author Matteo Beccati <matteo@beccati.com>
 */
class EregToPregFixer extends AbstractFixer
{
    /**
     * @var array the list of the ext/ereg function names, their preg equivalent and the preg modifier(s), if any
     *            all condensed in an array of arrays.
     */
    private static $functions = array(
        array('ereg', 'preg_match', ''),
        array('eregi', 'preg_match', 'i'),
        array('ereg_replace', 'preg_replace', ''),
        array('eregi_replace', 'preg_replace', 'i'),
        array('split', 'preg_split', ''),
        array('spliti', 'preg_split', 'i'),
    );

    /**
     * @var array the list of preg delimiters, in order of preference.
     */
    private static $delimiters = array('/', '#', '!');

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
        $end = $tokens->count() - 1;

        foreach (self::$functions as $map) {
            // the sequence is the function name, followed by "(" and a quoted string
            $seq = array(array(T_STRING, $map[0]), '(', array(T_CONSTANT_ENCAPSED_STRING));

            $currIndex = 0;
            while (null !== $currIndex) {
                $match = $tokens->findSequence($seq, $currIndex, $end, false);

                // did we find a match?
                if (null === $match) {
                    break;
                }

                // findSequence also returns the tokens, but we're only interested in the indexes, i.e.:
                // 0 => function name,
                // 1 => bracket "("
                // 2 => quoted string passed as 1st parameter
                $match = array_keys($match);

                // advance tokenizer cursor
                $currIndex = $match[2];

                // ensure it's a function call (not a method / static call)
                $prev = $tokens->getPrevMeaningfulToken($match[0]);
                if (null === $prev || $tokens[$prev]->isGivenKind(array(T_OBJECT_OPERATOR, T_DOUBLE_COLON))) {
                    continue;
                }

                // ensure the first parameter is just a string (e.g. has nothing appended)
                $next = $tokens->getNextMeaningfulToken($match[2]);
                if (null === $next || !$tokens[$next]->equalsAny(array(',', ')'))) {
                    continue;
                }

                // convert to PCRE
                $string = substr($tokens[$match[2]]->getContent(), 1, -1);
                $quote = substr($tokens[$match[2]]->getContent(), 0, 1);
                $delim = $this->getBestDelimiter($string);
                $preg = $delim.addcslashes($string, $delim).$delim.'D'.$map[2];

                // check if the preg is valid
                if (!$this->checkPreg($preg)) {
                    continue;
                }

                // modify function and argument
                $tokens[$match[2]]->setContent($quote.$preg.$quote);
                $tokens[$match[0]]->setContent($map[1]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replace deprecated ereg regular expression functions with preg. Warning! This could change code behavior.';
    }

    /**
     * Check the validity of a PCRE.
     *
     * @param string $pattern the regular expression
     *
     * @return bool
     */
    private function checkPreg($pattern)
    {
        return false !== @preg_match($pattern, '');
    }

    /**
     * Get the delimiter that would require the least escaping in a regular expression.
     *
     * @param string $pattern the regular expression
     *
     * @return string the preg delimiter
     */
    private function getBestDelimiter($pattern)
    {
        // try do find something that's not used
        $delimiters = array();
        foreach (self::$delimiters as $k => $d) {
            if (false === strpos($pattern, $d)) {
                return $d;
            }

            $delimiters[$d] = array(substr_count($pattern, $d), $k);
        }

        // return the least used delimiter, using the position in the list as a tie breaker
        uasort($delimiters, function ($a, $b) {
            if ($a[0] === $b[0]) {
                return Utils::cmpInt($a, $b);
            }

            return $a[0] < $b[0] ? -1 : 1;
        });

        return key($delimiters);
    }
}
