<?php
/**
 * PSR2_Sniffs_ControlStructures_SwitchDeclarationSniff.
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
 * PSR2_Sniffs_ControlStructures_SwitchDeclarationSniff.
 *
 * Ensures all switch statements are defined correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PSR2_Sniffs_ControlStructures_SwitchDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_SWITCH);

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

        // We can't process SWITCH statements unless we know where they start and end.
        if (isset($tokens[$stackPtr]['scope_opener']) === false
            || isset($tokens[$stackPtr]['scope_closer']) === false
        ) {
            return;
        }

        $switch        = $tokens[$stackPtr];
        $nextCase      = $stackPtr;
        $caseAlignment = ($switch['column'] + 4);
        $caseCount     = 0;
        $foundDefault  = false;

        while (($nextCase = $this->_findNextCase($phpcsFile, ($nextCase + 1), $switch['scope_closer'])) !== false) {
            if ($tokens[$nextCase]['code'] === T_DEFAULT) {
                $type         = 'default';
                $foundDefault = true;
            } else {
                $type = 'case';
                $caseCount++;
            }

            if ($tokens[$nextCase]['content'] !== strtolower($tokens[$nextCase]['content'])) {
                $expected = strtolower($tokens[$nextCase]['content']);
                $error    = strtoupper($type).' keyword must be lowercase; expected "%s" but found "%s"';
                $data     = array(
                             $expected,
                             $tokens[$nextCase]['content'],
                            );
                $phpcsFile->addError($error, $nextCase, $type.'NotLower', $data);
            }

            if ($tokens[$nextCase]['column'] !== $caseAlignment) {
                $error = strtoupper($type).' keyword must be indented 4 spaces from SWITCH keyword';
                $phpcsFile->addError($error, $nextCase, $type.'Indent');
            }

            $prevCode   = $phpcsFile->findPrevious(T_WHITESPACE, ($nextCase - 1), $stackPtr, true);
            $blankLines = ($tokens[$nextCase]['line'] - $tokens[$prevCode]['line'] - 1);
            if ($blankLines !== 0) {
                $error = 'Blank lines are not allowed between case statements; found %s';
                $data  = array($blankLines);
                $phpcsFile->addError($error, $nextCase, 'SpaceBetweenCase', $data);
            }

            if ($type === 'case'
                && ($tokens[($nextCase + 1)]['code'] !== T_WHITESPACE
                || $tokens[($nextCase + 1)]['content'] !== ' ')
            ) {
                $error = 'CASE keyword must be followed by a single space';
                $phpcsFile->addError($error, $nextCase, 'SpacingAfterCase');
            }

            $opener = $tokens[$nextCase]['scope_opener'];
            if ($tokens[$opener]['code'] === T_COLON) {
                if ($tokens[($opener - 1)]['code'] === T_WHITESPACE) {
                    $error = 'There must be no space before the colon in a '.strtoupper($type).' statement';
                    $phpcsFile->addError($error, $nextCase, 'SpaceBeforeColon'.$type);
                }
            } else {
                $error = strtoupper($type).' statements must not be defined using curly braces';
                $phpcsFile->addError($error, $nextCase, 'WrongOpener'.$type);
            }

            $nextCloser = $tokens[$nextCase]['scope_closer'];
            if ($tokens[$nextCloser]['scope_condition'] === $nextCase) {
                // Only need to check some things once, even if the
                // closer is shared between multiple case statements, or even
                // the default case.
                if ($tokens[$nextCloser]['column'] !== ($caseAlignment + 4)) {
                    $error = 'Terminating statement must be indented to the same level as the CASE body';
                    $phpcsFile->addError($error, $nextCloser, 'BreakIndent');
                }
            }

            // We only want cases from here on in.
            if ($type !== 'case') {
                continue;
            }

            $nextCode = $phpcsFile->findNext(
                T_WHITESPACE,
                ($tokens[$nextCase]['scope_opener'] + 1),
                $nextCloser,
                true
            );

            if ($tokens[$nextCode]['code'] !== T_CASE && $tokens[$nextCode]['code'] !== T_DEFAULT) {
                // This case statement has content. If the next case or default comes
                // before the closer, it means we dont have a terminating statement
                // and instead need a comment.
                $nextCode = $this->_findNextCase($phpcsFile, ($tokens[$nextCase]['scope_opener'] + 1), $nextCloser);
                if ($nextCode !== false) {
                    $prevCode = $phpcsFile->findPrevious(T_WHITESPACE, ($nextCode - 1), $nextCase, true);
                    if ($tokens[$prevCode]['code'] !== T_COMMENT) {
                        $error = 'There must be a comment when fall-through is intentional in a non-empty case body';
                        $phpcsFile->addError($error, $nextCase, 'TerminatingComment');
                    }
                }
            }
        }//end while

    }//end process()


    /**
     * Find the next CASE or DEFAULT statement from a point in the file.
     *
     * Note that nested switches are ignored.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position to start looking at.
     * @param int                  $end       The position to stop looking at.
     *
     * @return int | bool
     */
    private function _findNextCase(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $end)
    {
        $tokens = $phpcsFile->getTokens();
        while (($stackPtr = $phpcsFile->findNext(array(T_CASE, T_DEFAULT, T_SWITCH), $stackPtr, $end)) !== false) {
            // Skip nested SWITCH statements; they are handled on their own.
            if ($tokens[$stackPtr]['code'] === T_SWITCH) {
                $stackPtr = $tokens[$stackPtr]['scope_closer'];
                continue;
            }

            break;
        }

        return $stackPtr;

    }//end _findNextCase()


}//end class

?>
