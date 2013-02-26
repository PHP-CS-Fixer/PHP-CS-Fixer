<?php
/**
 * Squiz_Sniffs_Debug_JavaScriptLintSniff.
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
 * Squiz_Sniffs_Debug_JavaScriptLintSniff.
 *
 * Runs JavaScript Lint on the file.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Debug_JavaScriptLintSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('JS');


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $fileName = $phpcsFile->getFilename();

        $jslPath = PHP_CodeSniffer::getConfigData('jsl_path');
        if (is_null($jslPath) === true) {
            return;
        }

        $cmd = '"'.$jslPath.'" -nologo -nofilelisting -nocontext -nosummary -output-format __LINE__:__ERROR__ -process "'.$fileName.'"';
        $msg = exec($cmd, $output, $retval);

        // $exitCode is the last line of $output if no error occurs, on error it
        // is numeric. Try to handle various error conditions and provide useful
        // error reporting.
        if ($retval === 2 || $retval === 4) {
            if (is_array($output) === true) {
                $msg = join('\n', $output);
            }

            throw new PHP_CodeSniffer_Exception("Failed invoking JavaScript Lint, retval was [$retval], output was [$msg]");
        }


        if (is_array($output) === true) {
            $tokens = $phpcsFile->getTokens();

            foreach ($output as $finding) {
                $split   = strpos($finding, ':');
                $line    = substr($finding, 0, $split);
                $message = substr($finding, ($split + 1));

                // Find the token at the start of the line.
                $lineToken = null;
                foreach ($tokens as $ptr => $info) {
                    if ($info['line'] == $line) {
                        $lineToken = $ptr;
                        break;
                    }
                }

                if ($lineToken !== null) {
                    $phpcsFile->addWarning(trim($message), $ptr, 'ExternalTool');
                }
            }//end foreach
        }//end if

    }//end process()

}//end class
?>
