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
use PhpCsFixer\DocBlock\TagComparator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\TagComparator
 */
final class TagComparatorTest extends TestCase
{
    /**
     * @dataProvider provideComparatorTogetherCases
     *
     * @group legacy
     */
    public function testComparatorTogether(string $first, string $second, bool $expected): void
    {
        $tag1 = new Tag(new Line('* @'.$first));
        $tag2 = new Tag(new Line('* @'.$second));

        $this->expectDeprecation('Method PhpCsFixer\DocBlock\TagComparator::shouldBeTogether is deprecated and will be removed in version 4.0.');

        self::assertSame($expected, TagComparator::shouldBeTogether($tag1, $tag2));
    }

    public static function provideComparatorTogetherCases(): iterable
    {
        yield ['return', 'return', true];

        yield ['param', 'param', true];

        yield ['return', 'param', false];

        yield ['var', 'foo', false];

        yield ['api', 'deprecated', false];

        yield ['author', 'copyright', true];

        yield ['author', 'since', false];

        yield ['link', 'see', true];

        yield ['category', 'package', true];
    }

    /**
     * @dataProvider provideComparatorTogetherWithDefinedGroupsCases
     *
     * @param string[][] $groups
     *
     * @group legacy
     */
    public function testComparatorTogetherWithDefinedGroups(array $groups, string $first, string $second, bool $expected): void
    {
        $tag1 = new Tag(new Line('* @'.$first));
        $tag2 = new Tag(new Line('* @'.$second));

        $this->expectDeprecation('Method PhpCsFixer\DocBlock\TagComparator::shouldBeTogether is deprecated and will be removed in version 4.0.');

        self::assertSame(
            $expected,
            TagComparator::shouldBeTogether($tag1, $tag2, $groups)
        );
    }

    public static function provideComparatorTogetherWithDefinedGroupsCases(): iterable
    {
        yield [[['param', 'return']], 'return', 'return', true];

        yield [[], 'param', 'return', false];

        yield [[['param', 'return']], 'return', 'param', true];

        yield [[['param', 'return']], 'var', 'foo', false];

        yield [[['param', 'return']], 'api', 'deprecated', false];

        yield [[['param', 'return']], 'author', 'copyright', false];

        yield [[['param', 'return'], ['author', 'since']], 'author', 'since', true];

        yield [[...TagComparator::DEFAULT_GROUPS, ['param', 'return']], 'link', 'see', true];

        yield [[['param', 'return']], 'category', 'package', false];
    }
}
