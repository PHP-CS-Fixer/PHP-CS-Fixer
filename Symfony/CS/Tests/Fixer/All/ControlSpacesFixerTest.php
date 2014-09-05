<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\All;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class ControlSpacesFixerTest extends AbstractFixerTestBase
{
    public function testFixControlsWithSuffixBrace()
    {
        $try = 'try{';
        $tryFixed = 'try {';

        $this->makeTest($tryFixed, $try);
    }

    public function testFixControlsWithPrefixBraceAndParentheses()
    {
        $while = 'do { ... }while($test);';
        $whileFixed = 'do { ... } while ($test);';

        $this->makeTest($whileFixed, $while);
    }

    public function testFixControlClosingParenthesesKeepsIndentation()
    {
        $if = 'if(true === true
            && true === true
        )    {';

        $ifFixed = 'if (true === true
            && true === true
        ) {';

        $this->makeTest($ifFixed, $if);
    }

    /**
     * @dataProvider testFixControlsWithParenthesesAndSuffixBraceProvider
     */
    public function testFixControlsWithParenthesesAndSuffixBrace($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function testFixControlsWithParenthesesAndSuffixBraceProvider()
    {
        return array(
            array(
                'if ($test) {',
                'if($test){',
            ),
            array(
                'if ($test) {',
                'if( $test ){',
            ),
            array(
                'if ($test) {',
                'if  (   $test ){',
            ),
            array(
                'if (($test1 || $test2) && $test3) {',
                'if  (($test1 || $test2) && $test3){',
            ),
            array(
                'if (($test1 || $test2) && $test3) {',
                'if(($test1 || $test2) && $test3){',
            ),
            array(
                'if ($this->tesT ($test)) {',
            ),
            array(
                'if ($this->testtesT ($test)) {',
            ),
        );
    }

    public function testFixControlsWithPrefixBraceAndSuffixBrace()
    {
        $else = '}else{';
        $elseFixed = '} else {';

        $this->makeTest($elseFixed, $else);
    }

    public function testFixControlsWithPrefixBraceAndParenthesesAndSuffixBrace()
    {
        $elseif = '}elseif($test){';
        $elseifFixed = '} elseif ($test) {';

        $this->makeTest($elseifFixed, $elseif);
    }

    public function testFixControlsWithPrefixBraceAndParenthesesAndSuffixBraceInLambdas()
    {
        $use = ')use($test){';
        $useFixed = ') use ($test) {';

        $this->makeTest($useFixed, $use);
    }
}
