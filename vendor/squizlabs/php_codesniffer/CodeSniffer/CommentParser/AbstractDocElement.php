<?php
/**
 * A class to handle most of the parsing operations of a doc comment element.
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

if (interface_exists('PHP_CodeSniffer_CommentParser_DocElement', true) === false) {
    $error = 'Interface PHP_CodeSniffer_CommentParser_DocElement not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * A class to handle most of the parsing operations of a doc comment element.
 *
 * Extending classes should implement the getSubElements method to return
 * a list of elements that the doc comment element contains, in the order that
 * they appear in the element. For example a function parameter element has a
 * type, a variable name and a comment. It should therefore implement the method
 * as follows:
 *
 * <code>
 *    protected function getSubElements()
 *    {
 *        return array(
 *                'type',
 *                'variable',
 *                'comment',
 *               );
 *    }
 * </code>
 *
 * The processSubElement will be called for each of the sub elements to allow
 * the extending class to process them. So for the parameter element we would
 * have:
 *
 * <code>
 *    protected function processSubElement($name, $content, $whitespaceBefore)
 *    {
 *        if ($name === 'type') {
 *            echo 'The name of the variable was '.$content;
 *        }
 *        // Process other tags.
 *    }
 * </code>
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
abstract class PHP_CodeSniffer_CommentParser_AbstractDocElement implements PHP_CodeSniffer_CommentParser_DocElement
{

    /**
     * The element previous to this element.
     *
     * @var PHP_CodeSniffer_CommentParser_DocElement
     */
    protected $previousElement = null;

    /**
     * The element proceeding this element.
     *
     * @var PHP_CodeSniffer_CommentParser_DocElement
     */
    protected $nextElement = null;

    /**
     * The whitespace the occurs after this element and its sub elements.
     *
     * @var string
     */
    protected $afterWhitespace = '';

    /**
     * The tokens that comprise this element.
     *
     * @var array(string)
     */
    protected $tokens = array();

    /**
     * The file this element is in.
     *
     * @var array(string)
     */
    protected $phpcsFile = null;

    /**
     * The tag that this element represents (omitting the @ symbol).
     *
     * @var string
     */
    protected $tag = '';


    /**
     * Constructs a Doc Element.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElement $previousElement The element
     *                                                                  that ocurred
     *                                                                  before this.
     * @param array                                    $tokens          The tokens of
     *                                                                  this element.
     * @param string                                   $tag             The doc
     *                                                                  element tag
     *                                                                  this element
     *                                                                  represents.
     * @param PHP_CodeSniffer_File                     $phpcsFile       The file that
     *                                                                  this element
     *                                                                  is in.
     *
     * @throws Exception If $previousElement in not a DocElement or if
     *                   getSubElements() does not return an array.
     */
    public function __construct(
        $previousElement,
        array $tokens,
        $tag,
        PHP_CodeSniffer_File $phpcsFile
    ) {
        if ($previousElement !== null
            && ($previousElement instanceof PHP_CodeSniffer_CommentParser_DocElement) === false
        ) {
            $error = '$previousElement must be an instance of DocElement';
            throw new Exception($error);
        }

        $this->phpcsFile = $phpcsFile;

        $this->previousElement = $previousElement;
        if ($previousElement !== null) {
            $this->previousElement->nextElement = $this;
        }

        $this->tag    = $tag;
        $this->tokens = $tokens;

        $subElements = $this->getSubElements();

        if (is_array($subElements) === false) {
            throw new Exception('getSubElements() must return an array');
        }

        $whitespace            = '';
        $currElem              = 0;
        $lastElement           = '';
        $lastElementWhitespace = null;
        $numSubElements        = count($subElements);

        foreach ($this->tokens as $token) {
            if (trim($token) === '') {
                $whitespace .= $token;
            } else {
                if ($currElem < ($numSubElements - 1)) {
                    $element = $subElements[$currElem];
                    $this->processSubElement($element, $token, $whitespace);
                    $whitespace = '';
                    $currElem++;
                } else {
                    if ($lastElementWhitespace === null) {
                        $lastElementWhitespace = $whitespace;
                    }

                    $lastElement .= $whitespace.$token;
                    $whitespace   = '';
                }
            }
        }//end foreach

        $lastElement     = ltrim($lastElement);
        $lastElementName = $subElements[($numSubElements - 1)];

        // Process the last element in this tag.
        $this->processSubElement(
            $lastElementName,
            $lastElement,
            $lastElementWhitespace
        );

        $this->afterWhitespace = $whitespace;

    }//end __construct()


    /**
     * Returns the element that exists before this.
     *
     * @return PHP_CodeSniffer_CommentParser_DocElement
     */
    public function getPreviousElement()
    {
        return $this->previousElement;

    }//end getPreviousElement()


    /**
     * Returns the element that exists after this.
     *
     * @return PHP_CodeSniffer_CommentParser_DocElement
     */
    public function getNextElement()
    {
        return $this->nextElement;

    }//end getNextElement()


    /**
     * Returns the whitespace that exists before this element.
     *
     * @return string
     */
    public function getWhitespaceBefore()
    {
        if ($this->previousElement !== null) {
            return $this->previousElement->getWhitespaceAfter();
        }

        return '';

    }//end getWhitespaceBefore()


    /**
     * Returns the whitespace that exists after this element.
     *
     * @return string
     */
    public function getWhitespaceAfter()
    {
        return $this->afterWhitespace;

    }//end getWhitespaceAfter()


    /**
     * Returns the order that this element appears in the comment.
     *
     * @return int
     */
    public function getOrder()
    {
        if ($this->previousElement === null) {
            return 1;
        } else {
            return ($this->previousElement->getOrder() + 1);
        }

    }//end getOrder()


    /**
     * Returns the tag that this element represents, ommiting the @ symbol.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;

    }//end getTag()


    /**
     * Returns the raw content of this element, ommiting the tag.
     *
     * @return string
     */
    public function getRawContent()
    {
        return implode('', $this->tokens);

    }//end getRawContent()


    /**
     * Returns the comment tokens.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;

    }//end getTokens()


    /**
     * Returns the line in which this element first occured.
     *
     * @return int
     */
    public function getLine()
    {
        if ($this->previousElement === null) {
            // First element is on line one.
            return 1;
        } else {
            $previousContent = $this->previousElement->getRawContent();
            $previousLine    = $this->previousElement->getLine();

            return ($previousLine + substr_count($previousContent, $this->phpcsFile->eolChar));
        }

    }//end getLine()


    /**
     * Returns the sub element names that make up this element in the order they
     * appear in the element.
     *
     * @return array(string)
     * @see processSubElement()
     */
    abstract protected function getSubElements();


    /**
     * Called to process each sub element as sepcified in the return value
     * of getSubElements().
     *
     * @param string $name             The name of the element to process.
     * @param string $content          The content of the the element.
     * @param string $whitespaceBefore The whitespace found before this element.
     *
     * @return void
     * @see getSubElements()
     */
    abstract protected function processSubElement(
        $name,
        $content,
        $whitespaceBefore
    );


}//end class

?>
