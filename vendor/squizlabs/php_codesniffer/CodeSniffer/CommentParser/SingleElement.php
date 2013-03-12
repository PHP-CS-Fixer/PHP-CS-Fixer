<?php
/**
 * A class to represent single element doc tags.
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

if (class_exists('PHP_CodeSniffer_CommentParser_AbstractDocElement', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_AbstractDocElement not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * A class to represent single element doc tags.
 *
 * A single element doc tag contains only one value after the tag itself. An
 * example would be the \@package tag.
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
class PHP_CodeSniffer_CommentParser_SingleElement extends PHP_CodeSniffer_CommentParser_AbstractDocElement
{

    /**
     * The content that exists after the tag.
     *
     * @var string
     * @see getContent()
     */
    protected $content = '';

    /**
     * The whitespace that exists before the content.
     *
     * @var string
     * @see getWhitespaceBeforeContent()
     */
    protected $contentWhitespace = '';


    /**
     * Constructs a SingleElement doc tag.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElement $previousElement The element
     *                                                                  before this
     *                                                                  one.
     * @param array                                    $tokens          The tokens
     *                                                                  that comprise
     *                                                                  this element.
     * @param string                                   $tag             The tag that
     *                                                                  this element
     *                                                                  represents.
     * @param PHP_CodeSniffer_File                     $phpcsFile       The file that
     *                                                                  this element
     *                                                                  is in.
     */
    public function __construct(
        $previousElement,
        $tokens,
        $tag,
        PHP_CodeSniffer_File $phpcsFile
    ) {
        parent::__construct($previousElement, $tokens, $tag, $phpcsFile);

    }//end __construct()


    /**
     * Returns the element names that this tag is comprised of, in the order
     * that they appear in the tag.
     *
     * @return array(string)
     * @see processSubElement()
     */
    protected function getSubElements()
    {
        return array('content');

    }//end getSubElements()


    /**
     * Processes the sub element with the specified name.
     *
     * @param string $name             The name of the sub element to process.
     * @param string $content          The content of this sub element.
     * @param string $whitespaceBefore The whitespace that exists before the
     *                                 sub element.
     *
     * @return void
     * @see getSubElements()
     */
    protected function processSubElement($name, $content, $whitespaceBefore)
    {
        $this->content           = $content;
        $this->contentWhitespace = $whitespaceBefore;

    }//end processSubElement()


    /**
     * Returns the content of this tag.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;

    }//end getContent()


    /**
     * Returns the whitespace before the content of this tag.
     *
     * @return string
     */
    public function getWhitespaceBeforeContent()
    {
        return $this->contentWhitespace;

    }//end getWhitespaceBeforeContent()


    /**
     * Processes a content check for single doc element.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $commentStart The line number where
     *                                           the error occurs.
     * @param string               $docBlock     Whether this is a file or
     *                                           class comment doc.
     *
     * @return void
     */
    public function process(
        PHP_CodeSniffer_File $phpcsFile,
        $commentStart,
        $docBlock
    ) {
        if ($this->content === '') {
            $errorPos = ($commentStart + $this->getLine());
            $error    = 'Content missing for %s tag in %s comment';
            $data     = array(
                         $this->tag,
                         $docBlock,
                        );
            $phpcsFile->addError($error, $errorPos, 'EmptyTagContent', $data);
        }

    }//end process()


}//end class

?>
