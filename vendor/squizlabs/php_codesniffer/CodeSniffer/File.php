<?php
/**
 * A PHP_CodeSniffer_File object represents a PHP source file and the tokens
 * associated with it.
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
 * A PHP_CodeSniffer_File object represents a PHP source file and the tokens
 * associated with it.
 *
 * It provides a means for traversing the token stack, along with
 * other token related operations. If a PHP_CodeSniffer_Sniff finds and error or
 *  warning within a PHP_CodeSniffer_File, you can raise an error using the
 *  addError() or addWarning() methods.
 *
 * <b>Token Information</b>
 *
 * Each token within the stack contains information about itself:
 *
 * <code>
 *   array(
 *    'code'       => 301,       // the token type code (see token_get_all())
 *    'content'    => 'if',      // the token content
 *    'type'       => 'T_IF',    // the token name
 *    'line'       => 56,        // the line number when the token is located
 *    'column'     => 12,        // the column in the line where this token
 *                               // starts (starts from 1)
 *    'level'      => 2          // the depth a token is within the scopes open
 *    'conditions' => array(     // a list of scope condition token
 *                               // positions => codes that
 *                     2 => 50,  // openened the scopes that this token exists
 *                     9 => 353, // in (see conditional tokens section below)
 *                    ),
 *   );
 * </code>
 *
 * <b>Conditional Tokens</b>
 *
 * In addition to the standard token fields, conditions contain information to
 * determine where their scope begins and ends:
 *
 * <code>
 *   array(
 *    'scope_condition' => 38, // the token position of the condition
 *    'scope_opener'    => 41, // the token position that started the scope
 *    'scope_closer'    => 70, // the token position that ended the scope
 *   );
 * </code>
 *
 * The condition, the scope opener and the scope closer each contain this
 * information.
 *
 * <b>Parenthesis Tokens</b>
 *
 * Each parenthesis token (T_OPEN_PARENTHESIS and T_CLOSE_PARENTHESIS) has a
 * reference to their opening and closing parenthesis, one being itself, the
 * other being its opposite.
 *
 * <code>
 *   array(
 *    'parenthesis_opener' => 34,
 *    'parenthesis_closer' => 40,
 *   );
 * </code>
 *
 * Some tokens can "own" a set of parenthesis. For example a T_FUNCTION token
 * has parenthesis around its argument list. These tokens also have the
 * parenthesis_opener and and parenthesis_closer indices. Not all parenthesis
 * have owners, for example parenthesis used for arithmetic operations and
 * function calls. The parenthesis tokens that have an owner have the following
 * auxiliary array indices.
 *
 * <code>
 *   array(
 *    'parenthesis_opener' => 34,
 *    'parenthesis_closer' => 40,
 *    'parenthesis_owner'  => 33,
 *   );
 * </code>
 *
 * Each token within a set of parenthesis also has an array indice
 * 'nested_parenthesis' which is an array of the
 * left parenthesis => right parenthesis token positions.
 *
 * <code>
 *   'nested_parenthesis' => array(
 *                             12 => 15
 *                             11 => 14
 *                            );
 * </code>
 *
 * <b>Extended Tokens</b>
 *
 * PHP_CodeSniffer extends and augments some of the tokens created by
 * <i>token_get_all()</i>. A full list of these tokens can be seen in the
 * <i>Tokens.php</i> file.
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
class PHP_CodeSniffer_File
{

    /**
     * The absolute path to the file associated with this object.
     *
     * @var string
     */
    private $_file = '';

    /**
     * The EOL character this file uses.
     *
     * @var string
     */
    public $eolChar = '';

    /**
     * The PHP_CodeSniffer object controlling this run.
     *
     * @var PHP_CodeSniffer
     */
    public $phpcs = null;

    /**
     * The tokenizer being used for this file.
     *
     * @var object
     */
    public $tokenizer = null;

    /**
     * The tokenizer being used for this file.
     *
     * @var string
     */
    public $tokenizerType = 'PHP';

    /**
     * The number of tokens in this file.
     *
     * Stored here to save calling count() everywhere.
     *
     * @var int
     */
    public $numTokens = 0;

    /**
     * The tokens stack map.
     *
     * Note that the tokens in this array differ in format to the tokens
     * produced by token_get_all(). Tokens are initially produced with
     * token_get_all(), then augmented so that it's easier to process them.
     *
     * @var array()
     * @see Tokens.php
     */
    private $_tokens = array();

    /**
     * The errors raised from PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getErrors()
     */
    private $_errors = array();

    /**
     * The warnings raised form PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getWarnings()
     */
    private $_warnings = array();

    /**
     * Record the errors and warnings raised.
     *
     * @var bool
     */
    private $_recordErrors = true;

    /**
     * And array of lines being ignored by PHP_CodeSniffer.
     *
     * @var array()
     */
    private $_ignoredLines = array();

    /**
     * The total number of errors raised.
     *
     * @var int
     */
    private $_errorCount = 0;

    /**
     * The total number of warnings raised.
     *
     * @var int
     */
    private $_warningCount = 0;

    /**
     * An array of sniffs listening to this file's processing.
     *
     * @var array(PHP_CodeSniffer_Sniff)
     */
    private $_listeners = array();

    /**
     * The class name of the sniff currently processing the file.
     *
     * @var string
     */
    private $_activeListener = '';

    /**
     * An array of sniffs being processed and how long they took.
     *
     * @var array()
     */
    private $_listenerTimes = array();

    /**
     * An array of extensions mapping to the tokenizer to use.
     *
     * This value gets set by PHP_CodeSniffer when the object is created.
     *
     * @var array
     */
    protected $tokenizers = array();

    /**
     * An array of rules from the ruleset.xml file.
     *
     * This value gets set by PHP_CodeSniffer when the object is created.
     * It may be empty, indicating that the ruleset does not override
     * any of the default sniff settings.
     *
     * @var array
     */
    protected $ruleset = array();


    /**
     * Constructs a PHP_CodeSniffer_File.
     *
     * @param string          $file       The absolute path to the file to process.
     * @param array(string)   $listeners  The initial listeners listening
     *                                    to processing of this file.
     * @param array           $tokenizers An array of extensions mapping
     *                                    to the tokenizer to use.
     * @param array           $ruleset    An array of rules from the
     *                                    ruleset.xml file.
     * @param PHP_CodeSniffer $phpcs      The PHP_CodeSniffer object controlling
     *                                    this run.
     *
     * @throws PHP_CodeSniffer_Exception If the register() method does
     *                                   not return an array.
     */
    public function __construct(
        $file,
        array $listeners,
        array $tokenizers,
        array $ruleset,
        PHP_CodeSniffer $phpcs
    ) {
        $this->_file      = trim($file);
        $this->_listeners = $listeners;
        $this->tokenizers = $tokenizers;
        $this->ruleset    = $ruleset;
        $this->phpcs      = $phpcs;

        $cliValues = $phpcs->cli->getCommandLineValues();
        if (isset($cliValues['showSources']) === true
            && $cliValues['showSources'] !== true
            && array_key_exists('summary', $cliValues['reports']) === true
            && count($cliValues['reports']) === 1
        ) {
            $this->_recordErrors = false;
        }

    }//end __construct()


    /**
     * Sets the name of the currently active sniff.
     *
     * @param string $activeListener The class name of the current sniff.
     *
     * @return void
     */
    public function setActiveListener($activeListener)
    {
        $this->_activeListener = $activeListener;

    }//end setActiveListener()


    /**
     * Adds a listener to the token stack that listens to the specific tokens.
     *
     * When PHP_CodeSniffer encounters on the the tokens specified in $tokens,
     * it invokes the process method of the sniff.
     *
     * @param PHP_CodeSniffer_Sniff $listener The listener to add to the
     *                                        listener stack.
     * @param array(int)            $tokens   The token types the listener wishes to
     *                                        listen to.
     *
     * @return void
     */
    public function addTokenListener(PHP_CodeSniffer_Sniff $listener, array $tokens)
    {
        foreach ($tokens as $token) {
            if (isset($this->_listeners[$token]) === false) {
                $this->_listeners[$token] = array();
            }

            if (in_array($listener, $this->_listeners[$token], true) === false) {
                $this->_listeners[$token][] = $listener;
            }
        }

    }//end addTokenListener()


    /**
     * Removes a listener from listening from the specified tokens.
     *
     * @param PHP_CodeSniffer_Sniff $listener The listener to remove from the
     *                                        listener stack.
     * @param array(int)            $tokens   The token types the listener wishes to
     *                                        stop listen to.
     *
     * @return void
     */
    public function removeTokenListener(
        PHP_CodeSniffer_Sniff $listener,
        array $tokens
    ) {
        foreach ($tokens as $token) {
            if (isset($this->_listeners[$token]) === false) {
                continue;
            }

            if (in_array($listener, $this->_listeners[$token]) === true) {
                foreach ($this->_listeners[$token] as $pos => $value) {
                    if ($value === $listener) {
                        unset($this->_listeners[$token][$pos]);
                    }
                }
            }
        }

    }//end removeTokenListener()


    /**
     * Returns the token stack for this file.
     *
     * @return array()
     */
    public function getTokens()
    {
        return $this->_tokens;

    }//end getTokens()


    /**
     * Starts the stack traversal and tells listeners when tokens are found.
     *
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     */
    public function start($contents=null)
    {
        $this->_parse($contents);

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** START TOKEN PROCESSING ***".PHP_EOL;
        }

        $foundCode = false;
        $ignoring  = false;

        // Foreach of the listeners that have registered to listen for this
        // token, get them to process it.
        foreach ($this->_tokens as $stackPtr => $token) {
            // Check for ignored lines.
            if ($token['code'] === T_COMMENT) {
                if (strpos($token['content'], '@codingStandardsIgnoreStart') !== false) {
                    $ignoring = true;
                } else if (strpos($token['content'], '@codingStandardsIgnoreEnd') !== false) {
                    $ignoring = false;
                    // Ignore this comment too.
                    $this->_ignoredLines[$token['line']] = true;
                } else if (strpos($token['content'], '@codingStandardsIgnoreFile') !== false) {
                    // Ignoring the whole file, just a little late.
                    $this->_errors       = array();
                    $this->_warnings     = array();
                    $this->_errorCount   = 0;
                    $this->_warningCount = 0;
                    return;
                } else if (strpos($token['content'], '@codingStandardsChangeSetting') !== false) {
                    $start         = strpos($token['content'], '@codingStandardsChangeSetting');
                    $comment       = substr($token['content'], $start + 30);
                    $parts         = explode(' ', $comment);
                    $sniffParts    = explode('.', $parts[0]);
                    $listenerClass = $sniffParts[0].'_Sniffs_'.$sniffParts[1].'_'.$sniffParts[2].'Sniff';
                    $this->phpcs->setSniffProperty($listenerClass, $parts[1], $parts[2]);
                }
            }

            if ($ignoring === true) {
                $this->_ignoredLines[$token['line']] = true;
                continue;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                $type    = $token['type'];
                $content = str_replace($this->eolChar, '\n', $token['content']);
                echo "\t\tProcess token $stackPtr: $type => $content".PHP_EOL;
            }

            $tokenType = $token['code'];
            if ($tokenType !== T_INLINE_HTML) {
                $foundCode = true;
            }

            if (isset($this->_listeners[$tokenType]) === false) {
                continue;
            }

            foreach ($this->_listeners[$tokenType] as $listenerData) {
                // Make sure this sniff supports the tokenizer
                // we are currently using.
                $listener = $listenerData['listener'];
                $class    = $listenerData['class'];

                if (in_array($this->tokenizerType, $listenerData['tokenizers']) === false) {
                    continue;
                }

                // If the file path matches one of our ignore patterns, skip it.
                $parts = explode('_', $class);
                if (isset($parts[3]) === true) {
                    $source   = $parts[0].'.'.$parts[2].'.'.substr($parts[3], 0, -5);
                    $patterns = $this->phpcs->getIgnorePatterns($source);
                    foreach ($patterns as $pattern => $type) {
                        // While there is support for a type of each pattern
                        // (absolute or relative) we don't actually support it here.
                        $replacements = array(
                                         '\\,' => ',',
                                         '*'   => '.*',
                                        );

                        $pattern = strtr($pattern, $replacements);
                        if (preg_match("|{$pattern}|i", $this->_file) === 1) {
                            continue(2);
                        }
                    }
                }

                $this->setActiveListener($class);

                if (PHP_CODESNIFFER_VERBOSITY > 2) {
                    $startTime = microtime(true);
                    echo "\t\t\tProcessing ".$this->_activeListener.'... ';
                }

                $listener->process($this, $stackPtr);

                if (PHP_CODESNIFFER_VERBOSITY > 2) {
                    $timeTaken = (microtime(true) - $startTime);
                    if (isset($this->_listenerTimes[$this->_activeListener]) === false) {
                        $this->_listenerTimes[$this->_activeListener] = 0;
                    }

                    $this->_listenerTimes[$this->_activeListener] += $timeTaken;

                    $timeTaken = round(($timeTaken), 4);
                    echo "DONE in $timeTaken seconds".PHP_EOL;
                }

                $this->_activeListener = '';
            }//end foreach
        }//end foreach

        // Remove errors and warnings for ignored lines.
        foreach ($this->_ignoredLines as $line => $ignore) {
            if (isset($this->_errors[$line]) === true) {
                if ($this->_recordErrors === false) {
                    $this->_errorCount -= $this->_errors[$line];
                } else {
                    foreach ($this->_errors[$line] as $col => $errors) {
                        $this->_errorCount -= count($errors);
                    }
                }

                unset($this->_errors[$line]);
            }

            if (isset($this->_warnings[$line]) === true) {
                if ($this->_recordErrors === false) {
                    $this->_errorCount -= $this->_warnings[$line];
                } else {
                    foreach ($this->_warnings[$line] as $col => $warnings) {
                        $this->_warningCount -= count($warnings);
                    }
                }

                unset($this->_warnings[$line]);
            }
        }//end foreach

        if ($this->_recordErrors === false) {
            $this->_errors = array();
            $this->_warnings = array();
        }

        // If short open tags are off but the file being checked uses
        // short open tags, the whole content will be inline HTML
        // and nothing will be checked. So try and handle this case.
        if ($foundCode === false) {
            $shortTags = (bool) ini_get('short_open_tag');
            if ($shortTags === false) {
                $error = 'No PHP code was found in this file and short open tags are not allowed by this install of PHP. This file may be using short open tags but PHP does not allow them.';
                $this->addWarning($error, null, 'Internal.NoCodeFound');
            }
        }

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** END TOKEN PROCESSING ***".PHP_EOL;
        }

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** START SNIFF PROCESSING REPORT ***".PHP_EOL;

            asort($this->_listenerTimes, SORT_NUMERIC);
            $this->_listenerTimes = array_reverse($this->_listenerTimes, true);
            foreach ($this->_listenerTimes as $listener => $timeTaken) {
                echo "\t$listener: ".round(($timeTaken), 4).' secs'.PHP_EOL;
            }

            echo "\t*** END SNIFF PROCESSING REPORT ***".PHP_EOL;
        }

    }//end start()


    /**
     * Remove vars stored in this sniff that are no longer required.
     *
     * @return void
     */
    public function cleanUp()
    {
        $this->_tokens    = null;
        $this->_listeners = null;

    }//end cleanUp()


    /**
     * Tokenizes the file and prepares it for the test run.
     *
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     */
    private function _parse($contents=null)
    {
        try {
            $this->eolChar = self::detectLineEndings($this->_file, $contents);
        } catch (PHP_CodeSniffer_Exception $e) {
            $this->addWarning($e->getMessage(), null, 'Internal.DetectLineEndings');
            return;
        }

        // Determine the tokenizer from the file extension.
        $fileParts = explode('.', $this->_file);
        $extension = array_pop($fileParts);
        if (isset($this->tokenizers[$extension]) === true) {
            $tokenizerClass      = 'PHP_CodeSniffer_Tokenizers_'.$this->tokenizers[$extension];
            $this->tokenizerType = $this->tokenizers[$extension];
        } else {
            // Revert to default.
            $tokenizerClass = 'PHP_CodeSniffer_Tokenizers_'.$this->tokenizerType;
        }

        $tokenizer       = new $tokenizerClass();
        $this->tokenizer = $tokenizer;

        if ($contents === null) {
            $contents = file_get_contents($this->_file);
        }

        $this->_tokens   = self::tokenizeString($contents, $tokenizer, $this->eolChar);
        $this->numTokens = count($this->_tokens);

        // Check for mixed line endings as these can cause tokenizer errors and we
        // should let the user know that the results they get may be incorrect.
        // This is done by removing all backslashes, removing the newline char we
        // detected, then converting newlines chars into text. If any backslashes
        // are left at the end, we have additional newline chars in use.
        $contents = str_replace('\\', '', $contents);
        $contents = str_replace($this->eolChar, '', $contents);
        $contents = str_replace("\n", '\n', $contents);
        $contents = str_replace("\r", '\r', $contents);
        if (strpos($contents, '\\') !== false) {
            $error = 'File has mixed line endings; this may cause incorrect results';
            $this->addWarning($error, 0, 'Internal.LineEndings.Mixed');
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            if ($this->numTokens === 0) {
                $numLines = 0;
            } else {
                $numLines = $this->_tokens[($this->numTokens - 1)]['line'];
            }

            echo "[$this->numTokens tokens in $numLines lines]... ";
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

    }//end _parse()


    /**
     * Opens a file and detects the EOL character being used.
     *
     * @param string $file     The full path to the file.
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return string
     * @throws PHP_CodeSniffer_Exception If $file could not be opened.
     */
    public static function detectLineEndings($file, $contents=null)
    {
        if ($contents === null) {
            // Determine the newline character being used in this file.
            // Will be either \r, \r\n or \n.
            if (is_readable($file) === false) {
                $error = 'Error opening file; file no longer exists or you do not have access to read the file';
                throw new PHP_CodeSniffer_Exception($error);
            } else {
                $handle = fopen($file, 'r');
                if ($handle === false) {
                    $error = 'Error opening file; could not auto-detect line endings';
                    throw new PHP_CodeSniffer_Exception($error);
                }
            }

            $firstLine = fgets($handle);
            fclose($handle);

            $eolChar = substr($firstLine, -1);
            if ($eolChar === "\n") {
                $secondLastChar = substr($firstLine, -2, 1);
                if ($secondLastChar === "\r") {
                    $eolChar = "\r\n";
                }
            } else if ($eolChar !== "\r") {
                // Must not be an EOL char at the end of the line.
                // Probably a one-line file, so assume \n as it really
                // doesn't matter considering there are no newlines.
                $eolChar = "\n";
            }
        } else {
            if (preg_match("/\r\n?|\n/", $contents, $matches) !== 1) {
                // Assuming there are no newlines.
                $eolChar = "\n";
            } else {
                $eolChar = $matches[0];
            }
        }//end if

        return $eolChar;

    }//end detectLineEndings()


    /**
     * Adds an error to the error stack.
     *
     * @param string $error    The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the error message.
     * @param int    $severity The severity level for this error. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return void
     */
    public function addError($error, $stackPtr, $code='', $data=array(), $severity=0)
    {
        // Don't bother doing any processing if errors are just going to
        // be hidden in the reports anyway.
        if ($this->phpcs->cli->errorSeverity === 0) {
            return;
        }

        // Work out which sniff generated the error.
        if (substr($code, 0, 9) === 'Internal.') {
            // Any internal message.
            $sniff = $code;
        } else {
            $parts = explode('_', $this->_activeListener);
            if (isset($parts[3]) === true) {
                $sniff = $parts[0].'.'.$parts[2].'.'.$parts[3];

                // Remove "Sniff" from the end.
                $sniff = substr($sniff, 0, -5);
            } else {
                $sniff = 'unknownSniff';
            }

            if ($code !== '') {
                $sniff .= '.'.$code;
            }
        }

        // Make sure this message type has not been set to "warning".
        if (isset($this->ruleset[$sniff]['type']) === true
            && $this->ruleset[$sniff]['type'] === 'warning'
        ) {
            // Pass this off to the warning handler.
            $this->addWarning($error, $stackPtr, $code, $data, $severity);
            return;
        }

        // Make sure we are interested in this severity level.
        if (isset($this->ruleset[$sniff]['severity']) === true) {
            $severity = $this->ruleset[$sniff]['severity'];
        } else if ($severity === 0) {
            $severity = PHPCS_DEFAULT_ERROR_SEV;
        }

        if ($this->phpcs->cli->errorSeverity > $severity) {
            return;
        }

        // Make sure we are not ignoring this file.
        $patterns = $this->phpcs->getIgnorePatterns($sniff);
        foreach ($patterns as $pattern => $type) {
            // While there is support for a type of each pattern
            // (absolute or relative) we don't actually support it here.
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            $pattern = strtr($pattern, $replacements);
            if (preg_match("|{$pattern}|i", $this->_file) === 1) {
                return;
            }
        }

        if ($stackPtr === null) {
            $lineNum = 1;
            $column = 1;
        } else {
            $lineNum = $this->_tokens[$stackPtr]['line'];
            $column = $this->_tokens[$stackPtr]['column'];
        }

        $this->_errorCount++;
        if ($this->_recordErrors === false) {
            if (isset($this->_errors[$lineNum]) === false) {
                $this->_errors[$lineNum] = 0;
            }
            $this->_errors[$lineNum]++;
            return;
        }

        // Work out the warning message.
        if (isset($this->ruleset[$sniff]['message']) === true) {
            $error = $this->ruleset[$sniff]['message'];
        }

        if (empty($data) === true) {
            $message = $error;
        } else {
            $message = vsprintf($error, $data);
        }

        if (isset($this->_errors[$lineNum]) === false) {
            $this->_errors[$lineNum] = array();
        }

        if (isset($this->_errors[$lineNum][$column]) === false) {
            $this->_errors[$lineNum][$column] = array();
        }

        $this->_errors[$lineNum][$column][] = array(
                                               'message'  => $message,
                                               'source'   => $sniff,
                                               'severity' => $severity,
                                              );

    }//end addError()


    /**
     * Adds an warning to the warning stack.
     *
     * @param string $warning  The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the warning message.
     * @param int    $severity The severity level for this warning. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return void
     */
    public function addWarning($warning, $stackPtr, $code='', $data=array(), $severity=0)
    {
        // Don't bother doing any processing if warnings are just going to
        // be hidden in the reports anyway.
        if ($this->phpcs->cli->warningSeverity === 0) {
            return;
        }

        // Work out which sniff generated the warning.
        if (substr($code, 0, 9) === 'Internal.') {
            // Any internal message.
            $sniff = $code;
        } else {
            $parts = explode('_', $this->_activeListener);
            if (isset($parts[3]) === true) {
                $sniff = $parts[0].'.'.$parts[2].'.'.$parts[3];

                // Remove "Sniff" from the end.
                $sniff = substr($sniff, 0, -5);
            } else {
                $sniff = 'unknownSniff';
            }

            if ($code !== '') {
                $sniff .= '.'.$code;
            }
        }

        // Make sure this message type has not been set to "error".
        if (isset($this->ruleset[$sniff]['type']) === true
            && $this->ruleset[$sniff]['type'] === 'error'
        ) {
            // Pass this off to the error handler.
            $this->addError($warning, $stackPtr, $code, $data, $severity);
            return;
        }

        // Make sure we are interested in this severity level.
        if (isset($this->ruleset[$sniff]['severity']) === true) {
            $severity = $this->ruleset[$sniff]['severity'];
        } else if ($severity === 0) {
            $severity = PHPCS_DEFAULT_WARN_SEV;
        }

        if ($this->phpcs->cli->warningSeverity > $severity) {
            return;
        }

        // Make sure we are not ignoring this file.
        $patterns = $this->phpcs->getIgnorePatterns($sniff);
        foreach ($patterns as $pattern => $type) {
            // While there is support for a type of each pattern
            // (absolute or relative) we don't actually support it here.
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            $pattern = strtr($pattern, $replacements);
            if (preg_match("|{$pattern}|i", $this->_file) === 1) {
                return;
            }
        }

        if ($stackPtr === null) {
            $lineNum = 1;
            $column = 1;
        } else {
            $lineNum = $this->_tokens[$stackPtr]['line'];
            $column = $this->_tokens[$stackPtr]['column'];
        }

        $this->_warningCount++;
        if ($this->_recordErrors === false) {
            if (isset($this->_warnings[$lineNum]) === false) {
                $this->_warnings[$lineNum] = 0;
            }
            $this->_warnings[$lineNum]++;
            return;
        }

        // Work out the warning message.
        if (isset($this->ruleset[$sniff]['message']) === true) {
            $warning = $this->ruleset[$sniff]['message'];
        }

        if (empty($data) === true) {
            $message = $warning;
        } else {
            $message = vsprintf($warning, $data);
        }

        if (isset($this->_warnings[$lineNum]) === false) {
            $this->_warnings[$lineNum] = array();
        }

        if (isset($this->_warnings[$lineNum][$column]) === false) {
            $this->_warnings[$lineNum][$column] = array();
        }

        $this->_warnings[$lineNum][$column][] = array(
                                                 'message'  => $message,
                                                 'source'   => $sniff,
                                                 'severity' => $severity,
                                                );

    }//end addWarning()


    /**
     * Returns the number of errors raised.
     *
     * @return int
     */
    public function getErrorCount()
    {
        return $this->_errorCount;

    }//end getErrorCount()


    /**
     * Returns the number of warnings raised.
     *
     * @return int
     */
    public function getWarningCount()
    {
        return $this->_warningCount;

    }//end getWarningCount()


    /**
     * Returns the list of ignored lines.
     *
     * @return array
     */
    public function getIgnoredLines()
    {
        return $this->_ignoredLines;

    }//end getIgnoredLines()


    /**
     * Returns the errors raised from processing this file.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;

    }//end getErrors()


    /**
     * Returns the warnings raised from processing this file.
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->_warnings;

    }//end getWarnings()


    /**
     * Returns the absolute filename of this file.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_file;

    }//end getFilename()


    /**
     * Creates an array of tokens when given some PHP code.
     *
     * Starts by using token_get_all() but does a lot of extra processing
     * to insert information about the context of the token.
     *
     * @param string $string    The string to tokenize.
     * @param object $tokenizer A tokenizer class to use to tokenize the string.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return array
     */
    public static function tokenizeString($string, $tokenizer, $eolChar='\n')
    {
        $tokens = $tokenizer->tokenizeString($string, $eolChar);

        self::_createLineMap($tokens, $tokenizer, $eolChar);
        self::_createBracketMap($tokens, $tokenizer, $eolChar);
        self::_createParenthesisMap($tokens, $tokenizer, $eolChar);
        self::_createParenthesisNestingMap($tokens, $tokenizer, $eolChar);
        self::_createScopeMap($tokens, $tokenizer, $eolChar);

        // If we know the width of each tab, convert tabs
        // into spaces so sniffs can use one method of checking.
        if (PHP_CODESNIFFER_TAB_WIDTH > 0) {
            self::_convertTabs($tokens, $tokenizer, $eolChar);
        }

        // Column map requires the line map to be complete.
        self::_createColumnMap($tokens, $tokenizer, $eolChar);
        self::_createLevelMap($tokens, $tokenizer, $eolChar);

        // Allow the tokenizer to do additional processing if required.
        $tokenizer->processAdditional($tokens, $eolChar);

        return $tokens;

    }//end tokenizeString()


    /**
     * Creates a map of tokens => line numbers for each token.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createLineMap(&$tokens, $tokenizer, $eolChar)
    {
        $lineNumber = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokens[$i]['line'] = $lineNumber;
            if ($tokens[$i]['content'] === '') {
                continue;
            }

            $lineNumber += substr_count($tokens[$i]['content'], $eolChar);
        }

    }//end _createLineMap()


    /**
     * Converts tabs into spaces.
     *
     * Each tab can represent between 1 and $width spaces, so
     * this cannot be a straight string replace.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _convertTabs(&$tokens, $tokenizer, $eolChar)
    {
        $currColumn = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokenContent = $tokens[$i]['content'];

            if (strpos($tokenContent, "\t") === false) {
                // There are no tabs in this content.
                $currColumn += strlen($tokenContent);
            } else {
                // We need to determine the length of each tab.
                $tabs = preg_split(
                    "|(\t)|",
                    $tokenContent,
                    -1,
                    PREG_SPLIT_DELIM_CAPTURE
                );

                $tabNum       = 0;
                $tabsToSpaces = array();
                $newContent   = '';

                foreach ($tabs as $content) {
                    if ($content === '') {
                        continue;
                    }

                    if (strpos($content, "\t") === false) {
                        // This piece of content is not a tab.
                        $currColumn += strlen($content);
                        $newContent .= $content;
                    } else {
                        $lastCurrColumn = $currColumn;
                        $tabNum++;

                        // Move the pointer to the next tab stop.
                        if (($currColumn % PHP_CODESNIFFER_TAB_WIDTH) === 0) {
                            // This is the first tab, and we are already at a
                            // tab stop, so this tab counts as a single space.
                            $currColumn++;
                        } else {
                            $currColumn++;
                            while (($currColumn % PHP_CODESNIFFER_TAB_WIDTH) != 0) {
                                $currColumn++;
                            }

                            $currColumn++;
                        }

                        $length      = ($currColumn - $lastCurrColumn);
                        $newContent .= str_repeat(' ', $length);
                    }//end if
                }//end foreach

                $tokens[$i]['content'] = $newContent;
            }//end if

            if (isset($tokens[($i + 1)]['line']) === true
                && $tokens[($i + 1)]['line'] !== $tokens[$i]['line']
            ) {
                $currColumn = 1;
            }
        }//end for

    }//end _convertTabs()


    /**
     * Creates a column map.
     *
     * The column map indicates where the token started on the line where it
     * exists.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createColumnMap(&$tokens, $tokenizer, $eolChar)
    {
        $currColumn = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokens[$i]['column'] = $currColumn;
            if (isset($tokens[($i + 1)]['line']) === true
                && $tokens[($i + 1)]['line'] !== $tokens[$i]['line']
            ) {
                $currColumn = 1;
            } else {
                $currColumn += strlen($tokens[$i]['content']);
            }
        }

    }//end _createColumnMap()


    /**
     * Creates a map for opening and closing of square brackets.
     *
     * Each bracket token (T_OPEN_SQUARE_BRACKET and T_CLOSE_SQUARE_BRACKET)
     * has a reference to their opening and closing bracket
     * (bracket_opener and bracket_closer).
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createBracketMap(&$tokens, $tokenizer, $eolChar)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START BRACKET MAP ***".PHP_EOL;
        }

        $squareOpeners = array();
        $curlyOpeners  = array();
        $numTokens     = count($tokens);

        for ($i = 0; $i < $numTokens; $i++) {
            switch ($tokens[$i]['code']) {
            case T_OPEN_SQUARE_BRACKET:
                $squareOpeners[] = $i;

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", count($squareOpeners));
                    echo str_repeat("\t", count($curlyOpeners));
                    echo "=> Found square bracket opener at $i".PHP_EOL;
                }

                break;
            case T_OPEN_CURLY_BRACKET:
                if (isset($tokens[$i]['scope_closer']) === false) {
                    $curlyOpeners[] = $i;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "=> Found curly bracket opener at $i".PHP_EOL;
                    }
                }
                break;
            case T_CLOSE_SQUARE_BRACKET:
                if (empty($squareOpeners) === false) {
                    $opener                            = array_pop($squareOpeners);
                    $tokens[$i]['bracket_opener']      = $opener;
                    $tokens[$i]['bracket_closer']      = $i;
                    $tokens[$opener]['bracket_opener'] = $opener;
                    $tokens[$opener]['bracket_closer'] = $i;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "\t=> Found square bracket closer at $i for $opener".PHP_EOL;
                    }
                }
                break;
            case T_CLOSE_CURLY_BRACKET:
                if (empty($curlyOpeners) === false
                    && isset($tokens[$i]['scope_opener']) === false
                ) {
                    $opener                            = array_pop($curlyOpeners);
                    $tokens[$i]['bracket_opener']      = $opener;
                    $tokens[$i]['bracket_closer']      = $i;
                    $tokens[$opener]['bracket_opener'] = $opener;
                    $tokens[$opener]['bracket_closer'] = $i;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "\t=> Found curly bracket closer at $i for $opener".PHP_EOL;
                    }
                }
                break;
            default:
                continue;
            }//end switch
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END BRACKET MAP ***".PHP_EOL;
        }

    }//end _createBracketMap()


    /**
     * Creates a map for opening and closing of parenthesis.
     *
     * Each parenthesis token (T_OPEN_PARENTHESIS and T_CLOSE_PARENTHESIS) has a
     * reference to their opening and closing parenthesis (parenthesis_opener
     * and parenthesis_closer).
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createParenthesisMap(&$tokens, $tokenizer, $eolChar)
    {
        $openers   = array();
        $numTokens = count($tokens);
        $openOwner = null;

        for ($i = 0; $i < $numTokens; $i++) {
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$parenthesisOpeners) === true) {
                $tokens[$i]['parenthesis_opener'] = null;
                $tokens[$i]['parenthesis_closer'] = null;
                $tokens[$i]['parenthesis_owner']  = $i;
                $openOwner                        = $i;
            } else if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $openers[]                        = $i;
                $tokens[$i]['parenthesis_opener'] = $i;
                if ($openOwner !== null) {
                    $tokens[$openOwner]['parenthesis_opener'] = $i;
                    $tokens[$i]['parenthesis_owner']          = $openOwner;
                    $openOwner                                = null;
                }
            } else if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                // Did we set an owner for this set of parenthesis?
                $numOpeners = count($openers);
                if ($numOpeners !== 0) {
                    $opener = array_pop($openers);
                    if (isset($tokens[$opener]['parenthesis_owner']) === true) {
                        $owner = $tokens[$opener]['parenthesis_owner'];

                        $tokens[$owner]['parenthesis_closer'] = $i;
                        $tokens[$i]['parenthesis_owner']      = $owner;
                    }

                    $tokens[$i]['parenthesis_opener']      = $opener;
                    $tokens[$i]['parenthesis_closer']      = $i;
                    $tokens[$opener]['parenthesis_closer'] = $i;
                }
            }//end if
        }//end for

    }//end _createParenthesisMap()


    /**
     * Creates a map for the parenthesis tokens that surround other tokens.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createParenthesisNestingMap(
        &$tokens,
        $tokenizer,
        $eolChar
    ) {
        $numTokens = count($tokens);
        $map       = array();
        for ($i = 0; $i < $numTokens; $i++) {
            if (isset($tokens[$i]['parenthesis_opener']) === true
                && $i === $tokens[$i]['parenthesis_opener']
            ) {
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }

                if (isset($tokens[$i]['parenthesis_closer']) === true) {
                    $map[$tokens[$i]['parenthesis_opener']]
                        = $tokens[$i]['parenthesis_closer'];
                }
            } else if (isset($tokens[$i]['parenthesis_closer']) === true
                && $i === $tokens[$i]['parenthesis_closer']
            ) {
                array_pop($map);
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }
            } else {
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }
            }//end if
        }//end for

    }//end _createParenthesisNestingMap()


    /**
     * Creates a scope map of tokens that open scopes.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     * @see _recurseScopeMap()
     */
    private static function _createScopeMap(&$tokens, $tokenizer, $eolChar)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START SCOPE MAP ***".PHP_EOL;
        }

        $numTokens = count($tokens);
        for ($i = 0; $i < $numTokens; $i++) {
            // Check to see if the current token starts a new scope.
            if (isset($tokenizer->scopeOpeners[$tokens[$i]['code']]) === true) {
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type    = $tokens[$i]['type'];
                    $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                    echo "\tStart scope map at $i: $type => $content".PHP_EOL;
                }

                $i = self::_recurseScopeMap(
                    $tokens,
                    $numTokens,
                    $tokenizer,
                    $eolChar,
                    $i
                );
            }
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END SCOPE MAP ***".PHP_EOL;
        }

    }//end _createScopeMap()


    /**
     * Recurses though the scope openers to build a scope map.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param int    $numTokens The size of the tokens array.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     * @param int    $stackPtr  The position in the stack of the token that
     *                          opened the scope (eg. an IF token or FOR token).
     * @param int    $depth     How many scope levels down we are.
     * @param int    &$ignore   How many curly braces we are ignoring.
     *
     * @return int The position in the stack that closed the scope.
     */
    private static function _recurseScopeMap(
        &$tokens,
        $numTokens,
        $tokenizer,
        $eolChar,
        $stackPtr,
        $depth=1,
        &$ignore=0
    ) {
        $opener    = null;
        $currType  = $tokens[$stackPtr]['code'];
        $startLine = $tokens[$stackPtr]['line'];

        // We will need this to restore the value if we end up
        // returning a token ID that causes our calling function to go back
        // over already ignored braces.
        $originalIgnore = $ignore;

        // If the start token for this scope opener is the same as
        // the scope token, we have already found our opener.
        if (in_array($currType, $tokenizer->scopeOpeners[$currType]['start']) === true) {
            $opener = $stackPtr;
        }

        for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
            $tokenType = $tokens[$i]['code'];

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $tokens[$i]['type'];
                $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                echo str_repeat("\t", $depth);
                echo "Process token $i [";
                if ($opener !== null) {
                    echo "opener:$opener;";
                }

                if ($ignore > 0) {
                    echo "ignore=$ignore;";
                }

                echo "]: $type => $content".PHP_EOL;
            }

            // Very special case for IF statements in PHP that can be defined without
            // scope tokens. If an IF statement below this one has an opener but no
            // keyword, the opener will be incorrectly assigned to this IF statement.
            // E.g., if (1) 1; 1 ? (1 ? 1 : 1) : 1;
            if ($currType === T_IF && $opener === null && $tokens[$i]['code'] === T_SEMICOLON) {
                $opener = $tokens[$stackPtr]['parenthesis_closer'] + 1;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "=> Found semicolon before scope opener, mark scope to be $opener-$i".PHP_EOL;
                }
                foreach (array($stackPtr, $opener, $i) as $token) {
                    $tokens[$token]['scope_condition'] = $stackPtr;
                    $tokens[$token]['scope_opener']    = $opener;
                    $tokens[$token]['scope_closer']    = $i;
                }

                return $i;
            }

            // Is this an opening condition ?
            if (isset($tokenizer->scopeOpeners[$tokenType]) === true) {
                if ($opener === null) {
                    // Found another opening condition but still haven't
                    // found our opener, so we are never going to find one.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Couldn't find scope opener for $stackPtr ($type), bailing".PHP_EOL;
                    }

                    return $stackPtr;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* token is an opening condition *'.PHP_EOL;
                }

                $isShared
                    = ($tokenizer->scopeOpeners[$tokenType]['shared'] === true);

                if (isset($tokens[$i]['scope_condition']) === true) {
                    // We've been here before.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* already processed, skipping *'.PHP_EOL;
                    }

                    if ($isShared === false
                        && isset($tokens[$i]['scope_closer']) === true
                    ) {
                        $i = $tokens[$i]['scope_closer'];
                    }

                    continue;
                } else if ($currType === $tokenType
                    && $isShared === false
                    && $opener === null
                ) {
                    // We haven't yet found our opener, but we have found another
                    // scope opener which is the same type as us, and we don't
                    // share openers, so we will never find one.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* it was another token\'s opener, bailing *'.PHP_EOL;
                    }

                    return $stackPtr;
                } else {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* searching for opener *'.PHP_EOL;
                    }

                    if (in_array(T_CLOSE_CURLY_BRACKET, $tokenizer->scopeOpeners[$tokenType]['end']) === true) {
                        $oldIgnore = $ignore;
                        $ignore    = 0;
                    }

                    $i = self::_recurseScopeMap(
                        $tokens,
                        $numTokens,
                        $tokenizer,
                        $eolChar,
                        $i,
                        ($depth + 1),
                        $ignore
                    );

                    if (in_array(T_CLOSE_CURLY_BRACKET, $tokenizer->scopeOpeners[$tokenType]['end']) === true) {
                        $ignore = $oldIgnore;
                    }
                }//end if
            }//end if

            if (in_array($tokenType, $tokenizer->scopeOpeners[$currType]['start']) === true
                && $opener === null
            ) {
                if ($tokenType === T_OPEN_CURLY_BRACKET) {
                    // Make sure this is actually an opener and not a
                    // string offset (e.g., $var{0}).
                    for ($x = ($i - 1); $x > 0; $x--) {
                        if (in_array($tokens[$x]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                            continue;
                        } else {
                            // If the first non-whitespace/comment token is a
                            // variable or object operator then this is an opener
                            // for a string offset and not a scope.
                            if ($tokens[$x]['code'] === T_VARIABLE
                                || $tokens[$x]['code'] === T_OBJECT_OPERATOR
                            ) {
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo str_repeat("\t", $depth);
                                    echo '* ignoring curly brace *'.PHP_EOL;
                                }

                                $ignore++;
                            }//end if

                            break;
                        }//end if
                    }//end for
                }//end if

                if ($ignore === 0) {
                    // We found the opening scope token for $currType.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope opener for $stackPtr ($type)".PHP_EOL;
                    }

                    $opener = $i;
                }
            } else if (in_array($tokenType, $tokenizer->scopeOpeners[$currType]['end']) === true
                && $opener !== null
            ) {
                if ($ignore > 0 && $tokenType === T_CLOSE_CURLY_BRACKET) {
                    // The last opening bracket must have been for a string
                    // offset or alike, so let's ignore it.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* finished ignoring curly brace *'.PHP_EOL;
                    }

                    $ignore--;
                } else {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope closer for $stackPtr ($type)".PHP_EOL;
                    }

                    foreach (array($stackPtr, $opener, $i) as $token) {
                        $tokens[$token]['scope_condition'] = $stackPtr;
                        $tokens[$token]['scope_opener']    = $opener;
                        $tokens[$token]['scope_closer']    = $i;
                    }

                    if ($tokenizer->scopeOpeners[$tokens[$stackPtr]['code']]['shared'] === true) {
                        // As we are going back to where we started originally, restore
                        // the ignore value back to its original value.
                        $ignore = $originalIgnore;
                        return $opener;
                    } else {
                        return $i;
                    }
                }//end if
            } else if ($tokenType === T_OPEN_PARENTHESIS) {
                if (isset($tokens[$i]['parenthesis_owner']) === true) {
                    $owner = $tokens[$i]['parenthesis_owner'];
                    if (in_array($tokens[$owner]['code'], PHP_CodeSniffer_Tokens::$scopeOpeners) === true
                        && isset($tokens[$i]['parenthesis_closer']) === true
                    ) {
                        // If we get into here, then we opened a parenthesis for
                        // a scope (eg. an if or else if). We can just skip to
                        // the closing parenthesis.
                        $i = $tokens[$i]['parenthesis_closer'];

                        // Update the start of the line so that when we check to see
                        // if the closing parenthesis is more than 3 lines away from
                        // the statement, we check from the closing parenthesis.
                        $startLine
                            = $tokens[$tokens[$i]['parenthesis_closer']]['line'];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* skipping parenthesis *'.PHP_EOL;
                        }
                    }
                }//end if
            } else if ($tokenType === T_OPEN_CURLY_BRACKET && $opener !== null) {
                // We opened something that we don't have a scope opener for.
                // Examples of this are curly brackets for string offsets etc.
                // We want to ignore this so that we don't have an invalid scope
                // map.
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* ignoring curly brace *'.PHP_EOL;
                }

                $ignore++;
            } else if ($opener === null
                && isset($tokenizer->scopeOpeners[$currType]) === true
            ) {
                // If we still haven't found the opener after 3 lines,
                // we're not going to find it, unless we know it requires
                // an opener (in which case we better keep looking) or the last
                // token was empty (in which case we'll just confirm there is
                // more code in this file and not just a big comment).
                if ($tokens[$i]['line'] >= ($startLine + 3)
                    && in_array($tokens[($i - 1)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false
                ) {
                    if ($tokenizer->scopeOpeners[$currType]['strict'] === true) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type  = $tokens[$stackPtr]['type'];
                            $lines = ($tokens[$i]['line'] - $startLine);
                            echo str_repeat("\t", $depth);
                            echo "=> Still looking for $stackPtr ($type) scope opener after $lines lines".PHP_EOL;
                        }
                    } else {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Couldn't find scope opener for $stackPtr ($type), bailing".PHP_EOL;
                        }

                        return $stackPtr;
                    }
                }
            } else if ($opener !== null
                && $tokenType !== T_BREAK
                && in_array($tokenType, $tokenizer->endScopeTokens) === true
            ) {
                if (isset($tokens[$i]['scope_condition']) === false) {
                    if ($ignore > 0) {
                        // We found the end token for the opener we were ignoring.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* finished ignoring curly brace *'.PHP_EOL;
                        }

                        $ignore--;
                    } else {
                        // We found a token that closes the scope but it doesn't
                        // have a condition, so it belongs to another token and
                        // our token doesn't have a closer, so pretend this is
                        // the closer.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Found (unexpected) scope closer for $stackPtr ($type)".PHP_EOL;
                        }

                        foreach (array($stackPtr, $opener) as $token) {
                            $tokens[$token]['scope_condition'] = $stackPtr;
                            $tokens[$token]['scope_opener']    = $opener;
                            $tokens[$token]['scope_closer']    = $i;
                        }

                        return ($i - 1);
                    }//end if
                }//end if
            }//end if
        }//end for

        return $stackPtr;

    }//end _recurseScopeMap()


    /**
     * Constructs the level map.
     *
     * The level map adds a 'level' indice to each token which indicates the
     * depth that a token within a set of scope blocks. It also adds a
     * 'condition' indice which is an array of the scope conditions that opened
     * each of the scopes - position 0 being the first scope opener.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createLevelMap(&$tokens, $tokenizer, $eolChar)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START LEVEL MAP ***".PHP_EOL;
        }

        $numTokens  = count($tokens);
        $level      = 0;
        $conditions = array();
        $lastOpener = null;
        $openers    = array();

        for ($i = 0; $i < $numTokens; $i++) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $tokens[$i]['type'];
                $line    = $tokens[$i]['line'];
                $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                echo str_repeat("\t", ($level + 1));
                echo "Process token $i on line $line [lvl:$level;";
                if (empty($conditions) !== true) {
                    $condString = 'conds;';
                    foreach ($conditions as $condition) {
                        $condString .= token_name($condition).',';
                    }

                    echo rtrim($condString, ',').';';
                }

                echo "]: $type => $content".PHP_EOL;
            }

            $tokens[$i]['level']      = $level;
            $tokens[$i]['conditions'] = $conditions;

            if (isset($tokens[$i]['scope_condition']) === true) {
                // Check to see if this token opened the scope.
                if ($tokens[$i]['scope_opener'] === $i) {
                    $stackPtr = $tokens[$i]['scope_condition'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "=> Found scope opener for $stackPtr ($type)".PHP_EOL;
                    }

                    $stackPtr = $tokens[$i]['scope_condition'];

                    // If we find a scope opener that has a shared closer,
                    // then we need to go back over the condition map that we
                    // just created and fix ourselves as we just added some
                    // conditions where there was none. This happens for T_CASE
                    // statements that are using the same break statement.
                    if ($lastOpener !== null && $tokens[$lastOpener]['scope_closer'] === $tokens[$i]['scope_closer']) {
                        // This opener shares its closer with the previous opener,
                        // but we still need to check if the two openers share their
                        // closer with each other directly (like CASE and DEFAULT)
                        // or if they are just sharing because one doesn't have a
                        // closer (like CASE with no BREAK using a SWITCHes closer).
                        $thisType = $tokens[$tokens[$i]['scope_condition']]['code'];
                        $opener   = $tokens[$lastOpener]['scope_condition'];

                        $isShared = in_array(
                            $tokens[$opener]['code'],
                            $tokenizer->scopeOpeners[$thisType]['with']
                        );

                        $sameEnd = ($tokenizer->scopeOpeners[$thisType]['end'][0] === $tokenizer->scopeOpeners[$tokens[$opener]['code']]['end'][0]);
                        if ($isShared === true && $sameEnd === true) {
                            $badToken = $opener;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* shared closer, cleaning up $badToken ($type) *".PHP_EOL;
                            }

                            for ($x = $tokens[$i]['scope_condition']; $x <= $i; $x++) {
                                $oldConditions = $tokens[$x]['conditions'];
                                $oldLevel      = $tokens[$x]['level'];
                                $tokens[$x]['level']--;
                                unset($tokens[$x]['conditions'][$badToken]);
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    $type     = $tokens[$x]['type'];
                                    $oldConds = '';
                                    foreach ($oldConditions as $condition) {
                                        $oldConds .= token_name($condition).',';
                                    }

                                    $oldConds = rtrim($oldConds, ',');

                                    $newConds = '';
                                    foreach ($tokens[$x]['conditions'] as $condition) {
                                        $newConds .= token_name($condition).',';
                                    }

                                    $newConds = rtrim($newConds, ',');

                                    $newLevel = $tokens[$x]['level'];
                                    echo str_repeat("\t", ($level + 1));
                                    echo "* cleaned $x ($type) *".PHP_EOL;
                                    echo str_repeat("\t", ($level + 2));
                                    echo "=> level changed from $oldLevel to $newLevel".PHP_EOL;
                                    echo str_repeat("\t", ($level + 2));
                                    echo "=> conditions changed from $oldConds to $newConds".PHP_EOL;
                                }//end if
                            }//end for

                            unset($conditions[$badToken]);
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* token $badToken ($type) removed from conditions array *".PHP_EOL;
                            }

                            unset ($openers[$lastOpener]);

                            $level--;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", ($level + 2));
                                echo '* level decreased *'.PHP_EOL;
                            }
                        }//end if
                    }//end if

                    $level++;
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", ($level + 1));
                        echo '* level increased *'.PHP_EOL;
                    }

                    $conditions[$stackPtr] = $tokens[$stackPtr]['code'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "* token $stackPtr ($type) added to conditions array *".PHP_EOL;
                    }

                    $lastOpener = $tokens[$i]['scope_opener'];
                    if ($lastOpener !== null) {
                        $openers[$lastOpener] = $lastOpener;
                    }
                } else if ($tokens[$i]['scope_closer'] === $i) {
                    foreach (array_reverse($openers) as $opener) {
                        if ($tokens[$opener]['scope_closer'] === $i) {
                            $oldOpener = array_pop($openers);
                            if (empty($openers) === false) {
                                $lastOpener           = array_pop($openers);
                                $openers[$lastOpener] = $lastOpener;
                            } else {
                                $lastOpener = null;
                            }

                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $tokens[$oldOpener]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "=> Found scope closer for $oldOpener ($type)".PHP_EOL;
                            }

                            $oldCondition = array_pop($conditions);
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", ($level + 1));
                                echo '* token '.token_name($oldCondition).' removed from conditions array *'.PHP_EOL;
                            }

                            // Make sure this closer actually belongs to us.
                            // Either the condition also has to think this is the
                            // closer, or it has to allow sharing with us.
                            $condition
                                = $tokens[$tokens[$i]['scope_condition']]['code'];
                            if ($condition !== $oldCondition) {
                                if (in_array($condition, $tokenizer->scopeOpeners[$oldCondition]['with']) === false) {
                                    $badToken = $tokens[$oldOpener]['scope_condition'];

                                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                        $type = token_name($oldCondition);
                                        echo str_repeat("\t", ($level + 1));
                                        echo "* scope closer was bad, cleaning up $badToken ($type) *".PHP_EOL;
                                    }

                                    for ($x = ($oldOpener + 1); $x <= $i; $x++) {
                                        $oldConditions = $tokens[$x]['conditions'];
                                        $oldLevel      = $tokens[$x]['level'];
                                        $tokens[$x]['level']--;
                                        unset($tokens[$x]['conditions'][$badToken]);
                                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                            $type     = $tokens[$x]['type'];
                                            $oldConds = '';
                                            foreach ($oldConditions as $condition) {
                                                $oldConds .= token_name($condition).',';
                                            }

                                            $oldConds = rtrim($oldConds, ',');

                                            $newConds = '';
                                            foreach ($tokens[$x]['conditions'] as $condition) {
                                                $newConds .= token_name($condition).',';
                                            }

                                            $newConds = rtrim($newConds, ',');

                                            $newLevel = $tokens[$x]['level'];
                                            echo str_repeat("\t", ($level + 1));
                                            echo "* cleaned $x ($type) *".PHP_EOL;
                                            echo str_repeat("\t", ($level + 2));
                                            echo "=> level changed from $oldLevel to $newLevel".PHP_EOL;
                                            echo str_repeat("\t", ($level + 2));
                                            echo "=> conditions changed from $oldConds to $newConds".PHP_EOL;
                                        }//end if
                                    }//end for
                                }//end if
                            }//end if

                            $level--;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", ($level + 2));
                                echo '* level decreased *'.PHP_EOL;
                            }

                            $tokens[$i]['level']      = $level;
                            $tokens[$i]['conditions'] = $conditions;
                        }//end if
                    }//end foreach
                }//end if
            }//end if
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END LEVEL MAP ***".PHP_EOL;
        }

    }//end _createLevelMap()


    /**
     * Returns the declaration names for T_CLASS, T_INTERFACE and T_FUNCTION tokens.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface or function.
     *
     * @return string|null The name of the class, interface or function.
     *                     or NULL if the function is a closure.
     * @throws PHP_CodeSniffer_Exception If the specified token is not of type
     *                                   T_FUNCTION, T_CLASS or T_INTERFACE.
     */
    public function getDeclarationName($stackPtr)
    {
        $tokenCode = $this->_tokens[$stackPtr]['code'];
        if ($tokenCode !== T_FUNCTION
            && $tokenCode !== T_CLASS
            && $tokenCode !== T_INTERFACE
            && $tokenCode !== T_TRAIT
        ) {
            throw new PHP_CodeSniffer_Exception('Token type is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');
        }

        if ($tokenCode === T_FUNCTION
            && $this->isAnonymousFunction($stackPtr) === true
        ) {
            return null;
        }

        $token = $this->findNext(T_STRING, $stackPtr);
        return $this->_tokens[$token]['content'];

    }//end getDeclarationName()


    /**
     * Check if the token at the specified position is a anonymous function.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface or function.
     *
     * @return boolean
     * @throws PHP_CodeSniffer_Exception If the specified token is not of type
     *                                   T_FUNCTION
     */
    public function isAnonymousFunction($stackPtr)
    {
        $tokenCode = $this->_tokens[$stackPtr]['code'];
        if ($tokenCode !== T_FUNCTION) {
            throw new PHP_CodeSniffer_Exception('Token type is not T_FUNCTION');
        }

        if (isset($this->_tokens[$stackPtr]['parenthesis_opener']) === false) {
            // Something is not right with this function.
            return false;
        }

        $name = $this->findNext(T_STRING, ($stackPtr + 1));
        if ($name === false) {
            // No name found.
            return true;
        }

        $open = $this->_tokens[$stackPtr]['parenthesis_opener'];
        if ($name > $open) {
            return true;
        }

        return false;

    }//end isAnonymousFunction()


    /**
     * Returns the method parameters for the specified T_FUNCTION token.
     *
     * Each parameter is in the following format:
     *
     * <code>
     *   0 => array(
     *         'name'              => '$var',  // The variable name.
     *         'pass_by_reference' => false,   // Passed by reference.
     *         'type_hint'         => string,  // Type hint for array or custom type
     *        )
     * </code>
     *
     * Parameters with default values have and additional array indice of
     * 'default' with the value of the default as a string.
     *
     * @param int $stackPtr The position in the stack of the T_FUNCTION token
     *                      to acquire the parameters for.
     *
     * @return array()
     * @throws PHP_CodeSniffer_Exception If the specified $stackPtr is not of
     *                                   type T_FUNCTION.
     */
    public function getMethodParameters($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_FUNCTION) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_FUNCTION');
        }

        $opener = $this->_tokens[$stackPtr]['parenthesis_opener'];
        $closer = $this->_tokens[$stackPtr]['parenthesis_closer'];

        $vars            = array();
        $currVar         = null;
        $defaultStart    = null;
        $paramCount      = 0;
        $passByReference = false;
        $typeHint        = '';

        for ($i = ($opener + 1); $i <= $closer; $i++) {
            // Check to see if this token has a parenthesis opener. If it does
            // its likely to be an array, which might have arguments in it, which
            // we cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($this->_tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $this->_tokens[$i]['parenthesis_closer']) {
                    $i = ($this->_tokens[$i]['parenthesis_closer'] + 1);
                }
            }

            switch ($this->_tokens[$i]['code']) {
            case T_BITWISE_AND:
                $passByReference = true;
                break;
            case T_VARIABLE:
                $currVar = $i;
                break;
            case T_ARRAY_HINT:
                $typeHint = $this->_tokens[$i]['content'];
                break;
            case T_STRING:
                // This is a string, so it may be a type hint, but it could
                // also be a constant used as a default value.
                $prevComma = $this->findPrevious(T_COMMA, $i, $opener);
                if ($prevComma !== false) {
                    $nextEquals = $this->findNext(T_EQUAL, $prevComma, $i);
                    if ($nextEquals !== false) {
                        break;
                    }
                }

                if ($defaultStart === null) {
                    $typeHint .= $this->_tokens[$i]['content'];
                }

                break;
            case T_NS_SEPARATOR:
                // Part of a type hint or default value.
                if ($defaultStart === null) {
                    $typeHint .= $this->_tokens[$i]['content'];
                }

                break;
            case T_CLOSE_PARENTHESIS:
            case T_COMMA:
                // If it's null, then there must be no parameters for this
                // method.
                if ($currVar === null) {
                    continue;
                }

                $vars[$paramCount]         = array();
                $vars[$paramCount]['name'] = $this->_tokens[$currVar]['content'];

                if ($defaultStart !== null) {
                    $vars[$paramCount]['default']
                        = $this->getTokensAsString(
                            $defaultStart,
                            ($i - $defaultStart)
                        );
                }

                $vars[$paramCount]['pass_by_reference'] = $passByReference;
                $vars[$paramCount]['type_hint']         = $typeHint;

                // Reset the vars, as we are about to process the next parameter.
                $defaultStart    = null;
                $passByReference = false;
                $typeHint        = '';

                $paramCount++;
                break;
            case T_EQUAL:
                $defaultStart = ($i + 1);
                break;
            }//end switch
        }//end for

        return $vars;

    }//end getMethodParameters()


    /**
     * Returns the visibility and implementation properties of a method.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'scope'           => 'public', // public private or protected
     *    'scope_specified' => true,     // true is scope keyword was found.
     *    'is_abstract'     => false,    // true if the abstract keyword was found.
     *    'is_final'        => false,    // true if the final keyword was found.
     *    'is_static'       => false,    // true if the static keyword was found.
     *    'is_closure'      => false,    // true if no name is found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_FUNCTION token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_FUNCTION token.
     */
    public function getMethodProperties($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_FUNCTION) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_FUNCTION');
        }

        $valid = array(
                  T_PUBLIC,
                  T_PRIVATE,
                  T_PROTECTED,
                  T_STATIC,
                  T_FINAL,
                  T_ABSTRACT,
                  T_WHITESPACE,
                  T_COMMENT,
                  T_DOC_COMMENT,
                 );

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;
        $isClosure      = $this->isAnonymousFunction($stackPtr);

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (in_array($this->_tokens[$i]['code'], $valid) === false) {
                break;
            }

            switch ($this->_tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_ABSTRACT:
                $isAbstract = true;
                break;
            case T_FINAL:
                $isFinal = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }//end switch
        }//end for

        return array(
                'scope'           => $scope,
                'scope_specified' => $scopeSpecified,
                'is_abstract'     => $isAbstract,
                'is_final'        => $isFinal,
                'is_static'       => $isStatic,
                'is_closure'      => $isClosure,
               );

    }//end getMethodProperties()


    /**
     * Returns the visibility and implementation properties of the class member
     * variable found at the specified position in the stack.
     *
     * The format of the array is:
     *
     * <code>
     *   array(
     *    'scope'       => 'public', // public private or protected
     *    'is_static'   => false,    // true if the static keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_VARIABLE token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_VARIABLE token, or if the position is not
     *                                   a class member variable.
     */
    public function getMemberProperties($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_VARIABLE) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_VARIABLE');
        }

        $conditions = array_keys($this->_tokens[$stackPtr]['conditions']);
        $ptr        = array_pop($conditions);
        if (isset($this->_tokens[$ptr]) === false
            || $this->_tokens[$ptr]['code'] !== T_CLASS
        ) {
            if (isset($this->_tokens[$ptr]) === true
                && $this->_tokens[$ptr]['code'] === T_INTERFACE
            ) {
                // T_VARIABLEs in interfaces can actually be method arguments
                // but they wont be seen as being inside the method because there
                // are no scope openers and closers for abstract methods. If it is in
                // parentheses, we can be pretty sure it is a method argument.
                if (isset($this->_tokens[$stackPtr]['nested_parenthesis']) === false
                    || empty($this->_tokens[$stackPtr]['nested_parenthesis']) === true
                ) {
                    $error = 'Possible parse error: interfaces may not include member vars';
                    $this->addWarning($error, $stackPtr, 'Internal.ParseError.InterfaceHasMemberVar');
                    return array();
                }
            } else {
                throw new PHP_CodeSniffer_Exception('$stackPtr is not a class member var');
            }
        }

        $valid = array(
                  T_PUBLIC,
                  T_PRIVATE,
                  T_PROTECTED,
                  T_STATIC,
                  T_WHITESPACE,
                  T_COMMENT,
                  T_DOC_COMMENT,
                  T_VARIABLE,
                  T_COMMA,
                 );

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (in_array($this->_tokens[$i]['code'], $valid) === false) {
                break;
            }

            switch ($this->_tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }
        }//end for

        return array(
                'scope'           => $scope,
                'scope_specified' => $scopeSpecified,
                'is_static'       => $isStatic,
               );

    }//end getMemberProperties()


    /**
     * Returns the visibility and implementation properties of a class.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'is_abstract' => false, // true if the abstract keyword was found.
     *    'is_final'    => false, // true if the final keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_CLASS token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_CLASS token.
     */
    public function getClassProperties($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_CLASS) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_CLASS');
        }

        $valid = array(
                  T_FINAL,
                  T_ABSTRACT,
                  T_WHITESPACE,
                  T_COMMENT,
                  T_DOC_COMMENT,
                 );

        $isAbstract = false;
        $isFinal    = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (in_array($this->_tokens[$i]['code'], $valid) === false) {
                break;
            }

            switch ($this->_tokens[$i]['code']) {
            case T_ABSTRACT:
                $isAbstract = true;
                break;

            case T_FINAL:
                $isFinal = true;
                break;
            }
        }//end for

        return array(
                'is_abstract' => $isAbstract,
                'is_final'    => $isFinal,
               );

    }//end getClassProperties()


    /**
     * Determine if the passed token is a reference operator.
     *
     * Returns true if the specified token position represents a reference.
     * Returns false if the token represents a bitwise operator.
     *
     * @param int $stackPtr The position of the T_BITWISE_AND token.
     *
     * @return boolean
     */
    public function isReference($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $this->findPrevious(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            ($stackPtr - 1),
            null,
            true
        );

        if ($this->_tokens[$tokenBefore]['code'] === T_FUNCTION) {
            // Function returns a reference.
            return true;
        }

        if ($this->_tokens[$tokenBefore]['code'] === T_DOUBLE_ARROW) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if ($this->_tokens[$tokenBefore]['code'] === T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (in_array($this->_tokens[$tokenBefore]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        if (isset($this->_tokens[$stackPtr]['nested_parenthesis']) === true) {
            $brackets    = $this->_tokens[$stackPtr]['nested_parenthesis'];
            $lastBracket = array_pop($brackets);
            if (isset($this->_tokens[$lastBracket]['parenthesis_owner']) === true) {
                $owner = $this->_tokens[$this->_tokens[$lastBracket]['parenthesis_owner']];
                if ($owner['code'] === T_FUNCTION
                    || $owner['code'] === T_CLOSURE
                    || $owner['code'] === T_ARRAY
                ) {
                    // Inside a function or array declaration, this is a reference.
                    return true;
                }
            } else {
                $prev = $this->findPrevious(
                    array(T_WHITESPACE),
                    ($this->_tokens[$lastBracket]['parenthesis_opener'] - 1),
                    null,
                    true
                );

                if ($prev !== false && $this->_tokens[$prev]['code'] === T_USE) {
                    return true;
                }
            }
        }

        $tokenAfter = $this->findNext(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            ($stackPtr + 1),
            null,
            true
        );

        if ($this->_tokens[$tokenAfter]['code'] === T_VARIABLE
            && ($this->_tokens[$tokenBefore]['code'] === T_OPEN_PARENTHESIS
            || $this->_tokens[$tokenBefore]['code'] === T_COMMA)
        ) {
            return true;
        }

        return false;

    }//end isReference()


    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified length.
     *
     * @param int $start  The position to start from in the token stack.
     * @param int $length The length of tokens to traverse from the start pos.
     *
     * @return string The token contents.
     */
    public function getTokensAsString($start, $length)
    {
        $str = '';
        $end = ($start + $length);
        if ($end > $this->numTokens) {
            $end = $this->numTokens;
        }

        for ($i = $start; $i < $end; $i++) {
            $str .= $this->_tokens[$i]['content'];
        }

        return $str;

    }//end getTokensAsString()


    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack.
     * @param int       $end     The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the start of the token stack.
     * @param bool      $exclude If true, find the next token that are NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be checked. IE. checking will stop
     *                           at the next semi-colon found.
     *
     * @return int | bool
     * @see findNext()
     */
    public function findPrevious(
        $types,
        $start,
        $end=null,
        $exclude=false,
        $value=null,
        $local=false
    ) {
        $types = (array) $types;

        if ($end === null) {
            $end = 0;
        }

        for ($i = $start; $i >= $end; $i--) {
            $found = (bool) $exclude;
            foreach ($types as $type) {
                if ($this->_tokens[$i]['code'] === $type) {
                    $found = !$exclude;
                    break;
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    return $i;
                }
            }

            if ($local === true && $this->_tokens[$i]['code'] === T_SEMICOLON) {
                break;
            }
        }//end for

        return false;

    }//end findPrevious()


    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack.
     * @param int       $end     The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the end of the token stack.
     * @param bool      $exclude If true, find the next token that is NOT of
     *                           a type specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be checked. i.e., checking will stop
     *                           at the next semi-colon found.
     *
     * @return int | bool
     * @see findPrevious()
     */
    public function findNext(
        $types,
        $start,
        $end=null,
        $exclude=false,
        $value=null,
        $local=false
    ) {
        $types = (array) $types;

        if ($end === null || $end > $this->numTokens) {
            $end = $this->numTokens;
        }

        for ($i = $start; $i < $end; $i++) {
            $found = (bool) $exclude;
            foreach ($types as $type) {
                if ($this->_tokens[$i]['code'] === $type) {
                    $found = !$exclude;
                    break;
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    return $i;
                }
            }

            if ($local === true && $this->_tokens[$i]['code'] === T_SEMICOLON) {
                break;
            }
        }//end for

        return false;

    }//end findNext()


    /**
     * Returns the position of the first token on a line, matching given type.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack. The first token matching on
     *                           this line before this token will be returned.
     * @param bool      $exclude If true, find the token that is NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     *
     * @return int | bool
     */
    public function findFirstOnLine($types, $start, $exclude=false, $value=null)
    {
        if (is_array($types) === false) {
            $types = array($types);
        }

        $foundToken = false;

        for ($i = $start; $i >= 0; $i--) {
            if ($this->_tokens[$i]['line'] < $this->_tokens[$start]['line']) {
                break;
            }

            $found = $exclude;
            foreach ($types as $type) {
                if ($exclude === false) {
                    if ($this->_tokens[$i]['code'] === $type) {
                        $found = true;
                        break;
                    }
                } else {
                    if ($this->_tokens[$i]['code'] === $type) {
                        $found = false;
                        break;
                    }
                }
            }

            if ($found === true) {
                if ($value === null) {
                    $foundToken = $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    $foundToken = $i;
                }
            }
        }//end for

        return $foundToken;

    }//end findFirstOnLine()


    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * @param int       $stackPtr The position of the token we are checking.
     * @param int|array $types    The type(s) of tokens to search for.
     *
     * @return boolean
     */
    public function hasCondition($stackPtr, $types)
    {
        // Check for the existence of the token.
        if (isset($this->_tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->_tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $types      = (array) $types;
        $conditions = $this->_tokens[$stackPtr]['conditions'];

        foreach ($types as $type) {
            if (in_array($type, $conditions) === true) {
                // We found a token with the required type.
                return true;
            }
        }

        return false;

    }//end hasCondition()


    /**
     * Return the position of the condition for the passed token.
     *
     * Returns FALSE if the token does not have the condition.
     *
     * @param int $stackPtr The position of the token we are checking.
     * @param int $type     The type of token to search for.
     *
     * @return int
     */
    public function getCondition($stackPtr, $type)
    {
        // Check for the existence of the token.
        if (isset($this->_tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->_tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $conditions = $this->_tokens[$stackPtr]['conditions'];
        foreach ($conditions as $token => $condition) {
            if ($condition === $type) {
                return $token;
            }
        }

        return false;

    }//end getCondition()


    /**
     * Returns the name of the class that the specified class extends.
     *
     * Returns FALSE on error or if there is no extended class name.
     *
     * @param int $stackPtr The stack position of the class.
     *
     * @return string
     */
    public function findExtendedClassName($stackPtr)
    {
        // Check for the existence of the token.
        if (isset($this->_tokens[$stackPtr]) === false) {
            return false;
        }

        if ($this->_tokens[$stackPtr]['code'] !== T_CLASS) {
            return false;
        }

        if (isset($this->_tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classCloserIndex = $this->_tokens[$stackPtr]['scope_closer'];
        $extendsIndex     = $this->findNext(T_EXTENDS, $stackPtr, $classCloserIndex);
        if (false === $extendsIndex) {
            return false;
        }

        $stringIndex = $this->findNext(T_STRING, $extendsIndex, $classCloserIndex);
        if (false === $stringIndex) {
            return false;
        }

        return $this->_tokens[$stringIndex]['content'];

    }//end findExtendedClassName()


}//end class

?>
