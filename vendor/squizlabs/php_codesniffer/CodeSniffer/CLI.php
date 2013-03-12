<?php
/**
 * A class to process command line phpcs scripts.
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

if (is_file(dirname(__FILE__).'/../CodeSniffer.php') === true) {
    include_once dirname(__FILE__).'/../CodeSniffer.php';
} else {
    include_once 'PHP/CodeSniffer.php';
}

/**
 * A class to process command line phpcs scripts.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_CLI
{

    /**
     * An array of all values specified on the command line.
     *
     * @var array
     */
    protected $values = array();

    /**
     * The minimum severity level errors must have to be displayed.
     *
     * @var bool
     */
    public $errorSeverity = 0;

    /**
     * The minimum severity level warnings must have to be displayed.
     *
     * @var bool
     */
    public $warningSeverity = 0;


    /**
     * Exits if the minimum requirements of PHP_CodSniffer are not met.
     *
     * @return array
     */
    public function checkRequirements()
    {
        // Check the PHP version.
        if (version_compare(PHP_VERSION, '5.1.2') === -1) {
            echo 'ERROR: PHP_CodeSniffer requires PHP version 5.1.2 or greater.'.PHP_EOL;
            exit(2);
        }

        if (extension_loaded('tokenizer') === false) {
            echo 'ERROR: PHP_CodeSniffer requires the tokenizer extension to be enabled.'.PHP_EOL;
            exit(2);
        }

    }//end checkRequirements()


    /**
     * Get a list of default values for all possible command line arguments.
     *
     * @return array
     */
    public function getDefaults()
    {
        // The default values for config settings.
        $defaults['files']           = array();
        $defaults['standard']        = null;
        $defaults['verbosity']       = 0;
        $defaults['interactive']     = false;
        $defaults['explain']         = false;
        $defaults['local']           = false;
        $defaults['showSources']     = false;
        $defaults['extensions']      = array();
        $defaults['sniffs']          = array();
        $defaults['ignored']         = array();
        $defaults['reportFile']      = null;
        $defaults['generator']       = '';
        $defaults['reports']         = array();
        $defaults['errorSeverity']   = null;
        $defaults['warningSeverity'] = null;

        $reportFormat = PHP_CodeSniffer::getConfigData('report_format');
        if ($reportFormat !== null) {
            $defaults['reports'][$reportFormat] = null;
        }

        $tabWidth = PHP_CodeSniffer::getConfigData('tab_width');
        if ($tabWidth === null) {
            $defaults['tabWidth'] = 0;
        } else {
            $defaults['tabWidth'] = (int) $tabWidth;
        }

        $encoding = PHP_CodeSniffer::getConfigData('encoding');
        if ($encoding === null) {
            $defaults['encoding'] = 'iso-8859-1';
        } else {
            $defaults['encoding'] = strtolower($encoding);
        }

        $severity = PHP_CodeSniffer::getConfigData('severity');
        if ($severity !== null) {
            $defaults['errorSeverity']   = (int) $severity;
            $defaults['warningSeverity'] = (int) $severity;
        }

        $severity = PHP_CodeSniffer::getConfigData('error_severity');
        if ($severity !== null) {
            $defaults['errorSeverity'] = (int) $severity;
        }

        $severity = PHP_CodeSniffer::getConfigData('warning_severity');
        if ($severity !== null) {
            $defaults['warningSeverity'] = (int) $severity;
        }

        $showWarnings = PHP_CodeSniffer::getConfigData('show_warnings');
        if ($showWarnings !== null) {
            $showWarnings = (bool) $showWarnings;
            if ($showWarnings === false) {
                $defaults['warningSeverity'] = 0;
            }
        }

        $reportWidth = PHP_CodeSniffer::getConfigData('report_width');
        if ($reportWidth === null) {
            $defaults['reportWidth'] = 80;
        } else {
            $defaults['reportWidth'] = (int) $reportWidth;
        }

        $showProgress = PHP_CodeSniffer::getConfigData('show_progress');
        if ($showProgress === null) {
            $defaults['showProgress'] = false;
        } else {
            $defaults['showProgress'] = (bool) $showProgress;
        }

        return $defaults;

    }//end getDefaults()


    /**
     * Process the command line arguments and returns the values.
     *
     * @return array
     */
    public function getCommandLineValues()
    {
        if (defined('PHP_CODESNIFFER_IN_TESTS') === true) {
            return array();
        }

        if (empty($this->values) === false) {
            return $this->values;
        }

        $values = $this->getDefaults();

        for ($i = 1; $i < $_SERVER['argc']; $i++) {
            $arg = $_SERVER['argv'][$i];
            if ($arg === '') {
                continue;
            }

            if ($arg{0} === '-') {
                if ($arg === '-' || $arg === '--') {
                    // Empty argument, ignore it.
                    continue;
                }

                if ($arg{1} === '-') {
                    $values
                        = $this->processLongArgument(substr($arg, 2), $i, $values);
                } else {
                    $switches = str_split($arg);
                    foreach ($switches as $switch) {
                        if ($switch === '-') {
                            continue;
                        }

                        $values = $this->processShortArgument($switch, $i, $values);
                    }
                }
            } else {
                $values = $this->processUnknownArgument($arg, $i, $values);
            }//end if
        }//end for

        $this->values = $values;
        return $values;

    }//end getCommandLineValues()


    /**
     * Processes a short (-e) command line argument.
     *
     * @param string $arg    The command line argument.
     * @param int    $pos    The position of the argument on the command line.
     * @param array  $values An array of values determined from CLI args.
     *
     * @return array The updated CLI values.
     * @see getCommandLineValues()
     */
    public function processShortArgument($arg, $pos, $values)
    {
        switch ($arg) {
        case 'h':
        case '?':
            $this->printUsage();
            exit(0);
            break;
        case 'i' :
            $this->printInstalledStandards();
            exit(0);
            break;
        case 'v' :
            $values['verbosity']++;
            break;
        case 'l' :
            $values['local'] = true;
            break;
        case 's' :
            $values['showSources'] = true;
            break;
        case 'a' :
            $values['interactive'] = true;
            break;
        case 'e':
            $values['explain'] = true;
            break;
        case 'p' :
            $values['showProgress'] = true;
            break;
        case 'd' :
            $ini = explode('=', $_SERVER['argv'][($pos + 1)]);
            $_SERVER['argv'][($pos + 1)] = '';
            if (isset($ini[1]) === true) {
                ini_set($ini[0], $ini[1]);
            } else {
                ini_set($ini[0], true);
            }

            break;
        case 'n' :
            $values['warningSeverity'] = 0;
            break;
        case 'w' :
            $values['warningSeverity'] = null;
            break;
        default:
            $values = $this->processUnknownArgument('-'.$arg, $pos, $values);
        }//end switch

        return $values;

    }//end processShortArgument()


    /**
     * Processes a long (--example) command line argument.
     *
     * @param string $arg    The command line argument.
     * @param int    $pos    The position of the argument on the command line.
     * @param array  $values An array of values determined from CLI args.
     *
     * @return array The updated CLI values.
     * @see getCommandLineValues()
     */
    public function processLongArgument($arg, $pos, $values)
    {
        switch ($arg) {
        case 'help':
            $this->printUsage();
            exit(0);
            break;
        case 'version':
            echo 'PHP_CodeSniffer version @package_version@ (@package_state@) ';
            echo 'by Squiz Pty Ltd. (http://www.squiz.com.au)'.PHP_EOL;
            exit(0);
            break;
        case 'config-set':
            $key   = $_SERVER['argv'][($pos + 1)];
            $value = $_SERVER['argv'][($pos + 2)];
            PHP_CodeSniffer::setConfigData($key, $value);
            exit(0);
            break;
        case 'config-delete':
            $key = $_SERVER['argv'][($pos + 1)];
            PHP_CodeSniffer::setConfigData($key, null);
            exit(0);
            break;
        case 'config-show':
            $data = PHP_CodeSniffer::getAllConfigData();
            print_r($data);
            exit(0);
            break;
        default:
            if (substr($arg, 0, 7) === 'sniffs=') {
                $values['sniffs'] = array();

                $sniffs = substr($arg, 7);
                $sniffs = explode(',', $sniffs);

                // Convert the sniffs to class names.
                foreach ($sniffs as $sniff) {
                    $parts = explode('.', $sniff);
                    $values['sniffs'][] = $parts[0].'_Sniffs_'.$parts[1].'_'.$parts[2].'Sniff';
                }
            } else if (substr($arg, 0, 12) === 'report-file=') {
                $values['reportFile'] = realpath(substr($arg, 12));

                // It may not exist and return false instead.
                if ($values['reportFile'] === false) {
                    $values['reportFile'] = substr($arg, 12);
                }

                if (is_dir($values['reportFile']) === true) {
                    echo 'ERROR: The specified report file path "'.$values['reportFile'].'" is a directory.'.PHP_EOL.PHP_EOL;
                    $this->printUsage();
                    exit(2);
                }

                $dir = dirname($values['reportFile']);
                if (is_dir($dir) === false) {
                    echo 'ERROR: The specified report file path "'.$values['reportFile'].'" points to a non-existent directory.'.PHP_EOL.PHP_EOL;
                    $this->printUsage();
                    exit(2);
                }
            } else if (substr($arg, 0, 13) === 'report-width=') {
                $values['reportWidth'] = (int) substr($arg, 13);
            } else if (substr($arg, 0, 7) === 'report='
                || substr($arg, 0, 7) === 'report-'
            ) {
                if ($arg[6] === '-') {
                    // This is a report with file output.
                    $split = strpos($arg, '=');
                    if ($split === false) {
                        $report = substr($arg, 7);
                        $output = null;
                    } else {
                        $report = substr($arg, 7, ($split - 7));
                        $output = substr($arg, ($split + 1));
                        if ($output === false) {
                            $output = null;
                        }
                    }
                } else {
                    // This is a single report.
                    $report = substr($arg, 7);
                    $output = null;
                }

                $validReports     = array(
                                     'full',
                                     'xml',
                                     'checkstyle',
                                     'csv',
                                     'emacs',
                                     'notifysend',
                                     'source',
                                     'summary',
                                     'svnblame',
                                     'gitblame',
                                     'hgblame',
                                    );

                if (in_array($report, $validReports) === false) {
                    echo 'ERROR: Report type "'.$report.'" not known.'.PHP_EOL;
                    exit(2);
                }

                $values['reports'][$report] = $output;
            } else if (substr($arg, 0, 9) === 'standard=') {
                $values['standard'] = substr($arg, 9);
            } else if (substr($arg, 0, 11) === 'extensions=') {
                $values['extensions'] = explode(',', substr($arg, 11));
            } else if (substr($arg, 0, 9) === 'severity=') {
                $values['errorSeverity']   = (int) substr($arg, 9);
                $values['warningSeverity'] = $values['errorSeverity'];
            } else if (substr($arg, 0, 15) === 'error-severity=') {
                $values['errorSeverity'] = (int) substr($arg, 15);
            } else if (substr($arg, 0, 17) === 'warning-severity=') {
                $values['warningSeverity'] = (int) substr($arg, 17);
            } else if (substr($arg, 0, 7) === 'ignore=') {
                // Split the ignore string on commas, unless the comma is escaped
                // using 1 or 3 slashes (\, or \\\,).
                $ignored = preg_split(
                    '/(?<=(?<!\\\\)\\\\\\\\),|(?<!\\\\),/',
                    substr($arg, 7)
                );
                foreach ($ignored as $pattern) {
                    $values['ignored'][$pattern] = 'absolute';
                }
            } else if (substr($arg, 0, 10) === 'generator=') {
                $values['generator'] = substr($arg, 10);
            } else if (substr($arg, 0, 9) === 'encoding=') {
                $values['encoding'] = strtolower(substr($arg, 9));
            } else if (substr($arg, 0, 10) === 'tab-width=') {
                $values['tabWidth'] = (int) substr($arg, 10);
            } else {
                $values = $this->processUnknownArgument('--'.$arg, $pos, $values);
            }//end if

            break;
        }//end switch

        return $values;

    }//end processLongArgument()


    /**
     * Processes an unknown command line argument.
     *
     * Assumes all unknown arguments are files and folders to check.
     *
     * @param string $arg    The command line argument.
     * @param int    $pos    The position of the argument on the command line.
     * @param array  $values An array of values determined from CLI args.
     *
     * @return array The updated CLI values.
     * @see getCommandLineValues()
     */
    public function processUnknownArgument($arg, $pos, $values)
    {
        // We don't know about any additional switches; just files.
        if ($arg{0} === '-') {
            echo 'ERROR: option "'.$arg.'" not known.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        }

        $file = realpath($arg);
        if (file_exists($file) === false) {
            echo 'ERROR: The file "'.$arg.'" does not exist.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        } else {
            $values['files'][] = $file;
        }

        return $values;

    }//end processUnknownArgument()


    /**
     * Runs PHP_CodeSniffer over files and directories.
     *
     * @param array $values An array of values determined from CLI args.
     *
     * @return int The number of error and warning messages shown.
     * @see getCommandLineValues()
     */
    public function process($values=array())
    {
        if (empty($values) === true) {
            $values = $this->getCommandLineValues();
        }

        if ($values['generator'] !== '') {
            $phpcs = new PHP_CodeSniffer($values['verbosity']);
            $phpcs->generateDocs(
                $values['standard'],
                $values['files'],
                $values['generator']
            );
            exit(0);
        }

        $values['standard'] = $this->validateStandard($values['standard']);
        if (PHP_CodeSniffer::isInstalledStandard($values['standard']) === false) {
            // They didn't select a valid coding standard, so help them
            // out by letting them know which standards are installed.
            echo 'ERROR: the "'.$values['standard'].'" coding standard is not installed. ';
            $this->printInstalledStandards();
            exit(2);
        }

        if ($values['explain'] === true) {
            $this->explainStandard($values['standard']);
            exit(0);
        }

        $fileContents = '';
        if (empty($values['files']) === true) {
            // Check if they passing in the file contents.
            $handle       = fopen('php://stdin', 'r');
            $fileContents = stream_get_contents($handle);
            fclose($handle);

            if ($fileContents === '') {
                // No files and no content passed in.
                echo 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
                $this->printUsage();
                exit(2);
            }
        }

        $phpcs = new PHP_CodeSniffer(
            $values['verbosity'],
            $values['tabWidth'],
            $values['encoding'],
            $values['interactive']
        );

        // Set file extensions if they were specified. Otherwise,
        // let PHP_CodeSniffer decide on the defaults.
        if (empty($values['extensions']) === false) {
            $phpcs->setAllowedFileExtensions($values['extensions']);
        }

        // Set ignore patterns if they were specified.
        if (empty($values['ignored']) === false) {
            $phpcs->setIgnorePatterns($values['ignored']);
        }

        // Set some convenience member vars.
        if ($values['errorSeverity'] === null) {
            $this->errorSeverity = PHPCS_DEFAULT_ERROR_SEV;
        } else {
            $this->errorSeverity = $values['errorSeverity'];
        }

        if ($values['warningSeverity'] === null) {
            $this->warningSeverity = PHPCS_DEFAULT_WARN_SEV;
        } else {
            $this->warningSeverity = $values['warningSeverity'];
        }

        $phpcs->setCli($this);

        $phpcs->process(
            $values['files'],
            $values['standard'],
            $values['sniffs'],
            $values['local']
        );

        if ($fileContents !== '') {
            $phpcs->processFile('STDIN', $fileContents);
        }

        return $this->printErrorReport(
            $phpcs,
            $values['reports'],
            $values['showSources'],
            $values['reportFile'],
            $values['reportWidth']
        );

    }//end process()


    /**
     * Prints the error report for the run.
     *
     * Note that this function may actually print multiple reports
     * as the user may have specified a number of output formats.
     *
     * @param PHP_CodeSniffer $phpcs       The PHP_CodeSniffer object containing
     *                                     the errors.
     * @param array           $reports     A list of reports to print.
     * @param bool            $showSources TRUE if report should show error sources
     *                                     (not used by all reports).
     * @param string          $reportFile  A default file to log report output to.
     * @param int             $reportWidth How wide the screen reports should be.
     *
     * @return int The number of error and warning messages shown.
     */
    public function printErrorReport(
        PHP_CodeSniffer $phpcs,
        $reports,
        $showSources,
        $reportFile,
        $reportWidth
    ) {
        $reporting       = new PHP_CodeSniffer_Reporting();
        $filesViolations = $phpcs->getFilesErrors();

        if (empty($reports) === true) {
            $reports['full'] = $reportFile;
        }

        $errors   = 0;
        $toScreen = false;

        foreach ($reports as $report => $output) {
            if ($output === null) {
                $output = $reportFile;
            }

            if ($reportFile === null) {
                $toScreen = true;
            }

            // We don't add errors here because the number of
            // errors reported by each report type will always be the
            // same, so we really just need 1 number.
            $errors = $reporting->printReport(
                $report,
                $filesViolations,
                $showSources,
                $output,
                $reportWidth
            );
        }

        // Only print PHP_Timer output if no reports were
        // printed to the screen so we don't put additional output
        // in something like an XML report. If we are printing to screen,
        // the report types would have already worked out who should
        // print the timer info.
        if ($toScreen === false
            && PHP_CODESNIFFER_INTERACTIVE === false
            && class_exists('PHP_Timer', false) === true
        ) {
            echo PHP_Timer::resourceUsage().PHP_EOL.PHP_EOL;
        }

        // They should all return the same value, so it
        // doesn't matter which return value we end up using.
        return $errors;

    }//end printErrorReport()


    /**
     * Convert the passed standard into a valid standard.
     *
     * Checks things like default values and case.
     *
     * @param string $standard The standard to validate.
     *
     * @return string
     */
    public function validateStandard($standard)
    {
        if ($standard === null) {
            // They did not supply a standard to use.
            // Try to get the default from the config system.
            $standard = PHP_CodeSniffer::getConfigData('default_standard');
            if ($standard === null) {
                $standard = 'PEAR';
            }
        }

        // Check if the standard name is valid. If not, check that the case
        // was not entered incorrectly.
        if (PHP_CodeSniffer::isInstalledStandard($standard) === false) {
            $installedStandards = PHP_CodeSniffer::getInstalledStandards();
            foreach ($installedStandards as $validStandard) {
                if (strtolower($standard) === strtolower($validStandard)) {
                    $standard = $validStandard;
                    break;
                }
            }
        }

        return $standard;

    }//end validateStandard()


    /**
     * Prints a report showing the sniffs contained in a standard.
     *
     * @param string $standard The standard to validate.
     *
     * @return void
     */
    public function explainStandard($standard)
    {
        $phpcs = new PHP_CodeSniffer();
        $phpcs->setTokenListeners($standard);
        $sniffs = $phpcs->getSniffs();
        $sniffs = array_keys($sniffs);
        sort($sniffs);

        ob_start();

        $lastStandard = '';
        $lastCount    = '';
        $sniffCount   = count($sniffs);
        $sniffs[]     = '___';

        echo PHP_EOL."The $standard standard contains $sniffCount sniffs".PHP_EOL;

        ob_start();

        foreach ($sniffs as $sniff) {
            $parts = explode('_', $sniff);
            if ($lastStandard === '') {
                $lastStandard = $parts[0];
            }

            if ($parts[0] !== $lastStandard) {
                $sniffList = ob_get_contents();
                ob_end_clean();

                echo PHP_EOL.$lastStandard.' ('.$lastCount.' sniffs)'.PHP_EOL;
                echo str_repeat('-', strlen($lastStandard.$lastCount) + 10);
                echo PHP_EOL;
                echo $sniffList;

                $lastStandard = $parts[0];
                $lastCount    = 0;

                ob_start();
            }

            echo '  '.$parts[0].'.'.$parts[2].'.'.substr($parts[3], 0, -5).PHP_EOL;
            $lastCount++;
        }//end foreach

        ob_end_clean();

    }//end explainStandard()


    /**
     * Prints out the usage information for this script.
     *
     * @return void
     */
    public function printUsage()
    {
        echo 'Usage: phpcs [-nwlsaepvi] [-d key[=value]]'.PHP_EOL;
        echo '    [--report=<report>] [--report-file=<reportfile>] [--report-<report>=<reportfile>] ...'.PHP_EOL;
        echo '    [--report-width=<reportWidth>] [--generator=<generator>] [--tab-width=<tabWidth>]'.PHP_EOL;
        echo '    [--severity=<severity>] [--error-severity=<severity>] [--warning-severity=<severity>]'.PHP_EOL;
        echo '    [--config-set key value] [--config-delete key] [--config-show]'.PHP_EOL;
        echo '    [--standard=<standard>] [--sniffs=<sniffs>] [--encoding=<encoding>]'.PHP_EOL;
        echo '    [--extensions=<extensions>] [--ignore=<patterns>] <file> ...'.PHP_EOL;
        echo '        -n            Do not print warnings (shortcut for --warning-severity=0)'.PHP_EOL;
        echo '        -w            Print both warnings and errors (on by default)'.PHP_EOL;
        echo '        -l            Local directory only, no recursion'.PHP_EOL;
        echo '        -s            Show sniff codes in all reports'.PHP_EOL;
        echo '        -a            Run interactively'.PHP_EOL;
        echo '        -e            Explain a standard by showing the sniffs it includes'.PHP_EOL;
        echo '        -p            Show progress of the run'.PHP_EOL;
        echo '        -v[v][v]      Print verbose output'.PHP_EOL;
        echo '        -i            Show a list of installed coding standards'.PHP_EOL;
        echo '        -d            Set the [key] php.ini value to [value] or [true] if value is omitted'.PHP_EOL;
        echo '        --help        Print this help message'.PHP_EOL;
        echo '        --version     Print version information'.PHP_EOL;
        echo '        <file>        One or more files and/or directories to check'.PHP_EOL;
        echo '        <extensions>  A comma separated list of file extensions to check'.PHP_EOL;
        echo '                      (only valid if checking a directory)'.PHP_EOL;
        echo '        <patterns>    A comma separated list of patterns to ignore files and directories'.PHP_EOL;
        echo '        <encoding>    The encoding of the files being checked (default is iso-8859-1)'.PHP_EOL;
        echo '        <sniffs>      A comma separated list of sniff codes to limit the check to'.PHP_EOL;
        echo '                      (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo '        <severity>    The minimum severity required to display an error or warning'.PHP_EOL;
        echo '        <standard>    The name or path of the coding standard to use'.PHP_EOL;
        echo '        <tabWidth>    The number of spaces each tab represents'.PHP_EOL;
        echo '        <generator>   The name of a doc generator to use'.PHP_EOL;
        echo '                      (forces doc generation instead of checking)'.PHP_EOL;
        echo '        <report>      Print either the "full", "xml", "checkstyle", "csv", "emacs"'.PHP_EOL;
        echo '                      "source", "summary", "svnblame", "gitblame", "hgblame" or'.PHP_EOL;
        echo '                      "notifysend" report'.PHP_EOL;
        echo '                      (the "full" report is printed by default)'.PHP_EOL;
        echo '        <reportfile>  Write the report to the specified file path'.PHP_EOL;
        echo '        <reportWidth> How many columns wide screen reports should be printed'.PHP_EOL;

    }//end printUsage()


    /**
     * Prints out a list of installed coding standards.
     *
     * @return void
     */
    public function printInstalledStandards()
    {
        $installedStandards = PHP_CodeSniffer::getInstalledStandards();
        $numStandards       = count($installedStandards);

        if ($numStandards === 0) {
            echo 'No coding standards are installed.'.PHP_EOL;
        } else {
            $lastStandard = array_pop($installedStandards);
            if ($numStandards === 1) {
                echo "The only coding standard installed is $lastStandard".PHP_EOL;
            } else {
                $standardList  = implode(', ', $installedStandards);
                $standardList .= ' and '.$lastStandard;
                echo 'The installed coding standards are '.$standardList.PHP_EOL;
            }
        }

    }//end printInstalledStandards()


}//end class

?>
