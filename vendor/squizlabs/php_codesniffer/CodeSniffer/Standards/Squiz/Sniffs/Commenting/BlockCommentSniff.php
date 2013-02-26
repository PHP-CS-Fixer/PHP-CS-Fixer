<?php
/**
 * Squiz_Sniffs_Commenting_BlockCommentSniff.
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
 * Squiz_Sniffs_Commenting_BlockCommentSniff.
 *
 * Verifies that block comments are used appropriately.
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
class Squiz_Sniffs_Commenting_BlockCommentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_COMMENT,
                T_DOC_COMMENT,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // If its an inline comment return.
        if (substr($tokens[$stackPtr]['content'], 0, 2) !== '/*') {
            return;
        }

        // If this is a function/class/interface doc block comment, skip it.
        // We are only interested in inline doc block comments.
        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT) {
            $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            $ignore    = array(
                          T_CLASS,
                          T_INTERFACE,
                          T_TRAIT,
                          T_FUNCTION,
                          T_PUBLIC,
                          T_PRIVATE,
                          T_FINAL,
                          T_PROTECTED,
                          T_STATIC,
                          T_ABSTRACT,
                          T_CONST,
                         );
            if (in_array($tokens[$nextToken]['code'], $ignore) === true) {
                return;
            }

            $prevToken = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prevToken]['code'] === T_OPEN_TAG) {
                return;
            }
        }//end if

        $commentLines = array($stackPtr);
        $nextComment  = $stackPtr;
        $lastLine     = $tokens[$stackPtr]['line'];

        // Construct the comment into an array.
        while (($nextComment = $phpcsFile->findNext($tokens[$stackPtr]['code'], ($nextComment + 1), null, false)) !== false) {
            if (($tokens[$nextComment]['line'] - 1) !== $lastLine) {
                // Not part of the block.
                break;
            }

            $lastLine       = $tokens[$nextComment]['line'];
            $commentLines[] = $nextComment;
        }

        if (count($commentLines) <= 2) {
            // Small comment. Can't be right.
            if (count($commentLines) === 1) {
                $error = 'Single line block comment not allowed; use inline ("// text") comment instead';
                $phpcsFile->addError($error, $stackPtr, 'SingleLine');
                return;
            }

            if (trim($tokens[$commentLines[1]]['content']) === '*/') {
                if (trim($tokens[$stackPtr]['content']) === '/*') {
                    $error = 'Empty block comment not allowed';
                    $phpcsFile->addError($error, $stackPtr, 'Empty');
                    return;
                }
            }
        }

        $content = trim($tokens[$stackPtr]['content']);
        if ($content !== '/*' && $content !== '/**') {
            $error = 'Block comment text must start on a new line';
            $phpcsFile->addError($error, $stackPtr, 'NoNewLine');
            return;
        }

        $starColumn = ($tokens[$stackPtr]['column'] + 3);

        // Make sure first line isn't blank.
        if (trim($tokens[$commentLines[1]]['content']) === '') {
            $error = 'Empty line not allowed at start of comment';
            $phpcsFile->addError($error, $commentLines[1], 'HasEmptyLine');
        } else {
            // Check indentation of first line.
            $content      = $tokens[$commentLines[1]]['content'];
            $commentText  = ltrim($content);
            $leadingSpace = (strlen($content) - strlen($commentText));
            if ($leadingSpace !== $starColumn) {
                $expected  = $starColumn;
                $expected .= ($starColumn === 1) ? ' space' : ' spaces';
                $data      = array(
                              $expected,
                              $leadingSpace,
                             );

                $error = 'First line of comment not aligned correctly; expected %s but found %s';
                $phpcsFile->addError($error, $commentLines[1], 'FirstLineIndent', $data);
            }

            if (preg_match('|\p{Lu}|u', $commentText[0]) === 0) {
                $error = 'Block comments must start with a capital letter';
                $phpcsFile->addError($error, $commentLines[1], 'NoCapital');
            }
        }

        // Check that each line of the comment is indented past the star.
        foreach ($commentLines as $line) {
            $leadingSpace = (strlen($tokens[$line]['content']) - strlen(ltrim($tokens[$line]['content'])));
            // First and last lines (comment opener and closer) are handled separately.
            if ($line === $commentLines[(count($commentLines) - 1)] || $line === $commentLines[0]) {
                continue;
            }

            // First comment line was handled above.
            if ($line === $commentLines[1]) {
                continue;
            }

            // If it's empty, continue.
            if (trim($tokens[$line]['content']) === '') {
                continue;
            }

            if ($leadingSpace < $starColumn) {
                $expected  = $starColumn;
                $expected .= ($starColumn === 1) ? ' space' : ' spaces';
                $data      = array(
                              $expected,
                              $leadingSpace,
                             );

                $error = 'Comment line indented incorrectly; expected at least %s but found %s';
                $phpcsFile->addError($error, $line, 'LineIndent', $data);
            }
        }//end foreach

        // Finally, test the last line is correct.
        $lastIndex = (count($commentLines) - 1);
        $content   = trim($tokens[$commentLines[$lastIndex]]['content']);
        if ($content !== '*/' && $content !== '**/') {
            $error = 'Comment closer must be on a new line';
            $phpcsFile->addError($error, $commentLines[$lastIndex]);
        } else {
            $content      = $tokens[$commentLines[$lastIndex]]['content'];
            $commentText  = ltrim($content);
            $leadingSpace = (strlen($content) - strlen($commentText));
            if ($leadingSpace !== ($tokens[$stackPtr]['column'] - 1)) {
                $expected  = ($tokens[$stackPtr]['column'] - 1);
                $expected .= ($expected === 1) ? ' space' : ' spaces';
                $data      = array(
                              $expected,
                              $leadingSpace,
                             );

                $error = 'Last line of comment aligned incorrectly; expected %s but found %s';
                $phpcsFile->addError($error, $commentLines[$lastIndex], 'LastLineIndent', $data);
            }

        }

        // Check that the lines before and after this comment are blank.
        $contentBefore = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if (isset($tokens[$contentBefore]['scope_closer']) === true) {
            if (($tokens[$stackPtr]['line'] - $tokens[$contentBefore]['line']) !== 1) {
                $error = 'Empty line not required before block comment';
                $phpcsFile->addError($error, $stackPtr, 'HasEmptyLineBefore');
            }
        } else {
            if (($tokens[$stackPtr]['line'] - $tokens[$contentBefore]['line']) < 2) {
                $error = 'Empty line required before block comment';
                $phpcsFile->addError($error, $stackPtr, 'NoEmptyLineBefore');
            }
        }

        $commentCloser = $commentLines[$lastIndex];
        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, ($commentCloser + 1), null, true);
        if (($tokens[$contentAfter]['line'] - $tokens[$commentCloser]['line']) < 2) {
            $error = 'Empty line required after block comment';
            $phpcsFile->addError($error, $commentCloser, 'NoEmptyLineAfter');
        }

    }//end process()


}//end class


?>
