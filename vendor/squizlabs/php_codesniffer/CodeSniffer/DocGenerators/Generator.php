<?php
/**
 * The base class for all PHP_CodeSniffer documentation generators.
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
 * The base class for all PHP_CodeSniffer documentation generators.
 *
 * Documentation generators are used to print documentation about code sniffs
 * in a standard.
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
class PHP_CodeSniffer_DocGenerators_Generator
{

    /**
     * The name of the coding standard we are generating docs for.
     *
     * @var string
     */
    private $_standard = '';

    /**
     * An array of sniffs that we are limiting the generated docs to.
     *
     * If this array is empty, docs are generated for all sniffs in the
     * supplied coding standard.
     *
     * @var string
     */
    private $_sniffs = array();


    /**
     * Constructs a PHP_CodeSniffer_DocGenerators_Generator object.
     *
     * @param string $standard The name of the coding standard to generate
     *                         docs for.
     * @param array  $sniffs   An array of sniffs that we are limiting the
     *                         generated docs to.
     *
     * @see generate()
     */
    public function __construct($standard, array $sniffs=array())
    {
        $this->_standard = $standard;
        $this->_sniffs   = $sniffs;

    }//end __construct()


    /**
     * Retrieves the title of the sniff from the DOMNode supplied.
     *
     * @param DOMNode $doc The DOMNode object for the sniff.
     *                     It represents the "documentation" tag in the XML
     *                     standard file.
     *
     * @return string
     */
    protected function getTitle(DOMNode $doc)
    {
        return $doc->getAttribute('title');

    }//end getTitle()


    /**
     * Retrieves the name of the standard we are generating docs for.
     *
     * @return string
     */
    protected function getStandard()
    {
        return $this->_standard;

    }//end getStandard()


    /**
     * Generates the documentation for a standard.
     *
     * It's probably wise for doc generators to override this method so they
     * have control over how the docs are produced. Otherwise, the processSniff
     * method should be overridden to output content for each sniff.
     *
     * @return void
     * @see processSniff()
     */
    public function generate()
    {
        $standardFiles = $this->getStandardFiles();

        foreach ($standardFiles as $standard) {
            $doc = new DOMDocument();
            $doc->load($standard);
            $documentation = $doc->getElementsByTagName('documentation')->item(0);
            $this->processSniff($documentation);
        }

    }//end generate()


    /**
     * Returns a list of paths to XML standard files for all sniffs in a standard.
     *
     * Any sniffs that do not have an XML standard file are obviously not included
     * in the returned array. If documentation is only being generated for some
     * sniffs (ie. $this->_sniffs is not empty) then all others sniffs will
     * be filtered from the results as well.
     *
     * @return array(string)
     */
    protected function getStandardFiles()
    {
        if (is_dir($this->_standard) === true) {
            // This is a custom standard.
            $standardDir = $this->_standard;
            $standard    = basename($this->_standard);
        } else {
            $standardDir
                = realpath(dirname(__FILE__).'/../Standards/'.$this->_standard);

            $standard = $this->_standard;
        }

        $phpcs = new PHP_CodeSniffer();
        $sniffs = $phpcs->getSniffFiles($standardDir, $standard);

        $standardFiles = array();
        foreach ($sniffs as $sniff) {
            if (empty($this->_sniffs) === false) {
                // We are limiting the docs to certain sniffs only, so filter
                // out any unwanted sniffs.
                $sniffName = substr($sniff, (strrpos($sniff, '/') + 1));
                $sniffName = substr($sniffName, 0, -9);
                if (in_array($sniffName, $this->_sniffs) === false) {
                    continue;
                }
            }

            $standardFile = str_replace(
                DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR.'Docs'.DIRECTORY_SEPARATOR,
                $sniff
            );
            $standardFile = str_replace('Sniff.php', 'Standard.xml', $standardFile);

            if (is_file($standardFile) === true) {
                $standardFiles[] = $standardFile;
            }
        }//end foreach

        return $standardFiles;

    }//end getStandardFiles()


    /**
     * Process the documentation for a single sniff.
     *
     * Doc generators should override this function to produce output.
     *
     * @param DOMNode $doc The DOMNode object for the sniff.
     *                     It represents the "documentation" tag in the XML
     *                     standard file.
     *
     * @return void
     * @see generate()
     */
    protected function processSniff(DOMNode $doc)
    {

    }//end processSniff()


}//end class

?>
