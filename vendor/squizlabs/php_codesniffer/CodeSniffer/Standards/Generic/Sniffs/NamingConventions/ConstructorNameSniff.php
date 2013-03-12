<?php
/**
 * Generic_Sniffs_NamingConventions_ConstructorNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Leif Wickland <lwickland@rightnow.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Generic_Sniffs_NamingConventions_ConstructorNameSniff.
 *
 * Favor PHP 5 constructor syntax, which uses "function __construct()".
 * Avoid PHP 4 constructor syntax, which uses "function ClassName()".
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Leif Wickland <lwickland@rightnow.com>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_NamingConventions_ConstructorNameSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs the test with the tokens it wishes to listen for.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION), true);

    }//end __construct()


    /**
     * Processes this test when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     * @param int                  $currScope A pointer to the start of the scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $currScope
    ) {
        $className  = $phpcsFile->getDeclarationName($currScope);
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        if (strcasecmp($methodName, $className) === 0) {
            $error = 'PHP4 style constructors are not allowed; use "__construct()" instead';
            $phpcsFile->addError($error, $stackPtr, 'OldStyle');
        } else if (strcasecmp($methodName, '__construct') !== 0) {
            // Not a constructor.
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $parentClassName = $phpcsFile->findExtendedClassName($currScope);
        if ($parentClassName === false) {
            return;
        }

        $endFunctionIndex = $tokens[$stackPtr]['scope_closer'];
        $startIndex       = $stackPtr;
        while ($doubleColonIndex = $phpcsFile->findNext(array(T_DOUBLE_COLON), $startIndex, $endFunctionIndex)) {
            if ($tokens[($doubleColonIndex + 1)]['code'] === T_STRING
                && $tokens[($doubleColonIndex + 1)]['content'] === $parentClassName
            ) {
                $error = 'PHP4 style calls to parent constructors are not allowed; use "parent::__construct()" instead';
                $phpcsFile->addError($error, ($doubleColonIndex + 1), 'OldStyleCall');
            }

            $startIndex = ($doubleColonIndex + 1);
        }

    }//end processTokenWithinScope()


}//end class

?>
