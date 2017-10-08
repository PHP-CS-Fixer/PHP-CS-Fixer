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
     * @param null|string $expected
     * @param string      $needle
     * @param array       $candidates
     *
     * @dataProvider provideMatchCases
     */
    public function testMatch($expected, $needle, array $candidates)
    {
        $matcher = new WordMatcher($candidates);
        $this->assertSame($expected, $matcher->match($needle));
    }

    /**
     * @return array
     */
    public function provideMatchCases()
    {
        return array(
            array(
                null,
                'foo',
                array(
                    'no_blank_lines_after_class_opening',
                    'no_blank_lines_after_phpdoc',
                ),
            ),
            array(
                'no_blank_lines_after_phpdoc',
                'no_blank_lines_after_phpdocs',
                array(
                    'no_blank_lines_after_class_opening',
                    'no_blank_lines_after_phpdoc',
                ),
            ),
            array(
                'no_blank_lines_after_foo',
                'no_blank_lines_foo',
                array(
                    'no_blank_lines_after_foo',
                    'no_blank_lines_before_foo',
                ),
            ),
            array(
                null,
                'braces',
                array(
                    'elseif',
                ),
            ),
        );
    }
}
