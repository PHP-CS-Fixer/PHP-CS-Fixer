<?php
/**
 * Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff.
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
 * Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff.
 *
 * Checks that the opening brace of a function is on the same line
 * as the function declaration.
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
class Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return void
     */
    public function register()
    {
        return array(T_FUNCTION);

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

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];

        // The end of the function occurs at the end of the argument list. Its
        // like this because some people like to break long function declarations
        // over multiple lines.
        $functionLine = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['line'];
        $braceLine    = $tokens[$openingBrace]['line'];

        $lineDifference = ($braceLine - $functionLine);

        if ($lineDifference > 0) {
            $error = 'Opening brace should be on the same line as the declaration';
            $phpcsFile->addError($error, $openingBrace, 'BraceOnNewLine');
            return;
        }

        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];
        if ($tokens[($closeBracket + 1)]['code'] !== T_WHITESPACE) {
            $length = 0;
        } else if ($tokens[($closeBracket + 1)]['content'] === "\t") {
            $length = '\t';
        } else {
            $length = strlen($tokens[($closeBracket + 1)]['content']);
        }

        if ($length !== 1) {
            $error = 'Expected 1 space after closing parenthesis; found %s';
            $data  = array($length);
            $phpcsFile->addError($error, $closeBracket, 'SpaceAfterBracket', $data);
            return;
        }

        $closeBrace = $tokens[$stackPtr]['scope_opener'];
        if ($tokens[($closeBrace - 1)]['code'] !== T_WHITESPACE) {
            $length = 0;
        } else if ($tokens[($closeBrace - 1)]['content'] === "\t") {
            $length = '\t';
        } else {
            $length = strlen($tokens[($closeBrace - 1)]['content']);
        }

        if ($length !== 1) {
            $error = 'Expected 1 space before opening brace; found %s';
            $data  = array($length);
            $phpcsFile->addError($error, $openingBrace, 'SpaceBeforeBrace', $data);
            return;
        }

    }//end process()


}//end class

?>
