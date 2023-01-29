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
     * @dataProvider provideComparatorCases
     *
     * @group legacy
     */
    public function testComparatorTogether(string $first, string $second, bool $expected): void
    {
        $tag1 = new Tag(new Line('* @'.$first));
        $tag2 = new Tag(new Line('* @'.$second));

        $this->expectDeprecation('%AMethod PhpCsFixer\DocBlock\TagComparator::shouldBeTogether is deprecated and will be removed in version 4.0.');

        static::assertSame($expected, TagComparator::shouldBeTogether($tag1, $tag2));
    }

    public static function provideComparatorCases(): array
    {
        return [
            ['return', 'return', true],
            ['param', 'param', true],
            ['return', 'param', false],
            ['var', 'foo', false],
            ['api', 'deprecated', false],
            ['author', 'copyright', true],
            ['author', 'since', false],
            ['link', 'see', true],
            ['category', 'package', true],
        ];
    }

    /**
     * @dataProvider provideComparatorWithDefinedGroupsCases
     *
     * @param string[][] $groups
     *
     * @group legacy
     */
    public function testComparatorTogetherWithDefinedGroups(array $groups, string $first, string $second, bool $expected): void
    {
        $tag1 = new Tag(new Line('* @'.$first));
        $tag2 = new Tag(new Line('* @'.$second));

        $this->expectDeprecation('%AMethod PhpCsFixer\DocBlock\TagComparator::shouldBeTogether is deprecated and will be removed in version 4.0.');

        static::assertSame(
            $expected,
            TagComparator::shouldBeTogether($tag1, $tag2, $groups)
        );
    }

    public static function provideComparatorWithDefinedGroupsCases(): array
    {
        return [
            [[['param', 'return']], 'return', 'return', true],
            [[], 'param', 'return', false],
            [[['param', 'return']], 'return', 'param', true],
            [[['param', 'return']], 'var', 'foo', false],
            [[['param', 'return']], 'api', 'deprecated', false],
            [[['param', 'return']], 'author', 'copyright', false],
            [[['param', 'return'], ['author', 'since']], 'author', 'since', true],
            [array_merge(TagComparator::DEFAULT_GROUPS, [['param', 'return']]), 'link', 'see', true],
            [[['param', 'return']], 'category', 'package', false],
        ];
    }
}
