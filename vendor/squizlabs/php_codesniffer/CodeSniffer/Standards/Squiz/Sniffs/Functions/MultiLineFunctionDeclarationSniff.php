<?php
/**
 * Squiz_Sniffs_Functions_MultiLineFunctionDeclarationSniff.
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

if (class_exists('PEAR_Sniffs_Functions_FunctionDeclarationSniff', true) === false) {
    $error = 'Class PEAR_Sniffs_Functions_FunctionDeclarationSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Squiz_Sniffs_Functions_MultiLineFunctionDeclarationSniff.
 *
 * Ensure single and multi-line function declarations are defined correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Functions_MultiLineFunctionDeclarationSniff extends PEAR_Sniffs_Functions_FunctionDeclarationSniff
{


    /**
     * Processes multi-line declarations.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     * @param array                $tokens    The stack of tokens that make up
     *                                        the file.
     *
     * @return void
     */
    public function processMultiLineDeclaration(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tokens)
    {
        // We do everything the parent sniff does, and a bit more.
        parent::processMultiLineDeclaration($phpcsFile, $stackPtr, $tokens);

        $openBracket = $tokens[$stackPtr]['parenthesis_opener'];
        $this->processBracket($phpcsFile, $openBracket, $tokens, 'function');

        if ($tokens[$stackPtr]['code'] !== T_CLOSURE) {
            return;
        }

        $use = $phpcsFile->findNext(T_USE, ($tokens[$stackPtr]['parenthesis_closer'] + 1), $tokens[$stackPtr]['scope_opener']);
        if ($use === false) {
            return;
        }

        $openBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1), null);
        $this->processBracket($phpcsFile, $openBracket, $tokens, 'use');

        // Also check spacing.
        if ($tokens[($use - 1)]['code'] === T_WHITESPACE) {
            $gap = strlen($tokens[($use - 1)]['content']);
        } else {
            $gap = 0;
        }

    }//end processMultiLineDeclaration()


    /**
     * Processes the contents of a single set of brackets.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                  $openBracket The position of the open bracket
     *                                          in the stack passed in $tokens.
     * @param array                $tokens      The stack of tokens that make up
     *                                          the file.
     * @param string               $type        The type of the token the brackets
     *                                          belong to (function or use).
     *
     * @return void
     */
    public function processBracket(PHP_CodeSniffer_File $phpcsFile, $openBracket, $tokens, $type='function')
    {
        $errorPrefix = '';
        if ($type === 'use') {
            $errorPrefix = 'Use';
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        // The open bracket should be the last thing on the line.
        if ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']) {
            $next = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), null, true);
            if ($tokens[$next]['line'] !== ($tokens[$openBracket]['line'] + 1)) {
                $error = 'The first parameter of a multi-line '.$type.' declaration must be on the line after the opening bracket';
                $phpcsFile->addError($error, $next, $errorPrefix.'FirstParamSpacing');
            }
        }

        // Each line between the brackets should contain a single parameter.
        $lastCommaLine = null;
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            // Skip brackets, like arrays, as they can contain commas.
            if (isset($tokens[$i]['parenthesis_opener']) === true) {
                $i = $tokens[$i]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$i]['code'] === T_COMMA) {
                if ($lastCommaLine !== null && $lastCommaLine === $tokens[$i]['line']) {
                    $error = 'Multi-line '.$type.' declarations must define one parameter per line';
                    $phpcsFile->addError($error, $i, $errorPrefix.'OneParamPerLine');
                } else {
                    // Comma must be the last thing on the line.
                    $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), null, true);
                    if ($tokens[$next]['line'] !== ($tokens[$i]['line'] + 1)) {
                        $error = 'Commas in multi-line '.$type.' declarations must be the last content on a line';
                        $phpcsFile->addError($error, $next, $errorPrefix.'ContentAfterComma');
                    }
                }

                $lastCommaLine = $tokens[$i]['line'];
            }
        }

    }//end processBracket()


}//end class

?>
