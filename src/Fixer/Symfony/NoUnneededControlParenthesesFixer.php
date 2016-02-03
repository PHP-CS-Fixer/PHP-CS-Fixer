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

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 */
final class NoUnneededControlParenthesesFixer extends AbstractFixer
{
    /**
     * To be removed when PHP support will be 5.5+.
     *
     * @var string[] List of statements to fix.
     */
    private $controlStatements = array(
        'break',
        'clone',
        'continue',
        'echo_print',
        'return',
        'switch_case',
        'yield',
    );

    private static $loops = array(
        'break' => array('lookupTokens' => T_BREAK, 'neededSuccessors' => array(';')),
        'clone' => array('lookupTokens' => T_CLONE, 'neededSuccessors' => array(';', ':', ',', ')')),
        'continue' => array('lookupTokens' => T_CONTINUE, 'neededSuccessors' => array(';')),
        'echo_print' => array('lookupTokens' => array(T_ECHO, T_PRINT), 'neededSuccessors' => array(';', array(T_CLOSE_TAG))),
        'return' => array('lookupTokens' => T_RETURN, 'neededSuccessors' => array(';')),
        'switch_case' => array('lookupTokens' => T_CASE, 'neededSuccessors' => array(';', ':')),
    );

    /**
     * Dynamic yield option set on constructor.
     */
    public function __construct()
    {
        // To be moved back on static when PHP support will be 5.5+
        if (defined('T_YIELD')) {
            self::$loops['yield'] = array('lookupTokens' => T_YIELD, 'neededSuccessors' => array(';', ')'));
        }
    }

    /**
     * @param array $controlStatements
     */
    public function configure(array $controlStatements = null)
    {
        if (null === $controlStatements) {
            return;
        }

        $this->controlStatements = $controlStatements;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        $types = array();

        foreach (self::$loops as $loop) {
            $types = array_merge($types, (array) $loop['lookupTokens']);
        }

        return $tokens->isAnyTokenKindsFound($types);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // Checks if specific statements are set and uses them in this case.
        $loops = array_intersect_key(self::$loops, array_flip($this->controlStatements));

        foreach ($tokens as $index => $token) {
            if (!$token->equals('(')) {
                continue;
            }

            $blockStartIndex = $index;
            $index = $tokens->getPrevMeaningfulToken($index);
            $token = $tokens[$index];

            foreach ($loops as $loop) {
                if (!$token->isGivenKind($loop['lookupTokens'])) {
                    continue;
                }

                $blockEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $blockStartIndex);
                $blockEndNextIndex = $tokens->getNextMeaningfulToken($blockEndIndex);

                if (!$tokens[$blockEndNextIndex]->equalsAny($loop['neededSuccessors'])) {
                    continue;
                }

                if ($tokens[$blockStartIndex - 1]->isWhitespace() || $tokens[$blockStartIndex - 1]->isComment()) {
                    $this->clearParenthesis($tokens, $blockStartIndex);
                } else {
                    // Adds a space to prevent broken code like `return2`.
                    $tokens->overrideAt($blockStartIndex, array(T_WHITESPACE, ' '));
                }

                $this->clearParenthesis($tokens, $blockEndIndex);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes unneeded parentheses around control statements.';
    }

    /**
     * Should be run before no_trailing_whitespace.
     *
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 30;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function clearParenthesis(Tokens $tokens, $index)
    {
        $tokens[$index]->clear();

        if (
            isset($tokens[$index - 1], $tokens[$index + 1]) &&
            $tokens[$index - 1]->isWhitespace() &&
            $tokens[$index + 1]->isWhitespace()
        ) {
            $tokens[$index - 1]->setContent($tokens[$index - 1]->getContent().$tokens[$index + 1]->getContent());
            $tokens[$index + 1]->clear();
        }
    }
}
