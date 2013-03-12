<?php
/**
 * Full report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Full report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Full implements PHP_CodeSniffer_Report
{


    /**
     * Prints all errors and warnings for each file processed.
     *
     * Errors and warnings are displayed together, grouped by file.
     *
     * @param array   $report      Prepared report.
     * @param boolean $showSources Show sources?
     * @param int     $width       Maximum allowed lne width.
     * @param boolean $toScreen    Is the report being printed to screen?
     *
     * @return string
     */
    public function generate(
        $report,
        $showSources=false,
        $width=80,
        $toScreen=true
    ) {
        $errorsShown = 0;
        $width       = max($width, 70);

        foreach ($report['files'] as $filename => $file) {
            if (empty($file['messages']) === true) {
                continue;
            }

            echo PHP_EOL.'FILE: ';
            if (strlen($filename) <= ($width - 9)) {
                echo $filename;
            } else {
                echo '...'.substr($filename, (strlen($filename) - ($width - 9)));
            }

            echo PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;

            echo 'FOUND '.$file['errors'].' ERROR(S) ';
            if ($file['warnings'] > 0) {
                echo 'AND '.$file['warnings'].' WARNING(S) ';
            }

            echo 'AFFECTING '.count($file['messages']).' LINE(S)'.PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;

            // Work out the max line number for formatting.
            $maxLine = 0;
            foreach ($file['messages'] as $line => $lineErrors) {
                if ($line > $maxLine) {
                    $maxLine = $line;
                }
            }

            $maxLineLength = strlen($maxLine);

            // The length of the word ERROR or WARNING; used for padding.
            if ($file['warnings'] > 0) {
                $typeLength = 7;
            } else {
                $typeLength = 5;
            }

            // The padding that all lines will require that are
            // printing an error message overflow.
            $paddingLine2  = str_repeat(' ', ($maxLineLength + 1));
            $paddingLine2 .= ' | ';
            $paddingLine2 .= str_repeat(' ', $typeLength);
            $paddingLine2 .= ' | ';

            // The maximum amount of space an error message can use.
            $maxErrorSpace = ($width - strlen($paddingLine2) - 1);

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $message = $error['message'];
                        if ($showSources === true) {
                            $message .= ' ('.$error['source'].')';
                        }

                        // The padding that goes on the front of the line.
                        $padding  = ($maxLineLength - strlen($line));
                        $errorMsg = wordwrap(
                            $message,
                            $maxErrorSpace,
                            PHP_EOL.$paddingLine2
                        );

                        echo ' '.str_repeat(' ', $padding).$line.' | '.$error['type'];
                        if ($error['type'] === 'ERROR') {
                            if ($file['warnings'] > 0) {
                                echo '  ';
                            }
                        }

                        echo ' | '.$errorMsg.PHP_EOL;
                        $errorsShown++;
                    }//end foreach
                }//end foreach
            }//end foreach

            echo str_repeat('-', $width).PHP_EOL.PHP_EOL;
        }//end foreach

        if ($toScreen === true
            && PHP_CODESNIFFER_INTERACTIVE === false
            && class_exists('PHP_Timer', false) === true
        ) {
            echo PHP_Timer::resourceUsage().PHP_EOL.PHP_EOL;
        }

        return $errorsShown;

    }//end generate()


}//end class

?>
