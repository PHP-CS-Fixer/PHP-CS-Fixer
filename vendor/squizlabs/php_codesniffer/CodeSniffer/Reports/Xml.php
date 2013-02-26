<?php
/**
 * Xml report for PHP_CodeSniffer.
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
 * Xml report for PHP_CodeSniffer.
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
class PHP_CodeSniffer_Reports_Xml implements PHP_CodeSniffer_Report
{


    /**
     * Prints all violations for processed files, in a proprietary XML format.
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
        $out = new XMLWriter;
        $out->openMemory();
        $out->setIndent(true);
        $out->startDocument('1.0', 'UTF-8');
        $out->startElement('phpcs');
        $out->writeAttribute('version', '@package_version@');

        $errorsShown = 0;

        foreach ($report['files'] as $filename => $file) {
            if (empty($file['messages']) === true) {
                continue;
            }

            $out->startElement('file');
            $out->writeAttribute('name', $filename);
            $out->writeAttribute('errors', $file['errors']);
            $out->writeAttribute('warnings', $file['warnings']);

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $error['type'] = strtolower($error['type']);
                        if (PHP_CODESNIFFER_ENCODING !== 'utf-8') {
                            $error['message'] = iconv(PHP_CODESNIFFER_ENCODING, 'utf-8', $error['message']);
                        }

                        $out->startElement($error['type']);
                        $out->writeAttribute('line', $line);
                        $out->writeAttribute('column', $column);
                        $out->writeAttribute('source', $error['source']);
                        $out->writeAttribute('severity', $error['severity']);
                        $out->text($error['message']);
                        $out->endElement();

                        $errorsShown++;
                    }
                }
            }//end foreach

            $out->endElement();
        }//end foreach

        $out->endElement();
        echo $out->flush();

        return $errorsShown;

    }//end generate()


}//end class

?>
