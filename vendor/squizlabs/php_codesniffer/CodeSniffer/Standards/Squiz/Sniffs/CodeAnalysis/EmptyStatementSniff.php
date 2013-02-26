<?php
/**
 * This sniff class detects empty statement.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_CodeAnalysis_EmptyStatementSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_CodeAnalysis_EmptyStatementSniff not found');
}

/**
 * This sniff class detects empty statement.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_CodeAnalysis_EmptyStatementSniff extends Generic_Sniffs_CodeAnalysis_EmptyStatementSniff
{

    /**
     * List of block tokens that this sniff covers.
     *
     * The key of this hash identifies the required token while the boolean
     * value says mark an error or mark a warning.
     *
     * @var array
     */
    protected $checkedTokens = array(
                                T_DO      => true,
                                T_ELSE    => true,
                                T_ELSEIF  => true,
                                T_FOR     => true,
                                T_FOREACH => true,
                                T_IF      => true,
                                T_SWITCH  => true,
                                T_WHILE   => true,
                               );

}//end class

?>
