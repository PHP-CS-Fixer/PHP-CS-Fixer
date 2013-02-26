<?php
/**
 * Squiz_Sniffs_Files_FileExtensionSniff.
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
 * Squiz_Sniffs_Files_FileExtensionSniff.
 *
 * Tests that the stars in a doc comment align correctly.
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
class Squiz_Sniffs_Files_FileExtensionSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_TAG,
               );

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

        // Make sure this is the first PHP open tag so we don't process
        // the same file twice.
        $prevOpenTag = $phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1));
        if ($prevOpenTag !== false) {
            return;
        }

        $fileName  = $phpcsFile->getFileName();
        $extension = substr($fileName, strrpos($fileName, '.'));
        $nextClass = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), $stackPtr);

        if ($extension === '.php') {
            if ($nextClass !== false) {
                $error = '%s found in ".php" file; use ".inc" extension instead';
                $data  = array(ucfirst($tokens[$nextClass]['content']));
                $phpcsFile->addError($error, $stackPtr, 'ClassFound', $data);
            }
        } else if ($extension === '.inc') {
            if ($nextClass === false) {
                $error = 'No interface or class found in ".inc" file; use ".php" extension instead';
                $phpcsFile->addError($error, $stackPtr, 'NoClass');
            }
        }

    }//end process()


}//end class


?>
