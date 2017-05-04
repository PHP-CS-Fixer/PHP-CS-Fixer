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

namespace PhpCsFixer\Tests\DocBlock;

use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\DocBlock\Tag;
use PHPUnit\Framework\TestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\Tag
 */
final class TagTest extends TestCase
{
    /**
     * @param string $expected
     * @param string $new
     * @param string $input
     *
     * @dataProvider provideNameCases
     */
    public function testName($expected, $new, $input)
    {
        $tag = new Tag(new Line($input));

        $this->assertSame($expected, $tag->getName());

        if ('other' === $expected) {
            $this->setExpectedException('RuntimeException', 'Cannot set name on unknown tag');
        }

        $tag->setName($new);

        $this->assertSame($new, $tag->getName());
    }

    public function provideNameCases()
    {
        return array(
            array('param', 'var', '     * @param Foo $foo'),
            array('return', 'type', '*   @return            false'),
            array('thRoWs', 'throws', '*@thRoWs \Exception'),
            array('THROWSSS', 'throws', "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"),
            array('other', 'foobar', ' *   @\Foo\Bar(baz = 123)'),
            array('expectedException', 'baz', '     * @expectedException Exception'),
            array('property-read', 'property-write', ' * @property-read integer $daysInMonth number of days in the given month'),
            array('method', 'foo', ' * @method'),
            array('method', 'hi', ' * @method string getString()'),
            array('other', 'hello', ' * @method("GET")'),
        );
    }

    /**
     * @param bool   $expected
     * @param string $input
     *
     * @dataProvider provideValidCases
     */
    public function testValid($expected, $input)
    {
        $tag = new Tag(new Line($input));

        $this->assertSame($expected, $tag->valid());
    }

    public function provideValidCases()
    {
        return array(
            array(true, '     * @param Foo $foo'),
            array(true, '*   @return            false'),
            array(true, '*@throws \Exception'),
            array(true, ' * @method'),
            array(true, ' * @method string getString()'),
            array(true, ' * @property-read integer $daysInMonth number of days in the given month'),
            array(false, ' * @method("GET")'),
            array(false, '*@thRoWs \InvalidArgumentException'),
            array(false, "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"),
            array(false, ' *   @\Foo\Bar(baz = 123)'),
            array(false, '     * @expectedException Exception'),
        );
    }
}
