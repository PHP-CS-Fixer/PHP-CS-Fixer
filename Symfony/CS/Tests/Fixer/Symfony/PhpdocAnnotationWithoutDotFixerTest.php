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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class PhpdocAnnotationWithoutDotFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string $str   Some string
     * @param bool   $isStr Is it a string?
     * @param int    $int   Some multiline
     *                      description. With many dots
     *
     * @return array Result array
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
                '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string $str   Some string.
     * @param bool   $isStr Is it a string?
     * @param int    $int   Some multiline
     *                      description. With many dots.
     *
     * @return array Result array。
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
            ),
            array(
                // invalid char inside line won't crash the fixer
                '<?php
    /**
     * @var string This: '.chr(174).' is an odd character.
     * @var string This: '.chr(174).' is an odd character 2nd time。
     */',
            ),
        );
    }
}
