<?php
/**
 * Squiz_Sniffs_WhiteSpace_FunctionOpeningBraceSpaceSniff.
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
 * Squiz_Sniffs_WhiteSpace_FunctionOpeningBraceSpaceSniff.
 *
 * Checks that there is no empty line after the opening brace of a function.
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
class Squiz_Sniffs_WhiteSpace_FunctionOpeningBraceSpaceSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            // Probably an interface method.
            return;
        }

        $openBrace   = $tokens[$stackPtr]['scope_opener'];
        $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($openBrace + 1), null, true);

        if ($nextContent === $tokens[$stackPtr]['scope_closer']) {
             // The next bit of content is the closing brace, so this
             // is an empty function and should have a blank line
             // between the opening and closing braces.
            return;
        }

        $braceLine = $tokens[$openBrace]['line'];
        $nextLine  = $tokens[$nextContent]['line'];

        $found = ($nextLine - $braceLine - 1);
        if ($found > 0) {
            $error = 'Expected 0 blank lines after opening function brace; %s found';
            $data  = array($found);
            $phpcsFile->addError($error, $openBrace, 'SpacingAfter', $data);
        }

        if ($phpcsFile->tokenizerType === 'JS') {
            // Do some additional checking before the function brace.
            $nestedFunction = ($phpcsFile->hasCondition($stackPtr, T_FUNCTION) === true || isset($tokens[$stackPtr]['nested_parenthesis']) === true);

            $functionLine   = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['line'];
            $lineDifference = ($braceLine - $functionLine);

            if ($nestedFunction === true) {
                if ($lineDifference > 0) {
                    $error = 'Expected 0 blank lines before opening brace of nested function; %s found';
                    $data  = array($found);
                    $phpcsFile->addError($error, $openBrace, 'SpacingAfterNested', $data);
                }
            } else {
                if ($lineDifference === 0) {
                    $error = 'Opening brace should be on a new line';
                    $phpcsFile->addError($error, $openBrace, 'ContentBefore');
                    return;
                }

                if ($lineDifference > 1) {
                    $error = 'Opening brace should be on the line after the declaration; found %s blank line(s)';
                    $data  = array(($lineDifference - 1));
                    $phpcsFile->addError($error, $openBrace, 'SpacingBefore', $data);
                    return;
                }
            }//end if
        }//end if

    }//end process()


}//end class

?>
