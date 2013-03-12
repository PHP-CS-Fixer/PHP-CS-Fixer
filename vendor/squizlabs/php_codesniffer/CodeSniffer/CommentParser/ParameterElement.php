<?php
/**
 * A class to represent param tags within a function comment.
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
 * A class to represent param tags within a function comment.
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
class PHP_CodeSniffer_CommentParser_ParameterElement extends PHP_CodeSniffer_CommentParser_AbstractDocElement
{

    /**
     * The variable name of this parameter name, including the $ sign.
     *
     * @var string
     */
    private $_varName = '';

    /**
     * The comment of this parameter tag.
     *
     * @var string
     */
    private $_comment = '';

    /**
     * The variable type of this parameter tag.
     *
     * @var string
     */
    private $_type = '';

    /**
     * The whitespace that exists before the variable name.
     *
     * @var string
     */
    private $_varNameWhitespace = '';

    /**
     * The whitespace that exists before the comment.
     *
     * @var string
     */
    private $_commentWhitespace = null;

    /**
     * The whitespace that exists before the variable type.
     *
     * @var string
     */
    private $_typeWhitespace = '';


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_ParameterElement.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElement $previousElement The element
     *                                                                  previous to
     *                                                                  this one.
     * @param array                                    $tokens          The tokens
     *                                                                  that make up
     *                                                                  this element.
     * @param PHP_CodeSniffer_File                     $phpcsFile       The file that
     *                                                                  this element
     *                                                                  is in.
     */
    public function __construct(
        $previousElement,
        $tokens,
        PHP_CodeSniffer_File $phpcsFile
    ) {
        parent::__construct($previousElement, $tokens, 'param', $phpcsFile);

        // Handle special variable type: array(x => y).
        $type = strtolower($this->_type);
        if ($this->_varName === '=>' && strpos($type, 'array(') !== false) {
            $rawContent = $this->getRawContent();
            $matches    = array();
            $pattern    = '/^(\s+)(array\(.*\))(\s+)(\$\S*)(\s+)(.*)/i';
            if (preg_match($pattern, $rawContent, $matches) !== 0) {
                // Process the sub elements correctly for this special case.
                if (count($matches) === 7) {
                    $this->processSubElement('type', $matches[2], $matches[1]);
                    $this->processSubElement('varName', $matches[4], $matches[3]);
                    $this->processSubElement('comment', $matches[6], $matches[5]);
                }
            }
        }

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
                'type',
                'varName',
                'comment',
               );

    }//end getSubElements()


    /**
     * Processes the sub element with the specified name.
     *
     * @param string $name             The name of the sub element to process.
     * @param string $content          The content of this sub element.
     * @param string $beforeWhitespace The whitespace that exists before the
     *                                 sub element.
     *
     * @return void
     * @see getSubElements()
     */
    protected function processSubElement($name, $content, $beforeWhitespace)
    {
        $element           = '_'.$name;
        $whitespace        = $element.'Whitespace';
        $this->$element    = $content;
        $this->$whitespace = $beforeWhitespace;

    }//end processSubElement()


    /**
     * Returns the variable name that this parameter tag represents.
     *
     * @return string
     */
    public function getVarName()
    {
        return $this->_varName;

    }//end getVarName()


    /**
     * Returns the variable type that this string represents.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;

    }//end getType()


    /**
     * Returns the comment of this comment for this parameter.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_comment;

    }//end getComment()


    /**
     * Returns the whitespace before the variable type.
     *
     * @return string
     * @see getWhiteSpaceBeforeVarName()
     * @see getWhiteSpaceBeforeComment()
     */
    public function getWhiteSpaceBeforeType()
    {
        return $this->_typeWhitespace;

    }//end getWhiteSpaceBeforeType()


    /**
     * Returns the whitespace before the variable name.
     *
     * @return string
     * @see getWhiteSpaceBeforeComment()
     * @see getWhiteSpaceBeforeType()
     */
    public function getWhiteSpaceBeforeVarName()
    {
        return $this->_varNameWhitespace;

    }//end getWhiteSpaceBeforeVarName()


    /**
     * Returns the whitespace before the comment.
     *
     * @return string
     * @see getWhiteSpaceBeforeVarName()
     * @see getWhiteSpaceBeforeType()
     */
    public function getWhiteSpaceBeforeComment()
    {
        return $this->_commentWhitespace;

    }//end getWhiteSpaceBeforeComment()


    /**
     * Returns the position of this parameter are it appears in the comment.
     *
     * This method differs from getOrder as it is only relative to method
     * parameters.
     *
     * @return int
     */
    public function getPosition()
    {
        if (($this->getPreviousElement() instanceof PHP_CodeSniffer_CommentParser_ParameterElement) === false) {
            return 1;
        } else {
            return ($this->getPreviousElement()->getPosition() + 1);
        }

    }//end getPosition()


    /**
     * Returns true if this parameter's variable aligns with the other's.
     *
     * @param PHP_CodeSniffer_CommentParser_ParameterElement $other The other param
     *                                                              to check
     *                                                              alignment with.
     *
     * @return boolean
     */
    public function alignsVariableWith(
        PHP_CodeSniffer_CommentParser_ParameterElement $other
    ) {
        // Format is:
        // @param type $variable Comment.
        // @param <-a-><---b---->
        // Compares the index before param variable.
        $otherVar = (strlen($other->_type) + strlen($other->_varNameWhitespace));
        $thisVar  = (strlen($this->_type) + strlen($this->_varNameWhitespace));
        if ($otherVar !== $thisVar) {
            return false;
        }

        return true;

    }//end alignsVariableWith()


    /**
     * Returns true if this parameter's comment aligns with the other's.
     *
     * @param PHP_CodeSniffer_CommentParser_ParameterElement $other The other param
     *                                                              to check
     *                                                              alignment with.
     *
     * @return boolean
     */
    public function alignsCommentWith(
        PHP_CodeSniffer_CommentParser_ParameterElement $other
    ) {
        // Compares the index before param comment.
        if (strlen($other->_commentWhitespace) === 0 && strlen($this->_commentWhitespace) === 0) {
            return true;
        }

        $otherComment
            = (strlen($other->_varName) + strlen($other->_commentWhitespace));
        $thisComment
            = (strlen($this->_varName) + strlen($this->_commentWhitespace));

        if ($otherComment !== $thisComment) {
            return false;
        }

        return true;

    }//end alignsCommentWith()


    /**
     * Returns true if this parameter aligns with the other paramter.
     *
     * @param PHP_CodeSniffer_CommentParser_ParameterElement $other The other param
     *                                                              to check
     *                                                              alignment with.
     *
     * @return boolean
     */
    public function alignsWith(PHP_CodeSniffer_CommentParser_ParameterElement $other)
    {
        if ($this->alignsVariableWith($other) === false) {
            return false;
        }

        if ($this->alignsCommentWith($other) === false) {
            return false;
        }

        return true;

    }//end alignsWith()


}//end class

?>
