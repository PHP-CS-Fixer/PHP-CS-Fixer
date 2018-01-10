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

namespace PhpCsFixer\Tests;

use PhpCsFixer\FixerNameValidator;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerNameValidator
 */
final class FixerNameValidatorTest extends TestCase
{
    /**
     * @param string $name
     * @param bool   $isCustom
     * @param bool   $isValid
     *
     * @dataProvider provideIsValidCases
     */
    public function testIsValid($name, $isCustom, $isValid)
    {
        $validator = new FixerNameValidator();

        $this->assertSame($isValid, $validator->isValid($name, $isCustom));
    }

    public function provideIsValidCases()
    {
        return array(
            array('', true, false),
            array('', false, false),
            array('foo', true, false),
            array('foo', false, true),
            array('foo_bar', false, true),
            array('foo_bar_4', false, true),
            array('Foo', false, false),
            array('fooBar', false, false),
            array('4foo', false, false),
            array('_foo', false, false),
            array('4_foo', false, false),
            array('vendor/foo', false, false),
            array('bendor/foo', true, false),
            array('Vendor/foo', true, true),
            array('Vendor4/foo', true, true),
            array('4vendor/foo', true, false),
            array('Vendor/foo', true, true),
            array('FooBar/foo', true, true),
            array('Foo-Bar/foo', true, false),
            array('Foo_Bar/foo', true, false),
            array('Foo/foo/bar', true, false),
            array('/foo', true, false),
            array('/foo', false, false),
            array('/foo/bar', true, false),
        );
    }
}
