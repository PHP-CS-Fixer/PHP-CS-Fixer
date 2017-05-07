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
            $this->setExpectedException(\RuntimeException::class, 'Cannot set name on unknown tag');
        }

        $tag->setName($new);

        $this->assertSame($new, $tag->getName());
    }

    public function provideNameCases()
    {
        return [
            ['param', 'var', '     * @param Foo $foo'],
            ['return', 'type', '*   @return            false'],
            ['thRoWs', 'throws', '*@thRoWs \Exception'],
            ['THROWSSS', 'throws', "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"],
            ['other', 'foobar', ' *   @\Foo\Bar(baz = 123)'],
            ['expectedException', 'baz', '     * @expectedException Exception'],
            ['property-read', 'property-write', ' * @property-read integer $daysInMonth number of days in the given month'],
            ['method', 'foo', ' * @method'],
            ['method', 'hi', ' * @method string getString()'],
            ['other', 'hello', ' * @method("GET")'],
        ];
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
        return [
            [true, '     * @param Foo $foo'],
            [true, '*   @return            false'],
            [true, '*@throws \Exception'],
            [true, ' * @method'],
            [true, ' * @method string getString()'],
            [true, ' * @property-read integer $daysInMonth number of days in the given month'],
            [false, ' * @method("GET")'],
            [false, '*@thRoWs \InvalidArgumentException'],
            [false, "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"],
            [false, ' *   @\Foo\Bar(baz = 123)'],
            [false, '     * @expectedException Exception'],
        ];
    }
}
