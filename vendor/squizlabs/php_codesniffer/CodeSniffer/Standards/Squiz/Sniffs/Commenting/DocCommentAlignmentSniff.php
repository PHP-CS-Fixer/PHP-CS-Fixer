<?php
/**
 * Squiz_Sniffs_Commenting_EmptyCatchCommentSniff.
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
 * Squiz_Sniffs_Commenting_DocCommentAlignmentSniff.
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
class Squiz_Sniffs_Commenting_DocCommentAlignmentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_DOC_COMMENT);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // We are only interested in function/class/interface doc block comments.
        $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        $ignore    = array(
                      T_CLASS,
                      T_INTERFACE,
                      T_FUNCTION,
                      T_PUBLIC,
                      T_PRIVATE,
                      T_PROTECTED,
                      T_STATIC,
                      T_ABSTRACT,
                     );

        if (in_array($tokens[$nextToken]['code'], $ignore) === false) {
            // Could be a file comment.
            $prevToken = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prevToken]['code'] !== T_OPEN_TAG) {
                return;
            }
        }

        // We only want to get the first comment in a block. If there is
        // a comment on the line before this one, return.
        $docComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($stackPtr - 1));
        if ($docComment !== false) {
            if ($tokens[$docComment]['line'] === ($tokens[$stackPtr]['line'] - 1)) {
                return;
            }
        }

        $comments       = array($stackPtr);
        $currentComment = $stackPtr;
        $lastComment    = $stackPtr;
        while (($currentComment = $phpcsFile->findNext(T_DOC_COMMENT, ($currentComment + 1))) !== false) {
            if ($tokens[$lastComment]['line'] === ($tokens[$currentComment]['line'] - 1)) {
                $comments[]  = $currentComment;
                $lastComment = $currentComment;
            } else {
                break;
            }
        }

        // The $comments array now contains pointers to each token in the
        // comment block.
        $requiredColumn  = strpos($tokens[$stackPtr]['content'], '*');
        $requiredColumn += $tokens[$stackPtr]['column'];

        foreach ($comments as $commentPointer) {
            // Check the spacing after each asterisk.
            $content   = $tokens[$commentPointer]['content'];
            $firstChar = substr($content, 0, 1);
            $lastChar  = substr($content, -1);
            if ($firstChar !== '/' &&  $lastChar !== '/') {
                $matches = array();
                preg_match('|^(\s+)?\*(\s+)?@|', $content, $matches);
                if (empty($matches) === false) {
                    if (isset($matches[2]) === false) {
                        $error = 'Expected 1 space between asterisk and tag; 0 found';
                        $phpcsFile->addError($error, $commentPointer, 'NoSpaceBeforeTag');
                    } else {
                        $length = strlen($matches[2]);
                        if ($length !== 1) {
                            $error = 'Expected 1 space between asterisk and tag; %s found';
                            $data  = array($length);
                            $phpcsFile->addError($error, $commentPointer, 'SpaceBeforeTag', $data);
                        }
                    }
                }
            }//end foreach

            // Check the alignment of each asterisk.
            $currentColumn  = strpos($content, '*');
            $currentColumn += $tokens[$commentPointer]['column'];

            if ($currentColumn === $requiredColumn) {
                // Star is aligned correctly.
                continue;
            }

            $error = 'Expected %s space(s) before asterisk; %s found';
            $data  = array(
                     ($requiredColumn - 1),
                     ($currentColumn - 1),
                    );
            $phpcsFile->addError($error, $commentPointer, 'SpaceBeforeAsterisk', $data);
        }//end foreach

    }//end process()


}//end class

?>
