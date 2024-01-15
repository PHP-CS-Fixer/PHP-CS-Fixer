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

namespace PhpCsFixer\Tests\Fixer\DoctrineAnnotation;

use PhpCsFixer\Tests\AbstractDoctrineAnnotationFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractDoctrineAnnotationFixer
 * @covers \PhpCsFixer\Doctrine\Annotation\DocLexer
 * @covers \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer
 */
final class DoctrineAnnotationArrayAssignmentFixerTest extends AbstractDoctrineAnnotationFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFixWithEqual(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['operator' => '=']);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield from self::createTestCases([
            [<<<'EOD'

                /**
                 * @Foo
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo()
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(bar="baz")
                 */
                EOD],
            [
                <<<'EOD'

                    /**
                     * @Foo(bar="baz")
                     */
                    EOD,
            ],
            [
                <<<'EOD'

                    /**
                     * @Foo({bar="baz"})
                     */
                    EOD,
                <<<'EOD'

                    /**
                     * @Foo({bar:"baz"})
                     */
                    EOD,
            ],
            [
                <<<'EOD'

                    /**
                     * @Foo({bar="baz"})
                     */
                    EOD,
                <<<'EOD'

                    /**
                     * @Foo({bar:"baz"})
                     */
                    EOD,
            ],
            [
                <<<'EOD'

                    /**
                     * @Foo({bar = "baz"})
                     */
                    EOD,
                <<<'EOD'

                    /**
                     * @Foo({bar : "baz"})
                     */
                    EOD,
            ],
            [<<<'EOD'

                /**
                 * See {@link https://help Help} or {@see BarClass} for details.
                 */
                EOD],
        ]);

        yield [
            <<<'EOD'
                <?php

                /**
                * @see \User getId()
                */

                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithColonCases
     */
    public function testFixWithColon(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['operator' => ':']);
        $this->doTest($expected, $input);
    }

    public static function provideFixWithColonCases(): iterable
    {
        yield from self::createTestCases([
            [<<<'EOD'

                /**
                 * @Foo
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo()
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(bar:"baz")
                 */
                EOD],
            [
                <<<'EOD'

                    /**
                     * @Foo(bar:"baz")
                     */
                    EOD,
            ],
            [
                <<<'EOD'

                    /**
                     * @Foo({bar:"baz"})
                     */
                    EOD,
                <<<'EOD'

                    /**
                     * @Foo({bar="baz"})
                     */
                    EOD,
            ],
            [
                <<<'EOD'

                    /**
                     * @Foo({bar : "baz"})
                     */
                    EOD,
                <<<'EOD'

                    /**
                     * @Foo({bar = "baz"})
                     */
                    EOD,
            ],
            [
                <<<'EOD'

                    /**
                     * @Foo(foo="bar", {bar:"baz"})
                     */
                    EOD,
                <<<'EOD'

                    /**
                     * @Foo(foo="bar", {bar="baz"})
                     */
                    EOD,
            ],
            [<<<'EOD'

                /**
                 * See {@link https://help Help} or {@see BarClass} for details.
                 */
                EOD],
        ]);
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php class FooClass{
                    /**
                     * @Foo({bar = "baz"})
                     */
                    private readonly Foo $foo;
                }
                EOD,
            <<<'EOD'
                <?php class FooClass{
                    /**
                     * @Foo({bar : "baz"})
                     */
                    private readonly Foo $foo;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php class FooClass{
                    /**
                     * @Foo({bar = "baz"})
                     */
                    readonly private Foo $foo;
                }
                EOD,
            <<<'EOD'
                <?php class FooClass{
                    /**
                     * @Foo({bar : "baz"})
                     */
                    readonly private Foo $foo;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php class FooClass{
                    /**
                     * @Foo({bar = "baz"})
                     */
                    readonly Foo $foo;
                }
                EOD,
            <<<'EOD'
                <?php class FooClass{
                    /**
                     * @Foo({bar : "baz"})
                     */
                    readonly Foo $foo;
                }
                EOD,
        ];
    }
}
