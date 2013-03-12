<?php
/**
 * A class to represent elements that have a value => comment format.
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
 * A class to represent elements that have a value => comment format.
 *
 * An example of a pair element tag is the \@throws as it has an exception type
 * and a comment on the circumstance of when the exception is thrown.
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
class PHP_CodeSniffer_CommentParser_PairElement extends PHP_CodeSniffer_CommentParser_AbstractDocElement
{

    /**
     * The value of the tag.
     *
     * @var string
     */
    private $_value = '';

    /**
     * The comment of the tag.
     *
     * @var string
     */
    private $_comment = '';

    /**
     * The whitespace that exists before the value elem.
     *
     * @var string
     */
    private $_valueWhitespace = '';

    /**
     * The whitespace that exists before the comment elem.
     *
     * @var string
     */
    private $_commentWhitespace = '';


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_PairElement doc tag.
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
        return array(
                'value',
                'comment',
               );

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
        $element           = '_'.$name;
        $whitespace        = $element.'Whitespace';
        $this->$element    = $content;
        $this->$whitespace = $whitespaceBefore;

    }//end processSubElement()


    /**
     * Returns the value of the tag.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;

    }//end getValue()


    /**
     * Returns the comment associated with the value of this tag.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_comment;

    }//end getComment()


    /**
     * Returns the whitespace before the content of this tag.
     *
     * @return string
     */
    public function getWhitespaceBeforeValue()
    {
        return $this->_valueWhitespace;

    }//end getWhitespaceBeforeValue()


}//end class

?>
