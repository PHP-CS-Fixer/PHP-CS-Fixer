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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitNamespacedTestCase
 */
final class PhpUnitNamespacedTestCaseFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [
                '<?php
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
    }',
            ],
            [
                '<?php
    final class TextDiffTest extends PHPUnit\Framework\TestCase
    {
    }',
                '<?php
    final class TextDiffTest extends PHPUnit_Framework_TestCase
    {
    }',
            ],
            [
                '<?php
    use \PHPUnit\Framework\TestCase;
    final class TextDiffTest extends TestCase
    {
    }',
                '<?php
    use \PHPUnit_Framework_TestCase;
    final class TextDiffTest extends PHPUnit_Framework_TestCase
    {
    }',
            ],
            [
                '<?php
    use \PHPUnit\Framework\TestCase as TestAlias;
    final class TextDiffTest extends TestAlias
    {
    }',
                '<?php
    use \PHPUnit_Framework_TestCase as TestAlias;
    final class TextDiffTest extends TestAlias
    {
    }',
            ],
        ];
    }
}
