<?php
/**
 * PEAR_Sniffs_Files_IncludingFileSniff.
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
 * PEAR_Sniffs_Files_IncludingFileSniff.
 *
 * Checks that the include_once is used in conditional situations, and
 * require_once is used elsewhere. Also checks that brackets do not surround
 * the file being included.
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
class PEAR_Sniffs_Files_IncludingFileSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Conditions that should use include_once
     *
     * @var array(int)
     */
    private static $_conditions = array(
                                   T_IF,
                                   T_ELSE,
                                   T_ELSEIF,
                                   T_SWITCH,
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_INCLUDE_ONCE,
                T_REQUIRE_ONCE,
                T_REQUIRE,
                T_INCLUDE,
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

        $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS) {
            $error = '"%s" is a statement not a function; no parentheses are required';
            $data  = array($tokens[$stackPtr]['content']);
            $phpcsFile->addError($error, $stackPtr, 'BracketsNotRequired', $data);
        }

        $inCondition = (count($tokens[$stackPtr]['conditions']) !== 0) ? true : false;

        // Check to see if this including statement is within the parenthesis
        // of a condition. If that's the case then we need to process it as being
        // within a condition, as they are checking the return value.
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $left => $right) {
                if (isset($tokens[$left]['parenthesis_owner']) === true) {
                    $inCondition = true;
                }
            }
        }

        // Check to see if they are assigning the return value of this
        // including call. If they are then they are probably checking it, so
        // it's conditional.
        $previous = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true) {
            // The have assigned the return value to it, so its conditional.
            $inCondition = true;
        }

        $tokenCode = $tokens[$stackPtr]['code'];
        if ($inCondition === true) {
            // We are inside a conditional statement. We need an include_once.
            if ($tokenCode === T_REQUIRE_ONCE) {
                $error  = 'File is being conditionally included; ';
                $error .= 'use "include_once" instead';
                $phpcsFile->addError($error, $stackPtr, 'UseIncludeOnce');
            } else if ($tokenCode === T_REQUIRE) {
                $error  = 'File is being conditionally included; ';
                $error .= 'use "include" instead';
                $phpcsFile->addError($error, $stackPtr, 'UseInclude');
            }
        } else {
            // We are unconditionally including, we need a require_once.
            if ($tokenCode === T_INCLUDE_ONCE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require_once" instead';
                $phpcsFile->addError($error, $stackPtr, 'UseRequireOnce');
            } else if ($tokenCode === T_INCLUDE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require" instead';
                $phpcsFile->addError($error, $stackPtr, 'UseRequire');
            }
        }//end if

    }//end process()


}//end class

?>
