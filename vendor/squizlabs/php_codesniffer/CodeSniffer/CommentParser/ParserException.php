<?php
/**
 * An exception to be thrown when a DocCommentParser finds an anomaly in a
 * doc comment.
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
 * An exception to be thrown when a DocCommentParser finds an anomaly in a
 * doc comment.
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
class PHP_CodeSniffer_CommentParser_ParserException extends Exception
{

    /**
     * The line where the exception occurred, in relation to the doc comment.
     *
     * @var int
     */
    private $_line = 0;


    /**
     * Constructs a DocCommentParserException.
     *
     * @param string $message The message of the exception.
     * @param int    $line    The position in comment where the error occurred.
     *                        A position of 0 indicates that the error occurred
     *                        at the opening line of the doc comment.
     */
    public function __construct($message, $line)
    {
        parent::__construct($message);
        $this->_line = $line;

    }//end __construct()


    /**
     * Returns the line number within the comment where the exception occurred.
     *
     * @return int
     */
    public function getLineWithinComment()
    {
        return $this->_line;

    }//end getLineWithinComment()


}//end class

?>
