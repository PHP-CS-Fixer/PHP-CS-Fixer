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

        static::assertSame($expected, $tag->getName());

        if ('other' === $expected) {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('Cannot set name on unknown tag');
        }

        $tag->setName($new);

        static::assertSame($new, $tag->getName());
    }

    public function provideNameCases(): array
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
     * @param list<string>|string $names
     *
     * @dataProvider provideNameEqualsCases
     */
    public function testNameEquals(bool $expected, $names, bool $allowWildcards, string $input): void
    {
        $tag = new Tag(new Line($input));

        static::assertSame($expected, $tag->nameEquals($names, $allowWildcards));
    }

    public function provideNameEqualsCases(): array
    {
        return [
            [true, 'param', false, '     * @param Foo $foo'],
            [true, 'return', false, '*   @return            false'],
            [false, 'param', false, '     * @psalm-param Foo $foo'],
            [false, '*param', false, '     * @psalm-param Foo $foo'],
            [true, 'param', true, '     * @param Foo $foo'],
            [true, 'return', true, '*   @return            false'],
            [false, 'param', true, '     * @psalm-param Foo $foo'],
            [true, '*param', true, '     * @psalm-param Foo $foo'],
            [true, ['param', 'return'], false, '     * @param Foo $foo'],
            [false, ['*param', '*return'], false, '     * @param Foo $foo'],
            [false, ['param', 'return'], false, '     * @psalm-param Foo $foo'],
            [false, ['*param', '*return'], false, '     * @psalm-param Foo $foo'],
            [true, ['param', 'return'], true, '     * @param Foo $foo'],
            [true, ['*param', '*return'], true, '     * @param Foo $foo'],
            [false, ['param', 'return'], true, '     * @psalm-param Foo $foo'],
            [true, ['*param', '*return'], true, '     * @psalm-param Foo $foo'],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValid(bool $expected, string $input): void
    {
        $tag = new Tag(new Line($input));

        static::assertSame($expected, $tag->valid());
    }

    public function provideValidCases(): array
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
