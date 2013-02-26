<?php
/**
 * Squiz_Sniffs_Strings_EchoedStringsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Strings_EchoedStringsSniff.
 *
 * Makes sure that any strings that are "echoed" are not enclosed in brackets
 * like a function call.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Strings_EchoedStringsSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_ECHO);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $firstContent = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);
        // If the first non-whitespace token is not an opening parenthesis, then we are not concerned.
        if ($tokens[$firstContent]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $endOfStatement = $phpcsFile->findNext(array(T_SEMICOLON), $stackPtr, null, false);

        // If the token before the semi-colon is not a closing parenthesis, then we are not concerned.
        if ($tokens[($endOfStatement - 1)]['code'] !== T_CLOSE_PARENTHESIS) {
            return;
        }

        if (($phpcsFile->findNext(PHP_CodeSniffer_Tokens::$operators, $stackPtr, $endOfStatement, false)) === false) {
            // There are no arithmetic operators in this.
            $error = 'Echoed strings should not be bracketed';
            $phpcsFile->addError($error, $stackPtr, 'HasBracket');
        }

    }//end process()


}//end class

?>
