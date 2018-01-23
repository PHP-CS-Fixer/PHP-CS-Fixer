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

namespace PhpCsFixer\Tests\Fixer\DoctrineAnnotation;

use PhpCsFixer\Tests\AbstractDoctrineAnnotationFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractDoctrineAnnotationFixer
 * @covers \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer
 */
final class DoctrineAnnotationArrayAssignmentFixerTest extends AbstractDoctrineAnnotationFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFixWithEqual($expected, $input = null)
    {
        $this->fixer->configure(['operator' => '=']);
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo
 */'],
            ['
/**
 * @Foo()
 */'],
            ['
/**
 * @Foo(bar="baz")
 */'],
            [
                '
/**
 * @Foo(bar="baz")
 */',
            ],
            [
                '
/**
 * @Foo({bar="baz"})
 */',
                '
/**
 * @Foo({bar:"baz"})
 */',
            ],
            [
                '
/**
 * @Foo({bar="baz"})
 */',
                '
/**
 * @Foo({bar:"baz"})
 */',
            ],
            [
                '
/**
 * @Foo({bar = "baz"})
 */',
                '
/**
 * @Foo({bar : "baz"})
 */',
            ],
            ['
/**
 * See {@link http://help Help} or {@see BarClass} for details.
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithColonCases
     */
    public function testFixWithColon($expected, $input = null)
    {
        $this->fixer->configure(['operator' => ':']);
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithColonCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo
 */'],
            ['
/**
 * @Foo()
 */'],
            ['
/**
 * @Foo(bar:"baz")
 */'],
            [
                '
/**
 * @Foo(bar:"baz")
 */',
            ],
            [
                '
/**
 * @Foo({bar:"baz"})
 */',
                '
/**
 * @Foo({bar="baz"})
 */',
            ],
            [
                '
/**
 * @Foo({bar : "baz"})
 */',
                '
/**
 * @Foo({bar = "baz"})
 */',
            ],
            [
                '
/**
 * @Foo(foo="bar", {bar:"baz"})
 */',
                '
/**
 * @Foo(foo="bar", {bar="baz"})
 */',
            ],
            ['
/**
 * See {@link http://help Help} or {@see BarClass} for details.
 */'],
        ]);
    }

    /**
     * @return array
     */
    public function getInvalidConfigurationCases()
    {
        return array_merge(parent::getInvalidConfigurationCases(), [
            [['operator' => 'foo']],
            [[
                'operator' => 'foo',
                'ignored_tags' => [],
            ]],
        ]);
    }
}
