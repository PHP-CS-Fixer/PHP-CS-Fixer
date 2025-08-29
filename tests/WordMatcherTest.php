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

namespace PhpCsFixer\Tests;

use PhpCsFixer\WordMatcher;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\WordMatcher
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class WordMatcherTest extends TestCase
{
    /**
     * @param list<string> $candidates
     *
     * @dataProvider provideMatchCases
     */
    public function testMatch(?string $expected, string $needle, array $candidates): void
    {
        $matcher = new WordMatcher($candidates);
        self::assertSame($expected, $matcher->match($needle));
    }

    /**
     * @return iterable<int, array{?string, string, list<string>}>
     */
    public static function provideMatchCases(): iterable
    {
        yield [
            null,
            'foo',
            [
                'no_blank_lines_after_class_opening',
                'no_blank_lines_after_phpdoc',
            ],
        ];

        yield [
            'no_blank_lines_after_phpdoc',
            'no_blank_lines_after_phpdocs',
            [
                'no_blank_lines_after_class_opening',
                'no_blank_lines_after_phpdoc',
            ],
        ];

        yield [
            'no_blank_lines_after_foo',
            'no_blank_lines_foo',
            [
                'no_blank_lines_after_foo',
                'no_blank_lines_before_foo',
            ],
        ];

        yield [
            null,
            'braces',
            [
                'elseif',
            ],
        ];
    }
}
