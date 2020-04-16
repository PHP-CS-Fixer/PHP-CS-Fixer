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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Antoine Bluchet <soyuka@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitExceptionMessageMatchesFixer
 */
final class PhpUnitExceptionMessageMatchesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $this->expectExceptionMessageMatches("/Message RegExp/");
        foo();
    }

}',
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $this->expectExceptionMessageRegExp("/Message RegExp/");
        foo();
    }

}',
                ['target' => PhpUnitTargetVersion::VERSION_8_4],
            ],
        ];
    }
}
