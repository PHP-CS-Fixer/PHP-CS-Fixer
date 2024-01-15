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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @author Jefersson Nathan <malukenho.dev@gmail.com>
 *
 * @covers \PhpCsFixer\Fixer\AbstractPhpUnitFixer
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitSizeClassFixer
 */
final class PhpUnitSizeClassFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'It does not change normal classes' => [
            <<<'EOD'
                <?php

                class Hello
                {
                }

                EOD,
        ];

        yield 'It marks a test class as @small by default' => [
            <<<'EOD'
                <?php

                /**
                 * @small
                 */
                class Test extends TestCase
                {
                }

                EOD,
            <<<'EOD'
                <?php

                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It marks a test class as specified in the configuration' => [
            <<<'EOD'
                <?php

                /**
                 * @large
                 */
                class Test extends TestCase
                {
                }

                EOD,
            <<<'EOD'
                <?php

                class Test extends TestCase
                {
                }

                EOD,
            ['group' => 'large'],
        ];

        yield 'It adds an @small tag to a class that already has a doc block' => [
            <<<'EOD'
                <?php

                /**
                 * @coversNothing
                 * @small
                 */
                class Test extends TestCase
                {
                }

                EOD,
            <<<'EOD'
                <?php

                /**
                 * @coversNothing
                 */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It does not change a class that is already @small' => [
            <<<'EOD'
                <?php

                /**
                 * @small
                 */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It does not change a class that is already @small and has other annotations' => [
            <<<'EOD'
                <?php

                /**
                 * @author malukenho
                 * @coversNothing
                 * @large
                 * @group large
                 */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It works on other indentation levels' => [
            <<<'EOD'
                <?php

                if (class_exists("Foo\Bar")) {
                    /**
                     * @small
                     */
                    class Test Extends TestCase
                    {
                    }
                }

                EOD,
            <<<'EOD'
                <?php

                if (class_exists("Foo\Bar")) {
                    class Test Extends TestCase
                    {
                    }
                }

                EOD,
        ];

        yield 'It works on other indentation levels when the class has other annotations' => [
            <<<'EOD'
                <?php

                if (class_exists("Foo\Bar")) {
                    /**
                     * @author malukenho again
                     *
                     *
                     * @covers \Other\Class
                     * @small
                     */
                    class Test Extends TestCase
                    {
                    }
                }

                EOD,
            <<<'EOD'
                <?php

                if (class_exists("Foo\Bar")) {
                    /**
                     * @author malukenho again
                     *
                     *
                     * @covers \Other\Class
                     */
                    class Test Extends TestCase
                    {
                    }
                }

                EOD,
        ];

        yield 'It always adds @small to the bottom of the doc block' => [
            <<<'EOD'
                <?php

                /**
                 * @coversNothing
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 * @small
                 */
                class Test extends TestCase
                {
                }

                EOD,
            <<<'EOD'
                <?php

                /**
                 * @coversNothing
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 *
                 */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It does not change a class with a single line @{size} doc block' => [
            <<<'EOD'
                <?php

                /** @medium */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It adds an @small tag to a class that already has a one linedoc block' => [
            <<<'EOD'
                <?php

                /**
                 * @coversNothing
                 * @small
                 */
                class Test extends TestCase
                {
                }

                EOD,
            <<<'EOD'
                <?php

                /** @coversNothing */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'By default it will not mark an abstract class as @small' => [
            <<<'EOD'
                <?php

                abstract class Test
                {
                }

                EOD,
        ];

        yield 'It works correctly with multiple classes in one file, even when one of them is not allowed' => [
            <<<'EOD'
                <?php

                /**
                 * @small
                 */
                class Test extends TestCase
                {
                }

                abstract class Test2 extends TestCase
                {
                }

                class FooBar
                {
                }

                /**
                 * @small
                 */
                class Test3 extends TestCase
                {
                }

                EOD,
            <<<'EOD'
                <?php

                class Test extends TestCase
                {
                }

                abstract class Test2 extends TestCase
                {
                }

                class FooBar
                {
                }

                class Test3 extends TestCase
                {
                }

                EOD,
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'it adds a docblock above when there is an attribute' => [
            <<<'EOD'
                <?php

                            /**
                             * @small
                             */
                            #[SimpleTest]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
            <<<'EOD'
                <?php

                            #[SimpleTest]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
        ];

        yield 'it adds the internal tag along other tags when there is an attribute' => [
            <<<'EOD'
                <?php

                            /**
                             * @coversNothing
                             * @small
                             */
                            #[SimpleTest]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
            <<<'EOD'
                <?php

                            /**
                             * @coversNothing
                             */
                            #[SimpleTest]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
        ];

        yield 'it adds a docblock above when there are attributes' => [
            <<<'EOD'
                <?php

                            /**
                             * @small
                             */
                            #[SimpleTest]
                            #[Deprecated]
                            #[Annotated]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
            <<<'EOD'
                <?php

                            #[SimpleTest]
                            #[Deprecated]
                            #[Annotated]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
        ];

        yield 'it adds the internal tag along other tags when there are attributes' => [
            <<<'EOD'
                <?php

                            /**
                             * @coversNothing
                             * @small
                             */
                            #[SimpleTest]
                            #[Deprecated]
                            #[Annotated]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
            <<<'EOD'
                <?php

                            /**
                             * @coversNothing
                             */
                            #[SimpleTest]
                            #[Deprecated]
                            #[Annotated]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
        ];
    }
}
