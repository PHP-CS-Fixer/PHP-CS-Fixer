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
class PhpUnitConstructFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
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
            array(
                '<?php $this->assertNull(/*bar*/ $a);',
                '<?php $this->assertSame(null /*foo*/, /*bar*/ $a);',
            ),
            array(
                '<?php $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException());',
            ),
            array(
                '<?php $this->assertSame(null /*comment*/ === $eventException ? $exception : $eventException, $event->getException());',
            ),
        );
    }
}
