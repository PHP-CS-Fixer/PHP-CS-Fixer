<?php
/**
 * A class to represent Comments of a doc comment.
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

if (class_exists('PHP_CodeSniffer_CommentParser_SingleElement', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_SingleElement not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * A class to represent Comments of a doc comment.
 *
 * Comments are in the following format.
 * <code>
 * /** <--this is the start of the comment.
 *  * This is a short comment description
 *  *
 *  * This is a long comment description
 *  * <-- this is the end of the comment
 *  * @return something
 *  {@/}
 *  </code>
 *
 * Note that the sentence before two newlines is assumed
 * the short comment description.
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
class PHP_CodeSniffer_CommentParser_CommentElement extends PHP_CodeSniffer_CommentParser_SingleElement
{


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_CommentElement.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElement $previousElement The element
     *                                                                  that
     *                                                                  appears
     *                                                                  before this
     *                                                                  element.
     * @param array                                    $tokens          The tokens
     *                                                                  that make
     *                                                                  up this
     *                                                                  element.
     * @param PHP_CodeSniffer_File                     $phpcsFile       The file
     *                                                                  that this
     *                                                                  element is
     *                                                                  in.
     */
    public function __construct(
        $previousElement,
        $tokens,
        PHP_CodeSniffer_File $phpcsFile
    ) {
        parent::__construct($previousElement, $tokens, 'comment', $phpcsFile);

    }//end __construct()


    /**
     * Returns the short comment description.
     *
     * @return string
     * @see getLongComment()
     */
    public function getShortComment()
    {
        $pos = $this->_getShortCommentEndPos();
        if ($pos === -1) {
            return '';
        }

        return implode('', array_slice($this->tokens, 0, ($pos + 1)));

    }//end getShortComment()


    /**
     * Returns the last token position of the short comment description.
     *
     * @return int The last token position of the short comment description
     * @see _getLongCommentStartPos()
     */
    private function _getShortCommentEndPos()
    {
        $found      = false;
        $whiteSpace = array(
                       ' ',
                       "\t",
                      );

        foreach ($this->tokens as $pos => $token) {
            $token = str_replace($whiteSpace, '', $token);
            if ($token === $this->phpcsFile->eolChar) {
                if ($found === false) {
                    // Include newlines before short description.
                    continue;
                } else {
                    if (isset($this->tokens[($pos + 1)]) === true) {
                        if ($this->tokens[($pos + 1)] === $this->phpcsFile->eolChar) {
                            return ($pos - 1);
                        }
                    } else {
                        return $pos;
                    }
                }
            } else {
                $found = true;
            }
        }//end foreach

        return (count($this->tokens) - 1);

    }//end _getShortCommentEndPos()


    /**
     * Returns the long comment description.
     *
     * @return string
     * @see getShortComment
     */
    public function getLongComment()
    {
        $start = $this->_getLongCommentStartPos();
        if ($start === -1) {
            return '';
        }

        return implode('', array_slice($this->tokens, $start));

    }//end getLongComment()


    /**
     * Returns the start position of the long comment description.
     *
     * Returns -1 if there is no long comment.
     *
     * @return int The start position of the long comment description.
     * @see _getShortCommentEndPos()
     */
    private function _getLongCommentStartPos()
    {
        $pos = ($this->_getShortCommentEndPos() + 1);
        if ($pos === (count($this->tokens) - 1)) {
            return -1;
        }

        $count = count($this->tokens);
        for ($i = $pos; $i < $count; $i++) {
            $content = trim($this->tokens[$i]);
            if ($content !== '') {
                if ($content{0} === '@') {
                    return -1;
                }

                return $i;
            }
        }

        return -1;

    }//end _getLongCommentStartPos()


    /**
     * Returns the whitespace that exists between
     * the short and the long comment description.
     *
     * @return string
     */
    public function getWhiteSpaceBetween()
    {
        $endShort  = ($this->_getShortCommentEndPos() + 1);
        $startLong = ($this->_getLongCommentStartPos() - 1);
        if ($startLong === -1) {
            return '';
        }

        return implode(
            '',
            array_slice($this->tokens, $endShort, ($startLong - $endShort))
        );

    }//end getWhiteSpaceBetween()


    /**
     * Returns the number of newlines that exist before the tags.
     *
     * @return int
     */
    public function getNewlineAfter()
    {
        $long = $this->getLongComment();
        if ($long !== '') {
            $long     = rtrim($long, ' ');
            $long     = strrev($long);
            $newlines = strspn($long, $this->phpcsFile->eolChar);
        } else {
            $endShort = ($this->_getShortCommentEndPos() + 1);
            $after    = implode('', array_slice($this->tokens, $endShort));
            $after    = trim($after, ' ');
            $newlines = strspn($after, $this->phpcsFile->eolChar);
        }

        return ($newlines / strlen($this->phpcsFile->eolChar));

    }//end getNewlineAfter()


    /**
     * Returns true if there is no comment.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (trim($this->getContent()) === '');

    }//end isEmpty()


}//end class

?>
