<?php

declare(strict_types=1);

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
use PhpCsFixer\Tests\TestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\Tag
 */
final class TagTest extends TestCase
{
    /**
     * @dataProvider provideNameCases
     */
    public function testName(string $expected, string $new, string $input): void
    {
        $tag = new Tag(new Line($input));

        self::assertSame($expected, $tag->getName());

        if ('other' === $expected) {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('Cannot set name on unknown tag');
        }

        $tag->setName($new);

        self::assertSame($new, $tag->getName());
    }

    /**
     * @return iterable<int, array{string, string, string}>
     */
    public static function provideNameCases(): iterable
    {
        yield ['param', 'var', '     * @param Foo $foo'];

        yield ['return', 'type', '*   @return            false'];

        yield ['thRoWs', 'throws', '*@thRoWs \Exception'];

        yield ['THROWSSS', 'throws', "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"];

        yield ['other', 'foobar', ' *   @\Foo\Bar(baz = 123)'];

        yield ['expectedException', 'baz', '     * @expectedException Exception'];

        yield ['property-read', 'property-write', ' * @property-read integer $daysInMonth number of days in the given month'];

        yield ['method', 'foo', ' * @method'];

        yield ['method', 'hi', ' * @method string getString()'];

        yield ['other', 'hello', ' * @method("GET")'];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValid(bool $expected, string $input): void
    {
        $tag = new Tag(new Line($input));

        self::assertSame($expected, $tag->valid());
    }

    /**
     * @return iterable<int, array{bool, string}>
     */
    public static function provideValidCases(): iterable
    {
        yield [true, '     * @param Foo $foo'];

        yield [true, '*   @return            false'];

        yield [true, '*@throws \Exception'];

        yield [true, ' * @method'];

        yield [true, ' * @method string getString()'];

        yield [true, ' * @property-read integer $daysInMonth number of days in the given month'];

        yield [false, ' * @method("GET")'];

        yield [false, '*@thRoWs \InvalidArgumentException'];

        yield [false, "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"];

        yield [false, ' *   @\Foo\Bar(baz = 123)'];

        yield [false, '     * @expectedException Exception'];
    }
}
