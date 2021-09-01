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
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\TagComparator
 */
final class TagComparatorTest extends TestCase
{
    /**
     * @dataProvider provideComparatorCases
     */
    public function testComparatorTogether(string $first, string $second, bool $expected): void
    {
        $tag1 = new Tag(new Line('* @'.$first));
        $tag2 = new Tag(new Line('* @'.$second));

        static::assertSame($expected, TagComparator::shouldBeTogether($tag1, $tag2));
    }

    public function provideComparatorCases(): array
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
}
