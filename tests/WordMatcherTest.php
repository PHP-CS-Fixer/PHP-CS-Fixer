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
 */
final class WordMatcherTest extends TestCase
{
    /**
     * @dataProvider provideMatchCases
     */
    public function testMatch(?string $expected, string $needle, array $candidates): void
    {
        $matcher = new WordMatcher($candidates);
        static::assertSame($expected, $matcher->match($needle));
    }

    public function provideMatchCases(): array
    {
        return [
            [
                null,
                'foo',
                [
                    'no_blank_lines_after_class_opening',
                    'no_blank_lines_after_phpdoc',
                ],
            ],
            [
                'no_blank_lines_after_phpdoc',
                'no_blank_lines_after_phpdocs',
                [
                    'no_blank_lines_after_class_opening',
                    'no_blank_lines_after_phpdoc',
                ],
            ],
            [
                'no_blank_lines_after_foo',
                'no_blank_lines_foo',
                [
                    'no_blank_lines_after_foo',
                    'no_blank_lines_before_foo',
                ],
            ],
            [
                null,
                'braces',
                [
                    'elseif',
                ],
            ],
        ];
    }
}
