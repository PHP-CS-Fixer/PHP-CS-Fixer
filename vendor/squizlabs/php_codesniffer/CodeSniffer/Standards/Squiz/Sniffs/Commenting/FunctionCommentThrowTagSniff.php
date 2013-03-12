<?php
/**
 * Verifies that a @throws tag exists for a function that throws exceptions.
 * Verifies the number of @throws tags and the number of throw tokens matches.
 * Verifies the exception type.
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

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Verifies that a @throws tag exists for a function that throws exceptions.
 * Verifies the number of @throws tags and the number of throw tokens matches.
 * Verifies the exception type.
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
class Squiz_Sniffs_Commenting_FunctionCommentThrowTagSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Commenting_FunctionCommentThrowTagSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_FUNCTION), array(T_THROW));

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        // Is this the first throw token within the current function scope?
        // If so, we have to validate other throw tokens within the same scope.
        $previousThrow = $phpcsFile->findPrevious(T_THROW, ($stackPtr - 1), $currScope);
        if ($previousThrow !== false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $find = array(
                 T_COMMENT,
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentEnd = $phpcsFile->findPrevious($find, ($currScope - 1));

        if ($commentEnd === false) {
            return;
        }

        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
            // Function doesn't have a comment. Let someone else warn about that.
            return;
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $comment      = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($e->getMessage(), $line, 'FailedParse');
            return;
        }

        // Find the position where the current function scope ends.
        $currScopeEnd = 0;
        if (isset($tokens[$currScope]['scope_closer']) === true) {
            $currScopeEnd = $tokens[$currScope]['scope_closer'];
        }

        // Find all the exception type token within the current scope.
        $throwTokens = array();
        $currPos     = $stackPtr;
        if ($currScopeEnd !== 0) {
            while ($currPos < $currScopeEnd && $currPos !== false) {

                /*
                    If we can't find a NEW, we are probably throwing
                    a variable, so we ignore it, but they still need to
                    provide at least one @throws tag, even through we
                    don't know the exception class.
                */

                $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($currPos + 1), null, true);
                if ($tokens[$nextToken]['code'] === T_NEW) {
                    $currException = $phpcsFile->findNext(
                        array(
                         T_NS_SEPARATOR,
                         T_STRING,
                        ),
                        $currPos,
                        $currScopeEnd,
                        false,
                        null,
                        true
                    );

                    if ($currException !== false) {
                        $endException = $phpcsFile->findNext(
                            array(
                             T_NS_SEPARATOR,
                             T_STRING,
                            ),
                            ($currException + 1),
                            $currScopeEnd,
                            true,
                            null,
                            true
                        );

                        if ($endException === false) {
                            $throwTokens[] = $tokens[$currException]['content'];
                        } else {
                            $throwTokens[] = $phpcsFile->getTokensAsString($currException, ($endException - $currException));
                        }
                    }//end if
                }//end if

                $currPos = $phpcsFile->findNext(T_THROW, ($currPos + 1), $currScopeEnd);
            }//end while
        }//end if

        // Only need one @throws tag for each type of exception thrown.
        $throwTokens = array_unique($throwTokens);
        sort($throwTokens);

        $throws = $this->commentParser->getThrows();
        if (empty($throws) === true) {
            $error = 'Missing @throws tag in function comment';
            $phpcsFile->addError($error, $commentEnd, 'Missing');
        } else if (empty($throwTokens) === true) {
            // If token count is zero, it means that only variables are being
            // thrown, so we need at least one @throws tag (checked above).
            // Nothing more to do.
            return;
        } else {
            $throwTags  = array();
            $lineNumber = array();
            foreach ($throws as $throw) {
                $throwTags[]                    = $throw->getValue();
                $lineNumber[$throw->getValue()] = $throw->getLine();
            }

            $throwTags = array_unique($throwTags);
            sort($throwTags);

            // Make sure @throws tag count matches throw token count.
            $tokenCount = count($throwTokens);
            $tagCount   = count($throwTags);
            if ($tokenCount !== $tagCount) {
                $error = 'Expected %s @throws tag(s) in function comment; %s found';
                $data  = array(
                          $tokenCount,
                          $tagCount,
                         );
                $phpcsFile->addError($error, $commentEnd, 'WrongNumber', $data);
                return;
            } else {
                // Exception type in @throws tag must be thrown in the function.
                foreach ($throwTags as $i => $throwTag) {
                    $errorPos = ($commentStart + $lineNumber[$throwTag]);
                    if (empty($throwTag) === false && $throwTag !== $throwTokens[$i]) {
                        $error = 'Expected "%s" but found "%s" for @throws tag exception';
                        $data  = array(
                                  $throwTokens[$i],
                                  $throwTag,
                                 );
                        $phpcsFile->addError($error, $errorPos, 'WrongType', $data);
                    }
                }
            }
        }//end if

    }//end processTokenWithinScope()


}//end class
?>
