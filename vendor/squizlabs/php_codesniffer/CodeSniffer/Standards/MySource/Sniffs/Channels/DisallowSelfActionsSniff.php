<?php
/**
 * Ensures that self is not used to call public method in action classes.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_MySource
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Ensures that self is not used to call public method in action classes.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_MySource
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class MySource_Sniffs_Channels_DisallowSelfActionsSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // We are not interested in abstract classes.
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($prev !== false && $tokens[$prev]['code'] === T_ABSTRACT) {
            return;
        }

        // We are only interested in Action classes.
        $classNameToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        $className      = $tokens[$classNameToken]['content'];
        if (substr($className, -7) !== 'Actions') {
            return;
        }

        $foundFunctions = array();
        $foundCalls     = array();

        // Find all static method calls in the form self::method() in the class.
        $classEnd = $tokens[$stackPtr]['scope_closer'];
        for ($i = ($classNameToken + 1); $i < $classEnd; $i++) {
            if ($tokens[$i]['code'] !== T_DOUBLE_COLON) {
                if ($tokens[$i]['code'] === T_FUNCTION) {
                    // Cache the function information.
                    $funcName  = $phpcsFile->findNext(T_STRING, ($i + 1));
                    $funcScope = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$scopeModifiers, ($i - 1));

                    $foundFunctions[$tokens[$funcName]['content']] = strtolower($tokens[$funcScope]['content']);
                }

                continue;
            }

            $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($i - 1), null, true);
            if ($tokens[$prevToken]['content'] !== 'self') {
                continue;
            }

            $funcNameToken = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), null, true);
            if ($tokens[$funcNameToken]['code'] === T_VARIABLE) {
                // We are only interested in function calls.
                continue;
            }

            $funcName = $tokens[$funcNameToken]['content'];

            // We've found the function, now we need to find it and see if it is
            // public, private or protected. If it starts with an underscore we
            // can assume it is private.
            if ($funcName{0} === '_') {
                continue;
            }

            $foundCalls[$i] = $funcName;
        }//end for

        $errorClassName = substr($className, 0, -7);

        foreach ($foundCalls as $token => $funcName) {
            if (isset($foundFunctions[$funcName]) === false) {
                // Function was not in this class, might have come from the parent.
                // Either way, we can't really check this.
                continue;
            } else if ($foundFunctions[$funcName] === 'public') {
                $error = 'Static calls to public methods in Action classes must not use the self keyword; use %s::%s() instead';
                $data  = array(
                          $errorClassName,
                          $funcName,
                         );
                $phpcsFile->addError($error, $token, 'Found', $data);
            }
        }

    }//end process()


}//end class

?>
