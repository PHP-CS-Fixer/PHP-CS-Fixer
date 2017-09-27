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

namespace PhpCsFixer\Tests\Fixer\Strict;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Strict\StrictMethodsFixer
 */
final class StrictMethodsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     * @requires PHP 7.0
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
namespace A\B\C;
class A {
    /**
     * @param int $test1
     * @param int|bool $test2
     */
    public function myFunction(int $test, string $test1, $test2)
    {
    }
}',
                '<?php
namespace A\B\C;
class A {
    /**
     * @param int $test
     * @param int $test1
     * @param int|bool $test2
     */
    public function myFunction($test, string $test1, $test2)
    {
    }
}',
            ],
        ];
    }
}
