<?php
/**
 * A class to manage reporting.
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

if (is_file(dirname(__FILE__).'/../CodeSniffer.php') === true) {
    include_once dirname(__FILE__).'/../CodeSniffer.php';
} else {
    include_once 'PHP/CodeSniffer.php';
}

/**
 * A class to manage reporting.
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
class PHP_CodeSniffer_Reporting
{


    /**
     * Produce the appropriate report object based on $type parameter.
     *
     * @param string $type Demanded report type.
     *
     * @return PHP_CodeSniffer_Report
     * @throws PHP_CodeSniffer_Exception If report is not available.
     */
    public function factory($type)
    {
        $type            = ucfirst($type);
        $filename        = $type.'.php';
        $reportClassName = 'PHP_CodeSniffer_Reports_'.$type;
        if (class_exists($reportClassName, true) === false) {
            throw new PHP_CodeSniffer_Exception('Report type "'.$type.'" not found.');
        }

        $reportClass = new $reportClassName();
        if (false === ($reportClass instanceof PHP_CodeSniffer_Report)) {
            throw new PHP_CodeSniffer_Exception('Class "'.$reportClassName.'" must implement the "PHP_CodeSniffer_Report" interface.');
        }

        return $reportClass;

    }//end factory()


    /**
     * Actually generates the report.
     * 
     * @param string  $report          Report type.
     * @param array   $filesViolations Collected violations.
     * @param boolean $showSources     Show sources?
     * @param string  $reportFile      Report file to generate.
     * @param integer $reportWidth     Report max width.
     * 
     * @return integer
     */
    public function printReport(
        $report,
        $filesViolations,
        $showSources,
        $reportFile='',
        $reportWidth=80
    ) {
        if ($reportFile !== null) {
            $reportDir = dirname($reportFile);
            if ($reportDir === '.') {
                // Passed report file is a filename in the current directory.
                $reportFile = PHPCS_CWD.'/'.basename($reportFile);
            } else {
                $reportDir = realpath(PHPCS_CWD.'/'.$reportDir);
                if ($reportDir !== false) {
                    // Report file path is relative.
                    $reportFile = $reportDir.'/'.basename($reportFile);
                }
            }
        }

        $reportClass = self::factory($report);
        $reportData  = $this->prepare($filesViolations);

        $toScreen = true;
        if ($reportFile !== null) {
            $toScreen = false;
            ob_start();
        }

        $numErrors = $reportClass->generate($reportData, $showSources, $reportWidth, $toScreen);

        if ($reportFile !== null) {
            $generatedReport = ob_get_contents();
            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }

            $generatedReport = trim($generatedReport);
            file_put_contents($reportFile, $generatedReport.PHP_EOL);
        }

        return $numErrors;

    }//end printReport()


    /**
     * Pre-process and package violations for all files.
     *
     * Used by error reports to get a packaged list of all errors in each file.
     *
     * @param array $filesViolations List of found violations.
     *
     * @return array
     */
    public function prepare(array $filesViolations)
    {
        $report = array(
                   'totals' => array(
                                'warnings' => 0,
                                'errors'   => 0,
                               ),
                   'files'  => array(),
                  );

        foreach ($filesViolations as $filename => $fileViolations) {
            $warnings    = $fileViolations['warnings'];
            $errors      = $fileViolations['errors'];
            $numWarnings = $fileViolations['numWarnings'];
            $numErrors   = $fileViolations['numErrors'];

            $report['files'][$filename] = array(
                                           'errors'   => 0,
                                           'warnings' => 0,
                                           'messages' => array(),
                                          );

            if ($numErrors === 0 && $numWarnings === 0) {
                // Prefect score!
                continue;
            }

            $report['files'][$filename]['errors']   = $numErrors;
            $report['files'][$filename]['warnings'] = $numWarnings;

            $report['totals']['errors']   += $numErrors;
            $report['totals']['warnings'] += $numWarnings;

            // Merge errors and warnings.
            foreach ($errors as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    $newErrors = array();
                    foreach ($colErrors as $data) {
                        $newErrors[] = array(
                                        'message'  => $data['message'],
                                        'source'   => $data['source'],
                                        'severity' => $data['severity'],
                                        'type'     => 'ERROR',
                                       );
                    }//end foreach

                    $errors[$line][$column] = $newErrors;
                }//end foreach

                ksort($errors[$line]);
            }//end foreach

            foreach ($warnings as $line => $lineWarnings) {
                foreach ($lineWarnings as $column => $colWarnings) {
                    $newWarnings = array();
                    foreach ($colWarnings as $data) {
                        $newWarnings[] = array(
                                          'message'  => $data['message'],
                                          'source'   => $data['source'],
                                          'severity' => $data['severity'],
                                          'type'     => 'WARNING',
                                         );
                    }//end foreach

                    if (isset($errors[$line]) === false) {
                        $errors[$line] = array();
                    }

                    if (isset($errors[$line][$column]) === true) {
                        $errors[$line][$column] = array_merge(
                            $newWarnings,
                            $errors[$line][$column]
                        );
                    } else {
                        $errors[$line][$column] = $newWarnings;
                    }
                }//end foreach

                ksort($errors[$line]);
            }//end foreach

            ksort($errors);

            $report['files'][$filename]['messages'] = $errors;
        }//end foreach

        ksort($report['files']);

        return $report;

    }//end prepare()


}//end class

?>
