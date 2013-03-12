<?php
/**
 * Squiz_Sniffs_PHP_EmbeddedPhpSniff.
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
 * Squiz_Sniffs_PHP_EmbeddedPhpSniff.
 *
 * Checks the indentation of embedded PHP code segments.
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
class Squiz_Sniffs_PHP_EmbeddedPhpSniff implements PHP_CodeSniffer_Sniff
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

        // If the close php tag is on the same line as the opening
        // then we have an inline embedded PHP block.
        $closeTag = $phpcsFile->findNext(array(T_CLOSE_TAG), $stackPtr);
        if ($closeTag === false) {
            return;
        }

        if ($tokens[$stackPtr]['line'] !== $tokens[$closeTag]['line']) {
            $this->_validateMultilineEmbeddedPhp($phpcsFile, $stackPtr);
        } else {
            $this->_validateInlineEmbeddedPhp($phpcsFile, $stackPtr);
        }

    }//end process()


    /**
     * Validates embedded PHP that exists on multiple lines.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    private function _validateMultilineEmbeddedPhp(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prevTag = $phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1));
        if ($prevTag === false) {
            // This is the first open tag.
            return;
        }

        // This isn't the first opening tag.
        $closingTag = $phpcsFile->findNext(T_CLOSE_TAG, $stackPtr);
        if ($closingTag === false) {
            // No closing tag? Problem.
            return;
        }

        $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($closingTag + 1), $phpcsFile->numTokens, true);
        if ($nextContent === false) {
            // Final closing tag. It will be handled elsewhere.
            return;
        }

        // Make sure the lines are opening and closing on different lines.
        if ($tokens[$stackPtr]['line'] === $tokens[$closingTag]['line']) {
            return;
        }

        // We have an opening and a closing tag, that lie within other content.
        // They are also on different lines.
        $firstContent = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), $closingTag, true);
        if ($firstContent === false) {
            $error = 'Empty embedded PHP tag found';
            $phpcsFile->addError($error, $stackPtr, 'Empty');
            return;
        }

        // Check for a blank line at the top.
        if ($tokens[$firstContent]['line'] > ($tokens[$stackPtr]['line'] + 1)) {
            // Find a token on the blank line to throw the error on.
            $i = $stackPtr;
            do {
                $i++;
            } while ($tokens[$i]['line'] !== ($tokens[$stackPtr]['line'] + 1));

            $error = 'Blank line found at start of embedded PHP content';
            $phpcsFile->addError($error, $i, 'SpacingBefore');
        } else if ($tokens[$firstContent]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Opening PHP tag must be on a line by itself';
            $phpcsFile->addError($error, $stackPtr, 'ContentAfterOpen');
        }

        // Check the indent of the first line.
        $startColumn   = $tokens[$stackPtr]['column'];
        $contentColumn = $tokens[$firstContent]['column'];
        if ($contentColumn !== $startColumn) {
            $error = 'First line of embedded PHP code must be indented %s spaces; %s found';
            $data  = array(
                      $startColumn,
                      $contentColumn,
                     );
            $phpcsFile->addError($error, $firstContent, 'Indent', $data);
        }

        // Check for a blank line at the bottom.
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($closingTag - 1), ($stackPtr + 1), true);
        if ($tokens[$lastContent]['line'] < ($tokens[$closingTag]['line'] - 1)) {
            // Find a token on the blank line to throw the error on.
            $i = $closingTag;
            do {
                $i--;
            } while ($tokens[$i]['line'] !== ($tokens[$closingTag]['line'] - 1));

            $error = 'Blank line found at end of embedded PHP content';
            $phpcsFile->addError($error, $i, 'SpacingAfter');
        } else if ($tokens[$lastContent]['line'] === $tokens[$closingTag]['line']) {
            $error = 'Closing PHP tag must be on a line by itself';
            $phpcsFile->addError($error, $closingTag, 'ContentAfterEnd');
        }

    }//end _validateMultilineEmbeddedPhp()


    /**
     * Validates embedded PHP that exists on one line.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    private function _validateInlineEmbeddedPhp(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // We only want one line PHP sections, so return if the closing tag is
        // on the next line.
        $closeTag = $phpcsFile->findNext(array(T_CLOSE_TAG), $stackPtr, null, false);
        if ($tokens[$stackPtr]['line'] !== $tokens[$closeTag]['line']) {
            return;
        }

        // Check that there is one, and only one space at the start of the statement.
        $firstContent = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);

        if ($firstContent === false || $tokens[$firstContent]['code'] === T_CLOSE_TAG) {
            $error = 'Empty embedded PHP tag found';
            $phpcsFile->addError($error, $stackPtr, 'Empty');
            return;
        }

        $leadingSpace = '';
        for ($i = ($stackPtr + 1); $i < $firstContent; $i++) {
            $leadingSpace .= $tokens[$i]['content'];
        }

        if (strlen($leadingSpace) >= 1) {
            $error = 'Expected 1 space after opening PHP tag; %s found';
            $data  = array((strlen($leadingSpace) + 1));
            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterOpen', $data);
        }

        $semiColonCount = 0;
        $semiColon      = $stackPtr;
        $lastSemiColon  = $semiColon;

        while (($semiColon = $phpcsFile->findNext(array(T_SEMICOLON), ($semiColon + 1), $closeTag)) !== false) {
            $lastSemiColon = $semiColon;
            $semiColonCount++;
        }

        $semiColon = $lastSemiColon;
        $error     = '';

        // Make sure there is atleast 1 semicolon.
        if ($semiColonCount === 0) {
            $error = 'Inline PHP statement must end with a semicolon';
            $phpcsFile->addError($error, $stackPtr, 'NoSemicolon');
            return;
        }

        // Make sure that there aren't more semicolons than are allowed.
        if ($semiColonCount > 1) {
            $error = 'Inline PHP statement must contain one statement per line; %s found';
            $data  = array($semiColonCount);
            $phpcsFile->addError($error, $stackPtr, 'MultipleStatements', $data);
        }

        // The statement contains only 1 semicolon, now it must be spaced properly.
        $whitespace = '';
        for ($i = ($semiColon + 1); $i < $closeTag; $i++) {
            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space before closing PHP tag; 0 found';
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceBeforeClose');
                return;
            }

            $whitespace .= $tokens[$i]['content'];
        }

        if (strlen($whitespace) === 1) {
            return;
        }

        if (strlen($whitespace) === 0) {
            $error = 'Expected 1 space before closing PHP tag; 0 found';
            $phpcsFile->addError($error, $stackPtr, 'NoSpaceBeforeClose');
        } else {
            $error = 'Expected 1 space before closing PHP tag; %s found';
            $data  = array(strlen($whitespace));
            $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeClose', $data);
        }

    }//end _validateInlineEmbeddedPhp()


}//end class

?>
