<?php
/**
 * Parses doc comments.
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

if (class_exists('PHP_CodeSniffer_CommentParser_CommentElement', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_CommentElement not found';
    throw new PHP_CodeSniffer_Exception($error);
}

if (class_exists('PHP_CodeSniffer_CommentParser_ParserException', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_ParserException not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses doc comments.
 *
 * This abstract parser handles the following tags:
 *
 * <ul>
 *  <li>The short description and the long description</li>
 *  <li>@see</li>
 *  <li>@link</li>
 *  <li>@deprecated</li>
 *  <li>@since</li>
 * </ul>
 *
 * Extending classes should implement the getAllowedTags() method to return the
 * tags that they wish to process, omitting the tags that this base class
 * processes. When one of these tags in encountered, the process&lt;tag_name&gt;
 * method is called on that class. For example, if a parser's getAllowedTags()
 * method returns \@param as one of its tags, the processParam method will be
 * called so that the parser can process such a tag.
 *
 * The method is passed the tokens that comprise this tag. The tokens array
 * includes the whitespace that exists between the tokens, as separate tokens.
 * It's up to the method to create a element that implements the DocElement
 * interface, which should be returned. The AbstractDocElement class is a helper
 * class that can be used to handle most of the parsing of the tokens into their
 * individual sub elements. It requires that you construct it with the element
 * previous to the element currently being processed, which can be acquired
 * with the protected $previousElement class member of this class.
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
abstract class PHP_CodeSniffer_CommentParser_AbstractParser
{

    /**
     * The comment element that appears in the doc comment.
     *
     * @var PHP_CodeSniffer_CommentParser_CommentElement
     */
    protected $comment = null;

    /**
     * The string content of the comment.
     *
     * @var string
     */
    protected $commentString = '';

    /**
     * The file that the comment exists in.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $phpcsFile = null;

    /**
     * The word tokens that appear in the comment.
     *
     * Whitespace tokens also appear in this stack, but are separate tokens
     * from words.
     *
     * @var array(string)
     */
    protected $words = array();

    /**
     * An array of all tags found in the comment.
     *
     * @var array(string)
     */
    protected $foundTags = array();

    /**
     * The previous doc element that was processed.
     *
     * null if the current element being processed is the first element in the
     * doc comment.
     *
     * @var PHP_CodeSniffer_CommentParser_DocElement
     */
    protected $previousElement = null;

    /**
     * A list of see elements that appear in this doc comment.
     *
     * @var array(PHP_CodeSniffer_CommentParser_SingleElement)
     */
    protected $sees = array();

    /**
     * A list of see elements that appear in this doc comment.
     *
     * @var array(PHP_CodeSniffer_CommentParser_SingleElement)
     */
    protected $deprecated = null;

    /**
     * A list of see elements that appear in this doc comment.
     *
     * @var array(PHP_CodeSniffer_CommentParser_SingleElement)
     */
    protected $links = array();

    /**
     * A element to represent \@since tags.
     *
     * @var PHP_CodeSniffer_CommentParser_SingleElement
     */
    protected $since = null;

    /**
     * True if the comment has been parsed.
     *
     * @var boolean
     */
    private $_hasParsed = false;

    /**
     * The tags that this class can process.
     *
     * @var array(string)
     */
    private static $_tags = array(
                             'see'        => false,
                             'link'       => false,
                             'deprecated' => true,
                             'since'      => true,
                            );

    /**
     * An array of unknown tags.
     *
     * @var array(string)
     */
    public $unknown = array();

    /**
     * The order of tags.
     *
     * @var array(string)
     */
    public $orders = array();


    /**
     * Constructs a Doc Comment Parser.
     *
     * @param string               $comment   The comment to parse.
     * @param PHP_CodeSniffer_File $phpcsFile The file that this comment is in.
     */
    public function __construct($comment, PHP_CodeSniffer_File $phpcsFile)
    {
        $this->commentString = $comment;
        $this->phpcsFile     = $phpcsFile;

    }//end __construct()


    /**
     * Initiates the parsing of the doc comment.
     *
     * @return void
     * @throws PHP_CodeSniffer_CommentParser_ParserException If the parser finds a
     *                                                       problem with the
     *                                                       comment.
     */
    public function parse()
    {
        if ($this->_hasParsed === false) {
            $this->_parse($this->commentString);
        }

    }//end parse()


    /**
     * Parse the comment.
     *
     * @param string $comment The doc comment to parse.
     *
     * @return void
     * @see _parseWords()
     */
    private function _parse($comment)
    {
        // Firstly, remove the comment tags and any stars from the left side.
        $lines = explode($this->phpcsFile->eolChar, $comment);
        foreach ($lines as &$line) {
            $line = trim($line);

            if ($line !== '') {
                if (substr($line, 0, 3) === '/**') {
                    $line = substr($line, 3);
                } else if (substr($line, -2, 2) === '*/') {
                    $line = substr($line, 0, -2);
                } else if ($line{0} === '*') {
                    $line = substr($line, 1);
                }

                // Add the words to the stack, preserving newlines. Other parsers
                // might be interested in the spaces between words, so tokenize
                // spaces as well as separate tokens.
                $flags = (PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $words = preg_split(
                    '|(\s+)|',
                    $line.$this->phpcsFile->eolChar,
                    -1,
                    $flags
                );

                $this->words = array_merge($this->words, $words);
            }//end if
        }//end foreach

        $this->_parseWords();

    }//end _parse()


    /**
     * Parses each word within the doc comment.
     *
     * @return void
     * @see _parse()
     * @throws PHP_CodeSniffer_CommentParser_ParserException If more than the allowed
     *                                                       number of occurences of
     *                                                       a tag is found.
     */
    private function _parseWords()
    {
        $allowedTags     = (self::$_tags + $this->getAllowedTags());
        $allowedTagNames = array_keys($allowedTags);
        $prevTagPos      = false;
        $wordWasEmpty    = true;

        foreach ($this->words as $wordPos => $word) {
            if (trim($word) !== '') {
                $wordWasEmpty = false;
            }

            if ($word{0} === '@') {
                $tag = substr($word, 1);

                // Filter out @ tags in the comment description.
                // A real comment tag should have whitespace and a newline before it.
                if (isset($this->words[($wordPos - 1)]) === false
                    || trim($this->words[($wordPos - 1)]) !== ''
                ) {
                    continue;
                }

                if (isset($this->words[($wordPos - 2)]) === false
                    || $this->words[($wordPos - 2)] !== $this->phpcsFile->eolChar
                ) {
                    continue;
                }

                $this->foundTags[] = array(
                                      'tag'  => $tag,
                                      'line' => $this->getLine($wordPos),
                                      'pos'  => $wordPos,
                                     );

                if ($prevTagPos !== false) {
                    // There was a tag before this so let's process it.
                    $prevTag = substr($this->words[$prevTagPos], 1);
                    $this->parseTag($prevTag, $prevTagPos, ($wordPos - 1));
                } else {
                    // There must have been a comment before this tag, so
                    // let's process that.
                    $this->parseTag('comment', 0, ($wordPos - 1));
                }

                $prevTagPos = $wordPos;

                if (in_array($tag, $allowedTagNames) === false) {
                    // This is not a tag that we process, but let's check to
                    // see if it is a tag we know about. If we don't know about it,
                    // we add it to a list of unknown tags.
                    $knownTags = array(
                                  'abstract',
                                  'access',
                                  'example',
                                  'filesource',
                                  'global',
                                  'ignore',
                                  'internal',
                                  'name',
                                  'static',
                                  'staticvar',
                                  'todo',
                                  'tutorial',
                                  'uses',
                                  'package_version@',
                                 );

                    if (in_array($tag, $knownTags) === false) {
                        $this->unknown[] = array(
                                            'tag'  => $tag,
                                            'line' => $this->getLine($wordPos),
                                            'pos'  => $wordPos,
                                           );
                    }
                }//end if
            }//end if
        }//end foreach

        // Only process this tag if there was something to process.
        if ($wordWasEmpty === false) {
            if ($prevTagPos === false) {
                // There must only be a comment in this doc comment.
                $this->parseTag('comment', 0, count($this->words));
            } else {
                // Process the last tag element.
                $prevTag  = substr($this->words[$prevTagPos], 1);
                $numWords = count($this->words);
                $endPos   = $numWords;

                if ($prevTag === 'package' || $prevTag === 'subpackage') {
                    // These are single-word tags, so anything after a newline
                    // is really a comment.
                    for ($endPos = $prevTagPos; $endPos < $numWords; $endPos++) {
                        if (strpos($this->words[$endPos], $this->phpcsFile->eolChar) !== false) {
                            break;
                        }
                    }
                }

                $this->parseTag($prevTag, $prevTagPos, $endPos);

                if ($endPos !== $numWords) {
                    // Process the final comment, if it is not empty.
                    $tokens  = array_slice($this->words, ($endPos + 1), $numWords);
                    $content = implode('', $tokens);
                    if (trim($content) !== '') {
                        $this->parseTag('comment', ($endPos + 1), $numWords);
                    }
                }
            }//end if
        }//end if

    }//end _parseWords()


    /**
     * Returns the line that the token exists on in the doc comment.
     *
     * @param int $tokenPos The position in the words stack to find the line
     *                      number for.
     *
     * @return int
     */
    protected function getLine($tokenPos)
    {
        $newlines = 0;
        for ($i = 0; $i < $tokenPos; $i++) {
            $newlines += substr_count($this->phpcsFile->eolChar, $this->words[$i]);
        }

        return $newlines;

    }//end getLine()


    /**
     * Parses see tag element within the doc comment.
     *
     * @param array(string) $tokens The word tokens that comprise this element.
     *
     * @return DocElement The element that represents this see comment.
     */
    protected function parseSee($tokens)
    {
        $see = new PHP_CodeSniffer_CommentParser_SingleElement(
            $this->previousElement,
            $tokens,
            'see',
            $this->phpcsFile
        );

        $this->sees[] = $see;
        return $see;

    }//end parseSee()


    /**
     * Parses the comment element that appears at the top of the doc comment.
     *
     * @param array(string) $tokens The word tokens that comprise this element.
     *
     * @return DocElement The element that represents this comment element.
     */
    protected function parseComment($tokens)
    {
        $this->comment = new PHP_CodeSniffer_CommentParser_CommentElement(
            $this->previousElement,
            $tokens,
            $this->phpcsFile
        );

        return $this->comment;

    }//end parseComment()


    /**
     * Parses \@deprecated tags.
     *
     * @param array(string) $tokens The word tokens that comprise this element.
     *
     * @return DocElement The element that represents this deprecated tag.
     */
    protected function parseDeprecated($tokens)
    {
        $this->deprecated = new PHP_CodeSniffer_CommentParser_SingleElement(
            $this->previousElement,
            $tokens,
            'deprecated',
            $this->phpcsFile
        );

        return $this->deprecated;

    }//end parseDeprecated()


    /**
     * Parses \@since tags.
     *
     * @param array(string) $tokens The word tokens that comprise this element.
     *
     * @return SingleElement The element that represents this since tag.
     */
    protected function parseSince($tokens)
    {
        $this->since = new PHP_CodeSniffer_CommentParser_SingleElement(
            $this->previousElement,
            $tokens,
            'since',
            $this->phpcsFile
        );

        return $this->since;

    }//end parseSince()


    /**
     * Parses \@link tags.
     *
     * @param array(string) $tokens The word tokens that comprise this element.
     *
     * @return SingleElement The element that represents this link tag.
     */
    protected function parseLink($tokens)
    {
        $link = new PHP_CodeSniffer_CommentParser_SingleElement(
            $this->previousElement,
            $tokens,
            'link',
            $this->phpcsFile
        );

        $this->links[] = $link;
        return $link;

    }//end parseLink()


    /**
     * Returns the see elements that appear in this doc comment.
     *
     * @return array(SingleElement)
     */
    public function getSees()
    {
        return $this->sees;

    }//end getSees()


    /**
     * Returns the comment element that appears at the top of this doc comment.
     *
     * @return CommentElement
     */
    public function getComment()
    {
        return $this->comment;

    }//end getComment()


    /**
     * Returns the word list.
     *
     * @return array
     */
    public function getWords()
    {
        return $this->words;

    }//end getWords()


    /**
     * Returns the list of found tags.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->foundTags;

    }//end getTags()


    /**
     * Returns the link elements found in this comment.
     *
     * Returns an empty array if no links are found in the comment.
     *
     * @return array(SingleElement)
     */
    public function getLinks()
    {
        return $this->links;

    }//end getLinks()


    /**
     * Returns the deprecated element found in this comment.
     *
     * Returns null if no element exists in the comment.
     *
     * @return SingleElement
     */
    public function getDeprecated()
    {
        return $this->deprecated;

    }//end getDeprecated()


    /**
     * Returns the since element found in this comment.
     *
     * Returns null if no element exists in the comment.
     *
     * @return SingleElement
     */
    public function getSince()
    {
        return $this->since;

    }//end getSince()


    /**
     * Parses the specified tag.
     *
     * @param string $tag   The tag name to parse (omitting the @ symbol from
     *                      the tag)
     * @param int    $start The position in the word tokens where this element
     *                      started.
     * @param int    $end   The position in the word tokens where this element
     *                      ended.
     *
     * @return void
     * @throws Exception If the process method for the tag cannot be found.
     */
    protected function parseTag($tag, $start, $end)
    {
        $tokens = array_slice($this->words, ($start + 1), ($end - $start));

        $allowedTags     = (self::$_tags + $this->getAllowedTags());
        $allowedTagNames = array_keys($allowedTags);
        if ($tag === 'comment' || in_array($tag, $allowedTagNames) === true) {
            $method = 'parse'.$tag;
            if (method_exists($this, $method) === false) {
                $error = 'Method '.$method.' must be implemented to process '.$tag.' tags';
                throw new Exception($error);
            }

            $this->previousElement = $this->$method($tokens);
        } else {
            $this->previousElement = new PHP_CodeSniffer_CommentParser_SingleElement(
                $this->previousElement,
                $tokens,
                $tag,
                $this->phpcsFile
            );
        }

        $this->orders[] = $tag;

        if ($this->previousElement === null
            || ($this->previousElement instanceof PHP_CodeSniffer_CommentParser_DocElement) === false
        ) {
            throw new Exception('Parse method must return a DocElement');
        }

    }//end parseTag()


    /**
     * Returns a list of tags that this comment parser allows for it's comment.
     *
     * Each tag should indicate if only one entry of this tag can exist in the
     * comment by specifying true as the array value, or false if more than one
     * is allowed. Each tag should omit the @ symbol. Only tags other than
     * the standard tags should be returned.
     *
     * @return array(string => boolean)
     */
    protected abstract function getAllowedTags();


    /**
     * Returns the tag orders (index => tagName).
     *
     * @return array
     */
    public function getTagOrders()
    {
        return $this->orders;

    }//end getTagOrders()


    /**
     * Returns the unknown tags.
     *
     * @return array
     */
    public function getUnknown()
    {
        return $this->unknown;

    }//end getUnknown()


}//end class

?>
