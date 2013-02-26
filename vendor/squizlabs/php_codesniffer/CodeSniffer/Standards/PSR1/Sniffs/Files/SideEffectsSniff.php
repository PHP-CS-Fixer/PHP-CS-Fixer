<?php
/**
 * PSR1_Sniffs_Files_SideEffectsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * PSR1_Sniffs_Files_SideEffectsSniff.
 *
 * Ensures a file declare new symbols and causes no other side effects, or executes
 * logic with side effects, but not both.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PSR1_Sniffs_Files_SideEffectsSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the token stack.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsFile->getTokens();
        $result = $this->_searchForConflict($phpcsFile, 0, ($phpcsFile->numTokens - 1), $tokens);

        if ($result['symbol'] !== null && $result['effect'] !== null) {
            $error = 'A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line %s and the first side effect is on line %s.';
            $data  = array(
                      $tokens[$result['symbol']]['line'],
                      $tokens[$result['effect']]['line'],
                     );
            $phpcsFile->addWarning($error, 0, 'FoundWithSymbols', $data);
        }

    }//end process()


    /**
     * Searches for symbol declarations and side effects.
     *
     * Returns the positions of both the first symbol declared and the first
     * side effect in the file. A NULL value for either indicates nothing was
     * found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $start     The token to start searching from.
     * @param int                  $end       The token to search to.
     * @param array                $tokens    The stack of tokens that make up
     *                                        the file.
     *
     * @return array
     */
    private function _searchForConflict(PHP_CodeSniffer_File $phpcsFile, $start, $end, $tokens)
    {
        $symbols = array(
                    T_CLASS,
                    T_INTERFACE,
                    T_TRAIT,
                    T_FUNCTION,
                   );

        $conditions = array(
                      T_IF,
                      T_ELSE,
                      T_ELSEIF,
                     );

        $firstSymbol = null;
        $firstEffect = null;
        for ($i = $start; $i <= $end; $i++) {
            // Ignore whitespace and comments.
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                continue;
            }

            // Ignore PHP tags.
            if ($tokens[$i]['code'] === T_OPEN_TAG
                || $tokens[$i]['code'] === T_CLOSE_TAG
            ) {
                continue;
            }

            // Ignore entire namespace, const and use statements.
            if ($tokens[$i]['code'] === T_NAMESPACE) {
                $next = $phpcsFile->findNext(array(T_SEMICOLON, T_OPEN_CURLY_BRACKET), ($i + 1));
                if ($next === false) {
                    $next = $i++;
                } else if ($tokens[$next]['code'] === T_OPEN_CURLY_BRACKET) {
                    $next = $tokens[$next]['bracket_closer'];
                }

                $i = $next;
                continue;
            } else if ($tokens[$i]['code'] === T_USE
                || $tokens[$i]['code'] === T_CONST
            ) {
                $i = $phpcsFile->findNext(T_SEMICOLON, ($i + 1));
                continue;
            }

            // Ignore function/class prefixes.
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$methodPrefixes) === true) {
                continue;
            }

            // Detect and skip over symbols.
            if (in_array($tokens[$i]['code'], $symbols) === true
                && isset($tokens[$i]['scope_closer']) === true
            ) {
                if ($firstSymbol === null) {
                    $firstSymbol = $i;
                }

                $i = $tokens[$i]['scope_closer'];
                continue;
            } else if ($tokens[$i]['code'] === T_STRING
                && strtolower($tokens[$i]['content']) === 'define'
            ) {
                if ($firstSymbol === null) {
                    $firstSymbol = $i;
                }

                $i = $phpcsFile->findNext(T_SEMICOLON, ($i + 1));
                continue;
            }

            // Conditional statements are allowed in symbol files as long as the
            // contents is only a symbol definition. So don't count these as effects
            // in this case.
            if (in_array($tokens[$i]['code'], $conditions) === true) {
                if (isset($tokens[$i]['scope_opener']) === false) {
                    // Probably an "else if", so just ignore.
                    continue;
                }

                $result = $this->_searchForConflict(
                    $phpcsFile,
                    ($tokens[$i]['scope_opener'] + 1),
                    ($tokens[$i]['scope_closer'] - 1),
                    $tokens
                );

                if ($result['symbol'] !== null) {
                    if ($firstSymbol === null) {
                        $firstSymbol = $result['symbol'];
                    }

                    if ($result['effect'] !== null) {
                        // Found a conflict.
                        $firstEffect = $result['effect'];
                        break;
                    }
                }

                if ($firstEffect === null) {
                    $firstEffect = $result['effect'];
                }

                $i = $tokens[$i]['scope_closer'];
                continue;
            }//end if

            if ($firstEffect === null) {
                $firstEffect = $i;
            }

            if ($firstSymbol !== null) {
                // We have a conflict we have to report, so no point continuing.
                break;
            }
        }//end for

        return array(
                'symbol' => $firstSymbol,
                'effect' => $firstEffect,
               );

    }//end _searchForConflict()


}//end class

?>
