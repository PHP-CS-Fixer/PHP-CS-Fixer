<?php
/**
 * Parses and verifies the doc comments for functions.
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

if (class_exists('Squiz_Sniffs_Commenting_FunctionCommentSniff', true) === false) {
    $error = 'Class Squiz_Sniffs_Commenting_FunctionCommentSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses and verifies the doc comments for functions.
 *
 * Same as the Squiz standard, but adds support for API tags.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class MySource_Sniffs_Commenting_FunctionCommentSniff extends Squiz_Sniffs_Commenting_FunctionCommentSniff
{


    /**
     * Process a list of unknown tags.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processUnknownTags($commentStart, $commentEnd)
    {
        $unknownTags = $this->commentParser->getUnknown();
        $words       = $this->commentParser->getWords();
        $hasApiTag   = false;
        $apiLength   = 3;
        foreach ($unknownTags as $errorTag) {
            $pos = $errorTag['pos'];
            if ($errorTag['tag'] === 'api') {
                if ($hasApiTag === true) {
                    // We've come across an API tag already, which means
                    // we were not the first tag in the API list.
                    $error = 'The @api tag must come first in the @api tag list in a function comment';
                    $this->currentFile->addError($error, ($commentStart + $errorTag['line']), 'ApiNotFirst');
                }

                $hasApiTag = true;

                // There needs to be a blank line before the @api tag.
                // So expect a single space before the tag, then 2 newlines before
                // that, then some content.
                if (trim($words[($pos - 2)]) !== ''
                    || strpos($words[($pos - 2)], $this->currentFile->eolChar) === false
                    || strpos($words[($pos - 3)], $this->currentFile->eolChar) === false
                    || trim($words[($pos - 4)]) === ''
                ) {
                    $error = 'There must be one blank line before the @api tag in a function comment';
                    $this->currentFile->addError($error, ($commentStart + $errorTag['line']), 'ApiSpacing');
                }
            } else if (substr($errorTag['tag'], 0, 4) === 'api-') {
                $hasApiTag = true;

                $tagLength = strlen($errorTag['tag']);
                if ($tagLength > $apiLength) {
                    $apiLength = $tagLength;
                }

                if (trim($words[($pos - 2)]) !== ''
                    || strpos($words[($pos - 2)], $this->currentFile->eolChar) === false
                    || trim($words[($pos - 3)]) === ''
                ) {
                    $error = 'There must be no blank line before the @%s tag in a function comment';
                    $data  = array($errorTag['tag']);
                    $this->currentFile->addError($error, ($commentStart + $errorTag['line']), 'ApiTagSpacing', $data);
                }
            } else {
                $error = '@%s tag is not allowed in function comment';
                $data  = array($errorTag['tag']);
                $this->currentFile->addWarning($error, ($commentStart + $errorTag['line']), 'TagNotAllowed', $data);
            }//end if
        }//end foreach

        if ($hasApiTag === true) {
            // API tags must be the last tags in a function comment.
            $order   = $this->commentParser->getTagOrders();
            $lastTag = array_pop($order);
            if ($lastTag !== 'api'
                && substr($lastTag, 0, 4) !== 'api-'
            ) {
                $error = 'The @api tags must be the last tags in a function comment';
                $this->currentFile->addError($error, $commentEnd, 'ApiNotLast');
            }

            // Check API tag indenting.
            foreach ($unknownTags as $errorTag) {
                if ($errorTag['tag'] === 'api'
                    || substr($errorTag['tag'], 0, 4) === 'api-'
                ) {
                    $expected = ($apiLength - strlen($errorTag['tag']) + 1);
                    $found    = strlen($words[($errorTag['pos'] + 1)]);
                    if ($found !== $expected) {
                        $error = '@%s tag indented incorrectly; expected %s spaces but found %s';
                        $data  = array(
                                  $errorTag['tag'],
                                  $expected,
                                  $found,
                                 );
                        $this->currentFile->addError($error, ($commentStart + $errorTag['line']), 'ApiTagIndent', $data);
                    }
                }
            }
        }//end if

    }//end processUnknownTags()


}//end class

?>
