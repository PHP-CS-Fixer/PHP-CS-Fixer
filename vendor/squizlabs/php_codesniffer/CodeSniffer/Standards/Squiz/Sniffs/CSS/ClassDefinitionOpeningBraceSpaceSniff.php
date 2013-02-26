<?php
/**
 * Squiz_Sniffs_CSS_ClassDefinitionOpeningBraceSpaceSniff.
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
 * Squiz_Sniffs_CSS_ClassDefinitionOpeningBraceSpaceSniff.
 *
 * Ensure there is a single space before the opening brace in a class definition
 * and the content starts on the next line.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_CSS_ClassDefinitionOpeningBraceSpaceSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('CSS');


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_OPEN_CURLY_BRACKET);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space before opening brace of class definition; 0 found';
            $phpcsFile->addError($error, $stackPtr, 'NoneBefore');
        } else {
            $content = $tokens[($stackPtr - 1)]['content'];
            if ($content !== ' ') {
                $length = strlen($content);
                if ($length === 1) {
                    $length = 'tab';
                }

                $error = 'Expected 1 space before opening brace of class definition; %s found';
                $data  = array($length);
                $phpcsFile->addError($error, $stackPtr, 'Before', $data);
            }
        }//end if

        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            return;
        }

        // Check for nested class definitions.
        $nested = false;
        $found  = $phpcsFile->findNext(
            T_OPEN_CURLY_BRACKET,
            ($stackPtr + 1),
            $tokens[$stackPtr]['bracket_closer']
        );
        if ($found !== false) {
            $nested = true;
        }

        $foundLines = ($tokens[$next]['line'] - $tokens[$stackPtr]['line'] - 1);
        if ($nested === true) {
            if ($foundLines !== 1) {
                $error = 'Expected 1 blank line after opening brace of nesting class definition; %s found';
                $data  = array($foundLines);
                $phpcsFile->addError($error, $stackPtr, 'AfterNesting', $data);
            }
        } else {
            if ($foundLines !== 0) {
                $error = 'Expected 0 blank lines after opening brace of class definition; %s found';
                $data  = array($foundLines);
                $phpcsFile->addError($error, $stackPtr, 'After', $data);
            }
        }

    }//end process()


}//end class

?>
