<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Bram Gotink <bram@gotink.me>
 */
class YodaConditionsFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideHighPrececenceExamples
     */
    public function testHighPrececence($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideHighPrececenceExamples()
    {
        return array(
            // simple Yoda conditions
            array('<?php return 2 < $a;'),
            array('<?php return 2 >= $a;'),
            array('<?php return 2 > $this->getA();'),

            // simple non-Yoda conditions
            array(
                '<?php return 2 < $a;',
                '<?php return $a > 2;',
            ),
            array(
                '<?php return 2 >= $a;',
                '<?php return $a <= 2;',
            ),
            array(
                '<?php return 2 > $this->getA();',
                '<?php return $this->getA() < 2;',
            ),

            // non-simple Yoda conditions
            array('<?php return 2 < $this->getA() % (4 - 1);'),
            array('<?php return 2 >= $a ? true : false;'),
            array('<?php return 0 < (0 > $a ? 0 : $a->getB());'),

            // non-simple non-Yoda conditions
            array(
                '<?php return 2 < $this->getA() % (4 - 1);',
                '<?php return $this->getA() % (4 - 1) > 2;',
            ),
            array(
                '<?php return 2 >= $a ? true : false;',
                '<?php return $a <= 2 ? true : false;',
            ),
            array(
                '<?php return 0 < (0 > $a ? 0 : $a->getB());',
                '<?php return ($a < 0 ? 0 : $a->getB()) > 0;',
            ),

            // large complicated code sample
            array(
'<?php
if (2 < $a) {
    return 1 > $b % 2 ? $b - 1 : $b;
} elseif (0 < (1 < $a % 3 ? $b->getC($c ? $c : $a))) {
    return $a or 0 < $b;
}',
'<?php
if ($a > 2) {
    return $b % 2 < 1 ? $b - 1 : $b;
} elseif (($a % 3 > 1 ? $b->getC($c ? $c : $a)) > 0) {
    return $a or $b > 0;
}'
            ),
        );
    }

    /**
     * @dataProvider provideLowPrecedenceExamples
     */
    public function testLowPrecedence($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideLowPrecedenceExamples()
    {
        return array(
            // simple Yoda conditions
            array('<?php return 2 === $a;'),
            array('<?php return null !== $a;'),
            array('<?php return "" !== $this->getA();'),

            // simple non-Yoda conditions
            array(
                '<?php return 2 === $a;',
                '<?php return $a === 2;',
            ),
            array(
                '<?php return null !== $a;',
                '<?php return $a !== null;',
            ),
            array(
                '<?php return "" !== $this->getA();',
                '<?php return $this->getA() !== "";',
            ),

            // non-simple Yoda conditions
            array('<?php return 2 !== $this->getA() % (4 - 1);'),
            array('<?php return 2 !== $a ? true : false;'),
            array('<?php return null !== (null === $a ? null : $a->getB());'),

            // non-simple non-Yoda conditions
            array(
                '<?php return 2 !== $this->getA() % (4 - 1);',
                '<?php return $this->getA() % (4 - 1) !== 2;',
            ),
            array(
                '<?php return 2 !== $a ? true : false;',
                '<?php return $a !== 2 ? true : false;',
            ),
            array(
                '<?php return null !== (null === $a ? null : $a->getB());',
                '<?php return ($a === null ? null : $a->getB()) !== null;',
            ),

            // large complicated code sample
            array(
'<?php
if (2 === $a) {
    return 1 === $b % 2 ? $b - 1 : $b;
} elseif (null !== (1 === $a % 2 ? $b->getC($c ? $c : $a))) {
    return $a or null !== $b;
}',
'<?php
if ($a === 2) {
    return $b % 2 === 1 ? $b - 1 : $b;
} elseif (($a % 2 === 1 ? $b->getC($c ? $c : $a)) !== null) {
    return $a or $b !== null;
}'
            ),
        );
    }

    /**
     * @dataProvider provideMixedExamples
     */
    public function testMix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideMixedExamples()
    {
        return array(
            // Yoda conditions
            array('<?php return false !== 0 > $a;'),
            array('<?php return 0 > (null === $a ? 0 : $a->getB());'),

            // non-Yoda conditions
            array(
                '<?php return false !== 0 > $a;',
                '<?php return $a < 0 !== false;',
            ),
            array(
                '<?php return 0 > (null === $a ? 0 : $a->getB());',
                '<?php return ($a === null ? 0 : $a->getB()) < 0;',
            ),
        );
    }
}
