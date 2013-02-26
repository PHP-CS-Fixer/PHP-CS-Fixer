<?php
/**
 * Squiz_Sniffs_NamingConventions_ValidFunctionNameSniff.
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

if (class_exists('Generic_Sniffs_PHP_LowerCaseConstantSniff', true) === false) {
    $error = 'Class Generic_Sniffs_PHP_LowerCaseConstantSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

if (class_exists('Generic_Sniffs_PHP_UpperCaseConstantSniff', true) === false) {
    $error = 'Class Generic_Sniffs_PHP_UpperCaseConstantSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Squiz_Sniffs_NamingConventions_ConstantCaseSniff.
 *
 * Ensures TRUE, FALSE and NULL are uppercase for PHP and lowercase for JS.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_NamingConventions_ConstantCaseSniff extends Generic_Sniffs_PHP_LowerCaseConstantSniff
{


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($phpcsFile->tokenizerType === 'JS') {
            parent::process($phpcsFile, $stackPtr);
        } else {
            $sniff = new Generic_Sniffs_PHP_UpperCaseConstantSniff;
            $sniff->process($phpcsFile, $stackPtr);
        }

    }//end process()


}//end class

?>
