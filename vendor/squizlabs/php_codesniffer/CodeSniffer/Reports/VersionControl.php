<?php
/**
 * Version control report base class for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Version control report base class for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.2.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class PHP_CodeSniffer_Reports_VersionControl implements PHP_CodeSniffer_Report
{

    /**
     * The name of the report we want in the output.
     *
     * @var string
     */
    protected $reportName = 'VERSION CONTROL';


    /**
     * Prints the author of all errors and warnings, as given by "version control blame".
     *
     * @param array   $report      Prepared report.
     * @param boolean $showSources Show sources?
     * @param integer $width       Maximum allowed lne width.
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
        $authors = array();
        $praise  = array();
        $sources = array();
        $width   = max($width, 70);

        $errorsShown = 0;

        foreach ($report['files'] as $filename => $file) {
            $blames = $this->getBlameContent($filename);

            foreach ($file['messages'] as $line => $lineErrors) {
                $author = $this->getAuthor($blames[($line - 1)]);
                if ($author === false) {
                    continue;
                }

                if (isset($authors[$author]) === false) {
                    $authors[$author] = 0;
                    $praise[$author]  = array(
                                         'good' => 0,
                                         'bad'  => 0,
                                        );
                }

                $praise[$author]['bad']++;

                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $errorsShown++;
                        $authors[$author]++;

                        if ($showSources === true) {
                            $source = $error['source'];
                            if (isset($sources[$author][$source]) === false) {
                                $sources[$author][$source] = 1;
                            } else {
                                $sources[$author][$source]++;
                            }
                        }
                    }
                }

                unset($blames[($line - 1)]);
            }//end foreach

            // No go through and give the authors some credit for
            // all the lines that do not have errors.
            foreach ($blames as $line) {
                $author = $this->getAuthor($line);
                if (false === $author) {
                    continue;
                }

                if (isset($authors[$author]) === false) {
                    // This author doesn't have any errors.
                    if (PHP_CODESNIFFER_VERBOSITY === 0) {
                        continue;
                    }

                    $authors[$author] = 0;
                    $praise[$author]  = array(
                                         'good' => 0,
                                         'bad'  => 0,
                                        );
                }

                $praise[$author]['good']++;
            }//end foreach
        }//end foreach

        if ($errorsShown === 0) {
            // Nothing to show.
            return 0;
        }

        arsort($authors);

        echo PHP_EOL.'PHP CODE SNIFFER '.$this->reportName.' BLAME SUMMARY'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;
        if ($showSources === true) {
            echo 'AUTHOR   SOURCE'.str_repeat(' ', ($width - 43)).'(Author %) (Overall %) COUNT'.PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        } else {
            echo 'AUTHOR'.str_repeat(' ', ($width - 34)).'(Author %) (Overall %) COUNT'.PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        }

        foreach ($authors as $author => $count) {
            if ($praise[$author]['good'] === 0) {
                $percent = 0;
            } else {
                $total   = ($praise[$author]['bad'] + $praise[$author]['good']);
                $percent = round(($praise[$author]['bad'] / $total * 100), 2);
            }

            $overallPercent = '('.round((($count / $errorsShown) * 100), 2).')';
            $authorPercent  = '('.$percent.')';
            $line = str_repeat(' ', (6 - strlen($count))).$count;
            $line = str_repeat(' ', (12 - strlen($overallPercent))).$overallPercent.$line;
            $line = str_repeat(' ', (11 - strlen($authorPercent))).$authorPercent.$line;
            $line = $author.str_repeat(' ', ($width - strlen($author) - strlen($line))).$line;

            echo $line.PHP_EOL;

            if ($showSources === true && isset($sources[$author]) === true) {
                $errors = $sources[$author];
                asort($errors);
                $errors = array_reverse($errors);

                foreach ($errors as $source => $count) {
                    if ($source === 'count') {
                        continue;
                    }

                    $line = str_repeat(' ', (5 - strlen($count))).$count;
                    echo '         '.$source.str_repeat(' ', ($width - 14 - strlen($source))).$line.PHP_EOL;
                }
            }
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        echo 'A TOTAL OF '.$errorsShown.' SNIFF VIOLATION(S) ';
        echo 'WERE COMMITTED BY '.count($authors).' AUTHOR(S)'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if ($toScreen === true
            && PHP_CODESNIFFER_INTERACTIVE === false
            && class_exists('PHP_Timer', false) === true
        ) {
            echo PHP_Timer::resourceUsage().PHP_EOL.PHP_EOL;
        }

        return $errorsShown;

    }//end generate()


    /**
     * Extract the author from a blame line.
     *
     * @param string $line Line to parse.
     *
     * @return mixed string or false if impossible to recover.
     */
    abstract protected function getAuthor($line);


    /**
     * Gets the blame output.
     *
     * @param string $filename File to blame.
     *
     * @return array
     */
    abstract protected function getBlameContent($filename);


}//end class

?>
