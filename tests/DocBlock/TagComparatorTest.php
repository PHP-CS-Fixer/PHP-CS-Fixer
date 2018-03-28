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
use PhpCsFixer\DocBlock\TagComparator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\TagComparator
 */
final class TagComparatorTest extends TestCase
{
    /**
     * @param string $first
     * @param string $second
     * @param bool   $expected
     *
     * @dataProvider provideComparatorCases
     */
    public function testComparatorTogether($first, $second, $expected)
    {
        $tag1 = new Tag(new Line('* @'.$first));
        $tag2 = new Tag(new Line('* @'.$second));

        $this->assertSame($expected, TagComparator::shouldBeTogether($tag1, $tag2));
    }

    public function provideComparatorCases()
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
