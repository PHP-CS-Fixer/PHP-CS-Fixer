<?php
/**
 * Squiz_Sniffs_Commenting_ClosingDeclarationCommentSniff.
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
 * Squiz_Sniffs_Commenting_ClosingDeclarationCommentSniff.
 *
 * Checks the //end ... comments on classes, interfaces and functions.
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
class Squiz_Sniffs_Commenting_ClosingDeclarationCommentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens..
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_FUNCTION) {

            $methodProps = $phpcsFile->getMethodProperties($stackPtr);

            // Abstract methods do not require a closing comment.
            if ($methodProps['is_abstract'] === true) {
                return;
            }

            // Closures do not require a closing comment.
            if ($methodProps['is_closure'] === true) {
                return;
            }

            // If this function is in an interface then we don't require
            // a closing comment.
            if ($phpcsFile->hasCondition($stackPtr, T_INTERFACE) === true) {
                return;
            }

            if (isset($tokens[$stackPtr]['scope_closer']) === false) {
                $error = 'Possible parse error: non-abstract method defined as abstract';
                $phpcsFile->addWarning($error, $stackPtr, 'Abstract');
                return;
            }

            $decName = $phpcsFile->getDeclarationName($stackPtr);
            $comment = '//end '.$decName.'()';
        } else if ($tokens[$stackPtr]['code'] === T_CLASS) {
            $comment = '//end class';
        } else {
            $comment = '//end interface';
        }//end if

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            $error = 'Possible parse error: %s missing opening or closing brace';
            $data  = array($tokens[$stackPtr]['content']);
            $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $data);
            return;
        }

        $closingBracket = $tokens[$stackPtr]['scope_closer'];

        if ($closingBracket === null) {
            // Possible inline structure. Other tests will handle it.
            return;
        }

        $error = 'Expected '.$comment;
        if (isset($tokens[($closingBracket + 1)]) === false || $tokens[($closingBracket + 1)]['code'] !== T_COMMENT) {
            $phpcsFile->addError($error, $closingBracket, 'Missing');
            return;
        }

        if (rtrim($tokens[($closingBracket + 1)]['content']) !== $comment) {
            $phpcsFile->addError($error, $closingBracket, 'Incorrect');
            return;
        }

    }//end process()


}//end class

?>
