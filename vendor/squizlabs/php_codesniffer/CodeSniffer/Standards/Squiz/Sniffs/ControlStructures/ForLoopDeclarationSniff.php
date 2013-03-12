<?php
/**
 * Squiz_Sniffs_ControlStructures_ForLoopDeclarationSniff.
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
 * Squiz_Sniffs_ControlStructures_ForLoopDeclarationSniff.
 *
 * Verifies that there is a space between each condition of for loops.
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
class Squiz_Sniffs_ControlStructures_ForLoopDeclarationSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FOR);

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

        $openingBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openingBracket === false) {
            $error = 'Possible parse error: no opening parenthesis for FOR keyword';
            $phpcsFile->addWarning($error, $stackPtr, 'NoOpenBracket');
            return;
        }

        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
            $error = 'Space found after opening bracket of FOR loop';
            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterOpen');
        }

        if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
            $error = 'Space found before closing bracket of FOR loop';
            $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeClose');
        }

        $firstSemicolon  = $phpcsFile->findNext(T_SEMICOLON, $openingBracket, $closingBracket);

        // Check whitespace around each of the tokens.
        if ($firstSemicolon !== false) {
            if ($tokens[($firstSemicolon - 1)]['code'] === T_WHITESPACE) {
                $error = 'Space found before first semicolon of FOR loop';
                $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeFirst');
            }

            if ($tokens[($firstSemicolon + 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space after first semicolon of FOR loop; 0 found';
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterFirst');
            } else {
                if (strlen($tokens[($firstSemicolon + 1)]['content']) !== 1) {
                    $spaces = strlen($tokens[($firstSemicolon + 1)]['content']);
                    $error  = 'Expected 1 space after first semicolon of FOR loop; %s found';
                    $data   = array($spaces);
                    $phpcsFile->addError($error, $stackPtr, 'SpacingAfterFirst', $data);
                }
            }

            $secondSemicolon = $phpcsFile->findNext(T_SEMICOLON, ($firstSemicolon + 1));

            if ($secondSemicolon !== false) {
                if ($tokens[($secondSemicolon - 1)]['code'] === T_WHITESPACE) {
                    $error = 'Space found before second semicolon of FOR loop';
                    $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeSecond');
                }

                if (($secondSemicolon + 1) !== $closingBracket
                    && $tokens[($secondSemicolon + 1)]['code'] !== T_WHITESPACE
                ) {
                    $error = 'Expected 1 space after second semicolon of FOR loop; 0 found';
                    $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterSecond');
                } else {
                    if (strlen($tokens[($secondSemicolon + 1)]['content']) !== 1) {
                        $spaces = strlen($tokens[($secondSemicolon + 1)]['content']);
                        $data   = array($spaces);
                        if (($secondSemicolon + 2) === $closingBracket) {
                            $error = 'Expected no space after second semicolon of FOR loop; %s found';
                            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterSecondNoThird', $data);
                        } else {
                            $error = 'Expected 1 space after second semicolon of FOR loop; %s found';
                            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterSecond', $data);
                        }
                    }
                }
            }//end if
        }//end if

    }//end process()


}//end class

?>
