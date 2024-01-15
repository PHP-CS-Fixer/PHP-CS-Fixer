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
 * @author Gert de Pagter <BackEndTea@gmail.com>
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer
 */
final class PhpUnitInternalClassFixerTest extends AbstractFixerTestCase
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

        yield 'It marks a test class as internal' => [
            <<<'EOD'
                <?php

                /**
                 * @internal
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

        yield 'It adds an internal tag to a class that already has a doc block' => [
            <<<'EOD'
                <?php

                /**
                 * @coversNothing
                 * @internal
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

        yield 'It does not change a class that is already internal' => [
            <<<'EOD'
                <?php

                /**
                 * @internal
                 */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It does not change a class that is already internal and has other annotations' => [
            <<<'EOD'
                <?php

                /**
                 * @author me
                 * @coversNothing
                 * @internal
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
                     * @internal
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
                     * @author me again
                     *
                     *
                     * @covers \Other\Class
                     * @internal
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
                     * @author me again
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

        yield 'It works for tab ident' => [
            <<<'EOD'
                <?php

                if (class_exists("Foo\Bar")) {
                	/**
                	 * @author me again
                	 *
                	 *
                	 * @covers \Other\Class
                	 * @internal
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
                	 * @author me again
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

        yield 'It always adds @internal to the bottom of the doc block' => [
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
                 * @internal
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

        yield 'It does not change a class with a single line internal doc block' => [
            <<<'EOD'
                <?php

                /** @internal */
                class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'It adds an internal tag to a class that already has a one linedoc block' => [
            <<<'EOD'
                <?php

                /**
                 * @coversNothing
                 * @internal
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

        yield 'By default it will not mark an abstract class as internal' => [
            <<<'EOD'
                <?php

                abstract class Test extends TestCase
                {
                }

                EOD,
        ];

        yield 'If abstract is added as an option, abstract classes will be marked internal' => [
            <<<'EOD'
                <?php

                /**
                 * @internal
                 */
                abstract class Test extends TestCase
                {
                }

                EOD,
            <<<'EOD'
                <?php

                abstract class Test extends TestCase
                {
                }

                EOD,
            [
                'types' => ['abstract'],
            ],
        ];

        yield 'If final is not added as an option, final classes will not be marked internal' => [
            <<<'EOD'
                <?php

                final class Test extends TestCase
                {
                }

                EOD,
            null,
            [
                'types' => ['abstract'],
            ],
        ];

        yield 'If normal is not added as an option, normal classes will not be marked internal' => [
            <<<'EOD'
                <?php

                class Test extends TestCase
                {
                }

                EOD,
            null,
            [
                'types' => ['abstract'],
            ],
        ];

        yield 'It works correctly with multiple classes in one file, even when one of them is not allowed' => [
            <<<'EOD'
                <?php

                /**
                 * @internal
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
                 * @internal
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

                abstract class Test2 extends TestCase
                {
                }

                class FooBar
                {
                }

                class Test extends TestCase
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
                             * @internal
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
                             * @internal
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
                             * @internal
                             */
                            #[SimpleTest]
                            #[Annotated]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
            <<<'EOD'
                <?php

                            #[SimpleTest]
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
                             * @internal
                             */
                            #[SimpleTest]
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
                            #[Annotated]
                            class Test extends TestCase
                            {
                            }
                EOD."\n            ",
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int|string, array{string, 1: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield 'If final is not added as an option, final classes will not be marked internal' => [
            <<<'EOD'
                <?php
                final readonly class Test extends TestCase
                {}

                EOD,
            null,
            [
                'types' => ['normal'],
            ],
        ];

        yield [
            <<<'EOD'
                <?php

                /**
                 * @internal
                 */
                readonly final class Test extends TestCase {}
                EOD,
            <<<'EOD'
                <?php
                readonly final class Test extends TestCase {}
                EOD,
        ];
    }
}
