<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitConstructFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $fixer = $this->getFixer();

        $fixer->configure(array(
            'assertEquals' => true,
            'assertSame' => true,
            'assertNotEquals' => true,
            'assertNotSame' => true,
        ));
        $this->makeTest($expected, $input);

        $fixer->configure(array(
            'assertEquals' => false,
            'assertSame' => false,
            'assertNotEquals' => false,
            'assertNotSame' => false,
        ));
        $this->makeTest($input ?: $expected, null, null, $fixer);

        foreach (array('assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame') as $method) {
            $config = array(
                'assertEquals' => false,
                'assertSame' => false,
                'assertNotEquals' => false,
                'assertNotSame' => false,
            );
            $config[$method] = true;

            $fixer->configure($config);
            $this->makeTest(
                $expected,
                $input && false !== strpos($input, $method) ? $input : null,
                null,
                $fixer
            );
        }
    }

    public function provideTestFixCases()
    {
        return array(
            array('<?php $sth->assertSame(true, $foo);'),
            array(
                '<?php $this->assertTrue($a);',
                '<?php $this->assertSame(true, $a);',
            ),
            array(
                '<?php $this->assertTrue($a  , "true" . $bar);',
                '<?php $this->assertSame(true  , $a  , "true" . $bar);',
            ),
            array(
                '<?php $this->assertFalse(  $a, "false" . $bar);',
                '<?php $this->assertSame(  false, $a, "false" . $bar);',
            ),
            array(
                '<?php $this->assertNull(  $a  , "null" . $bar);',
                '<?php $this->assertSame(  null, $a  , "null" . $bar);',
            ),
            array(
                '<?php $this->assertNotNull(  $a  , "notNull" . $bar);',
                '<?php $this->assertNotSame(  null, $a  , "notNull" . $bar);',
            ),
            array(
                '<?php $this->assertFalse(  $a, "false" . $bar);',
                '<?php $this->assertEquals(  false, $a, "false" . $bar);',
            ),
            array(
                '<?php $this->assertNotNull(  $a  , "notNull" . $bar);',
                '<?php $this->assertNotEquals(  null, $a  , "notNull" . $bar);',
            ),
            array(
                '<?php
    $this->assertTrue(
        $a,
        "foo" . $bar
    );',
                '<?php
    $this->assertSame(
        true,
        $a,
        "foo" . $bar
    );',
            ),
        );
    }
}
