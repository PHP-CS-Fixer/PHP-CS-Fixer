<?php
/**
 * Generic_Sniffs_Debug_CSSLintSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Roman Levishchenko <index.0h@gmail.com>
 * @copyright 2013 Roman Levishchenko
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Debug_CSSLintSniff.
 *
 * Runs csslint on the file.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Roman Levishchenko <index.0h@gmail.com>
 * @copyright 2013 Roman Levishchenko
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Debug_CSSLintSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('CSS');


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

        $csslintPath = PHP_CodeSniffer::getConfigData('csslint_path');
        if ($csslintPath === null) {
            return;
        }

        $cmd = $csslintPath.' '.escapeshellarg($fileName);
        exec($cmd, $output, $retval);

        if (is_array($output) === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $count  = count($output);

        for ($i = 0; $i < $count; $i++) {
            $matches    = array();
            $numMatches = preg_match(
                '/(error|warning) at line (\d+)/',
                $output[$i],
                $matches
            );

            if ($numMatches === 0) {
                continue;
            }

            $line    = (int) $matches[2];
            $message = 'csslint says: '.$output[($i + 1)];
            // 1-st line is message with error line and error code.
            // 2-nd error message.
            // 3-d wrong line in file.
            // 4-th empty line.
            $i += 4;

            $lineToken = null;
            foreach ($tokens as $ptr => $info) {
                if ($info['line'] === $line) {
                    $lineToken = $ptr;
                    break;
                }
            }

            if ($lineToken !== null) {
                $phpcsFile->addWarning($message, $lineToken, 'ExternalTool');
            }
        }//end for

    }//end process()


}//end class

?>
