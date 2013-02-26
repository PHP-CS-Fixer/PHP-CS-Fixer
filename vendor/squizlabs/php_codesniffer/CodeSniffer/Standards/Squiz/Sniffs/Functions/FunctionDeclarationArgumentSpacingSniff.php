<?php
/**
 * Squiz_Sniffs_Functions_FunctionDeclarationArgumentSpacingSniff.
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
 * Squiz_Sniffs_Functions_FunctionDeclarationArgumentSpacingSniff.
 *
 * Checks that arguments in function declarations are spaced correctly.
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
class Squiz_Sniffs_Functions_FunctionDeclarationArgumentSpacingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * How many spaces should surround the equals signs.
     *
     * @var int
     */
    public $equalsSpacing = 0;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLOSURE,
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
        $this->equalsSpacing = (int) $this->equalsSpacing;

        $tokens       = $phpcsFile->getTokens();
        $openBracket  = $tokens[$stackPtr]['parenthesis_opener'];
        $this->processBracket($phpcsFile, $openBracket);

        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($tokens[$openBracket]['parenthesis_closer'] + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket  = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1), null);
                $this->processBracket($phpcsFile, $openBracket);
            }
        }

    }//end process()


    /**
     * Processes the contents of a single set of brackets.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                  $openBracket The position of the open bracket
     *                                          in the stack passed in $tokens.
     *
     * @return void
     */
    public function processBracket(PHP_CodeSniffer_File $phpcsFile, $openBracket)
    {
        $tokens       = $phpcsFile->getTokens();
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $multiLine    = ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']);

        $nextParam = $openBracket;
        $params    = array();
        while (($nextParam = $phpcsFile->findNext(T_VARIABLE, ($nextParam + 1), $closeBracket)) !== false) {

            $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextParam + 1), ($closeBracket + 1), true);
            if ($nextToken === false) {
                break;
            }

            $nextCode = $tokens[$nextToken]['code'];

            if ($nextCode === T_EQUAL) {
                // Check parameter default spacing.
                $spacesBefore = 0;
                if (($nextToken - $nextParam) > 1) {
                    $spacesBefore = strlen($tokens[($nextParam + 1)]['content']);
                }

                if ($spacesBefore !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between argument "%s" and equals sign; expected '.$this->equalsSpacing.' but found %s';
                    $data  = array(
                              $tokens[$nextParam]['content'],
                              $spacesBefore,
                             );
                    $phpcsFile->addError($error, $nextToken, 'SpaceBeforeEquals', $data);
                }

                $spacesAfter = 0;
                if ($tokens[($nextToken + 1)]['code'] === T_WHITESPACE) {
                    $spacesAfter = strlen($tokens[($nextToken + 1)]['content']);
                }

                if ($spacesAfter !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between default value and equals sign for argument "%s"; expected '.$this->equalsSpacing.' but found %s';
                    $data  = array(
                              $tokens[$nextParam]['content'],
                              $spacesAfter,
                             );
                    $phpcsFile->addError($error, $nextToken, 'SpaceAfterDefault', $data);
                }
            }//end if

            // Find and check the comma (if there is one).
            $nextComma = $phpcsFile->findNext(T_COMMA, ($nextParam + 1), $closeBracket);
            if ($nextComma !== false) {
                // Comma found.
                if ($tokens[($nextComma - 1)]['code'] === T_WHITESPACE) {
                    $error = 'Expected 0 spaces between argument "%s" and comma; %s found';
                    $data  = array(
                              $tokens[$nextParam]['content'],
                              strlen($tokens[($nextComma - 1)]['content']),
                             );
                    $phpcsFile->addError($error, $nextToken, 'SpaceBeforeComma', $data);
                }
            }

            // Take references into account when expecting the
            // location of whitespace.
            if ($phpcsFile->isReference(($nextParam - 1)) === true) {
                $whitespace = ($nextParam - 2);
            } else {
                $whitespace = ($nextParam - 1);
            }

            if (empty($params) === false) {
                // This is not the first argument in the function declaration.
                $arg = $tokens[$nextParam]['content'];

                if ($tokens[$whitespace]['code'] === T_WHITESPACE) {
                    $gap = strlen($tokens[$whitespace]['content']);

                    // Before we throw an error, make sure there is no type hint.
                    $comma     = $phpcsFile->findPrevious(T_COMMA, ($nextParam - 1));
                    $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($comma + 1), null, true);
                    if ($phpcsFile->isReference($nextToken) === true) {
                        $nextToken++;
                    }

                    if ($nextToken !== $nextParam) {
                        // There was a type hint, so check the spacing between
                        // the hint and the variable as well.
                        $hint = $tokens[$nextToken]['content'];

                        if ($gap !== 1) {
                            $error = 'Expected 1 space between type hint and argument "%s"; %s found';
                            $data  = array(
                                      $arg,
                                      $gap,
                                     );
                            $phpcsFile->addError($error, $nextToken, 'SpacingAfterHint', $data);
                        }

                        if ($multiLine === false) {
                            if ($tokens[($comma + 1)]['code'] !== T_WHITESPACE) {
                                $error = 'Expected 1 space between comma and type hint "%s"; 0 found';
                                $data  = array($hint);
                                $phpcsFile->addError($error, $nextToken, 'NoSpaceBeforeHint', $data);
                            } else {
                                $gap = strlen($tokens[($comma + 1)]['content']);
                                if ($gap !== 1) {
                                    $error = 'Expected 1 space between comma and type hint "%s"; %s found';
                                    $data  = array(
                                              $hint,
                                              $gap,
                                             );
                                    $phpcsFile->addError($error, $nextToken, 'SpacingBeforeHint', $data);
                                }
                            }
                        }
                    } else if ($gap !== 1) {
                        // Just make sure this is not actually an indent.
                        if ($tokens[$whitespace]['line'] === $tokens[($whitespace - 1)]['line']) {
                            $error = 'Expected 1 space between comma and argument "%s"; %s found';
                            $data  = array(
                                      $arg,
                                      $gap,
                                     );
                            $phpcsFile->addError($error, $nextToken, 'SpacingBeforeArg', $data);
                        }
                    }//end if
                } else {
                    $error = 'Expected 1 space between comma and argument "%s"; 0 found';
                    $data  = array($arg);
                    $phpcsFile->addError($error, $nextToken, 'NoSpaceBeforeArg', $data);
                }//end if
            } else {
                // First argument in function declaration.
                if ($tokens[$whitespace]['code'] === T_WHITESPACE) {
                    $gap = strlen($tokens[$whitespace]['content']);
                    $arg = $tokens[$nextParam]['content'];

                    // Before we throw an error, make sure there is no type hint.
                    $bracket   = $phpcsFile->findPrevious(T_OPEN_PARENTHESIS, ($nextParam - 1));
                    $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($bracket + 1), null, true);
                    if ($phpcsFile->isReference($nextToken) === true) {
                        $nextToken++;
                    }

                    if ($nextToken !== $nextParam) {
                        // There was a type hint, so check the spacing between
                        // the hint and the variable as well.
                        $hint = $tokens[$nextToken]['content'];

                        if ($gap !== 1) {
                            $error = 'Expected 1 space between type hint and argument "%s"; %s found';
                            $data  = array(
                                      $arg,
                                      $gap,
                                     );
                            $phpcsFile->addError($error, $nextToken, 'SpacingAfterHint', $data);
                        }

                        if ($multiLine === false
                            && $tokens[($bracket + 1)]['code'] === T_WHITESPACE
                        ) {
                            $error = 'Expected 0 spaces between opening bracket and type hint "%s"; %s found';
                            $data  = array(
                                      $hint,
                                      strlen($tokens[($bracket + 1)]['content']),
                                     );
                            $phpcsFile->addError($error, $nextToken, 'SpacingAfterOpenHint', $data);
                        }
                    } else if ($multiLine === false) {
                        $error = 'Expected 0 spaces between opening bracket and argument "%s"; %s found';
                        $data  = array(
                                      $arg,
                                      $gap,
                                     );
                        $phpcsFile->addError($error, $nextToken, 'SpacingAfterOpen', $data);
                    }
                }//end if
            }//end if

            $params[] = $nextParam;

        }//end while

        if (empty($params) === true) {
            // There are no parameters for this function.
            if (($closeBracket - $openBracket) !== 1) {
                $error = 'Expected 0 spaces between brackets of function declaration; %s found';
                $data  = array(strlen($tokens[($closeBracket - 1)]['content']));
                $phpcsFile->addError($error, $openBracket, 'SpacingBetween', $data);
            }
        } else if ($multiLine === false
            && $tokens[($closeBracket - 1)]['code'] === T_WHITESPACE
        ) {
            $lastParam = array_pop($params);
            $error     = 'Expected 0 spaces between argument "%s" and closing bracket; %s found';
            $data      = array(
                          $tokens[$lastParam]['content'],
                          strlen($tokens[($closeBracket - 1)]['content']),
                         );
            $phpcsFile->addError($error, $closeBracket, 'SpacingBeforeClose', $data);
        }

    }//end processBracket()


}//end class

?>
