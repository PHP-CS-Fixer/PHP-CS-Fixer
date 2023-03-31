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

    public static function provideFixCases(): array
    {
        return [
            'It does not change normal classes' => [
                '<?php

class Hello
{
}
',
            ],
            'It marks a test class as @small by default' => [
                '<?php

/**
 * @small
 */
class Test extends TestCase
{
}
',
                '<?php

class Test extends TestCase
{
}
',
            ],
            'It marks a test class as specified in the configuration' => [
                '<?php

/**
 * @large
 */
class Test extends TestCase
{
}
',
                '<?php

class Test extends TestCase
{
}
',
                ['group' => 'large'],
            ],
            'It adds an @small tag to a class that already has a doc block' => [
                '<?php

/**
 * @coversNothing
 *
 * @small
 */
class Test extends TestCase
{
}
',
                '<?php

/**
 * @coversNothing
 */
class Test extends TestCase
{
}
',
            ],
            'It does not change a class that is already @small' => [
                '<?php

/**
 * @small
 */
class Test extends TestCase
{
}
',
            ],
            'It does not change a class that is already @small and has other annotations' => [
                '<?php

/**
 * @author malukenho
 * @coversNothing
 * @large
 * @group large
 */
class Test extends TestCase
{
}
',
            ],
            'It works on other indentation levels' => [
                '<?php

if (class_exists("Foo\Bar")) {
    /**
     * @small
     */
    class Test Extends TestCase
    {
    }
}
',
                '<?php

if (class_exists("Foo\Bar")) {
    class Test Extends TestCase
    {
    }
}
',
            ],
            'It works on other indentation levels when the class has other annotations' => [
                '<?php

if (class_exists("Foo\Bar")) {
    /**
     * @author malukenho again
     *
     *
     * @covers \Other\Class
     *
     * @small
     */
    class Test Extends TestCase
    {
    }
}
',
                '<?php

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
',
            ],
            'It always adds @small to the bottom of the doc block' => [
                '<?php

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
 *
 * @small
 */
class Test extends TestCase
{
}
',
                '<?php

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
',
            ],
            'It does not change a class with a single line @{size} doc block' => [
                '<?php

/** @medium */
class Test extends TestCase
{
}
',
            ],
            'It adds an @small tag to a class that already has a one linedoc block' => [
                '<?php

/**
 * @coversNothing
 *
 * @small
 */
class Test extends TestCase
{
}
',
                '<?php

/** @coversNothing */
class Test extends TestCase
{
}
',
            ],
            'By default it will not mark an abstract class as @small' => [
                '<?php

abstract class Test
{
}
',
            ],
            'It works correctly with multiple classes in one file, even when one of them is not allowed' => [
                '<?php

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
',
                '<?php

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
',
            ],
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
     * @return iterable<string[]>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'it adds a docblock above when there is an attribute' => [
            '<?php

            /**
             * @small
             */
            #[SimpleTest]
            class Test extends TestCase
            {
            }
            ',
            '<?php

            #[SimpleTest]
            class Test extends TestCase
            {
            }
            ',
        ];

        yield 'it adds the internal tag along other tags when there is an attribute' => [
            '<?php

            /**
             * @coversNothing
             *
             * @small
             */
            #[SimpleTest]
            class Test extends TestCase
            {
            }
            ',
            '<?php

            /**
             * @coversNothing
             */
            #[SimpleTest]
            class Test extends TestCase
            {
            }
            ',
        ];

        yield 'it adds a docblock above when there are attributes' => [
            '<?php

            /**
             * @small
             */
            #[SimpleTest]
            #[Deprecated]
            #[Annotated]
            class Test extends TestCase
            {
            }
            ',
            '<?php

            #[SimpleTest]
            #[Deprecated]
            #[Annotated]
            class Test extends TestCase
            {
            }
            ',
        ];

        yield 'it adds the internal tag along other tags when there are attributes' => [
            '<?php

            /**
             * @coversNothing
             *
             * @small
             */
            #[SimpleTest]
            #[Deprecated]
            #[Annotated]
            class Test extends TestCase
            {
            }
            ',
            '<?php

            /**
             * @coversNothing
             */
            #[SimpleTest]
            #[Deprecated]
            #[Annotated]
            class Test extends TestCase
            {
            }
            ',
        ];
    }
}
