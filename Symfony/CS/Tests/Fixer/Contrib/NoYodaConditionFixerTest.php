<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Nicola Pietroluongo <nik.longstone@gmail.com>
 */
class NoYodaConditionFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideExamples()
    {
        $yodaOperators = array('==', '===', '!=', '!==');
        $excludedOperators = array('<>', '<=', '>=',

        );

        $correctData = array();
        foreach (array_merge($yodaOperators, $excludedOperators) as $operator) {
            $correctData = array_merge($correctData, $this->getCorrectDataWithOperator($operator));
        }

        $incorrectData = array();
        foreach (array_merge($yodaOperators) as $operator) {
            $incorrectData = array_merge($incorrectData, $this->getIncorrectDataWithOperator($operator));
        }

        return array_merge(
            $correctData,
            $incorrectData
        );
    }

    /**
     * What is incorrect should be changed.
     */
    private function getIncorrectDataWithOperator($operator)
    {
        return array(
            array('<?php if ( $theForce '.$operator.' 1 ) { }  ;', '<?php if ( 1 '.$operator.' $theForce ) { }  ;'),
            array('<?php if ( $theForce '.$operator.' null ) { }  ;', '<?php if ( null '.$operator.' $theForce ) { }  ;'),
            array('<?php if ( $theForce '.$operator.' true ) { }  ;', '<?php if ( true '.$operator.' $theForce ) { }  ;'),
            array('<?php if ( $theForce '.$operator.' false ) { }  ;', '<?php if ( false '.$operator.' $theForce ) { }  ;'),
        );
    }

    /**
     * What is correct should not be changed.
     */
    private function getCorrectDataWithOperator($operator)
    {
        return array(
            array('<?php if ( $theForce '.$operator.' 1 ) { }  ;'),
            array('<?php if ( $theForce '.$operator.' 1 && $sith '.$operator.' 0) { }  ;'),
            array('<?php if ( $theForce '.$operator.' "be with you" ) { }  ;'),
            array('<?php if ( 0 '.$operator.' 1 ) { }  ;'),
            array('<?php if ( true '.$operator.' false ) { }  ;'),
            array('<?php if ( "Yoda" '.$operator.' "Jedi" ) { }  ;'),
            array('<?php if ( $yoda '.$operator.' $jedi ) { }  ;'),
            array("<?php if ( 'may the force' '.$operator.' 'be with you' ) { }  ;"),
        );
    }
}
