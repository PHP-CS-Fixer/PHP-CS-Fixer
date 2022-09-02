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

    public function provideFixCases(): array
    {
        return [
            'It does not change normal classes' => [
                '<?php

class Hello
{
}
',
            ],
            'It marks a test class as internal' => [
                '<?php

/**
 * @internal
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
            'It adds an internal tag to a class that already has a doc block' => [
                '<?php

/**
 * @coversNothing
 *
 * @internal
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
            'It does not change a class that is already internal' => [
                '<?php

/**
 * @internal
 */
class Test extends TestCase
{
}
',
            ],
            'It does not change a class that is already internal and has other annotations' => [
                '<?php

/**
 * @author me
 * @coversNothing
 * @internal
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
     * @internal
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
     * @author me again
     *
     *
     * @covers \Other\Class
     *
     * @internal
     */
    class Test Extends TestCase
    {
    }
}
',
                '<?php

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
',
            ],
            'It works for tab ident' => [
                '<?php

if (class_exists("Foo\Bar")) {
	/**
	 * @author me again
	 *
	 *
	 * @covers \Other\Class
	 *
	 * @internal
	 */
	class Test Extends TestCase
	{
	}
}
',
                '<?php

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
',
            ],
            'It always adds @internal to the bottom of the doc block' => [
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
 * @internal
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
            'It does not change a class with a single line internal doc block' => [
                '<?php

/** @internal */
class Test extends TestCase
{
}
',
            ],
            'It adds an internal tag to a class that already has a one linedoc block' => [
                '<?php

/**
 * @coversNothing
 *
 * @internal
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
            'By default it will not mark an abstract class as internal' => [
                '<?php

abstract class Test extends TestCase
{
}
',
            ],
            'If abstract is added as an option, abstract classes will be marked internal' => [
                '<?php

/**
 * @internal
 */
abstract class Test extends TestCase
{
}
',
                '<?php

abstract class Test extends TestCase
{
}
',
                [
                    'types' => ['abstract'],
                ],
            ],
            'If final is not added as an option, final classes will not be marked internal' => [
                '<?php

final class Test extends TestCase
{
}
',
                null,
                [
                    'types' => ['abstract'],
                ],
            ],
            'If normal is not added as an option, normal classes will not be marked internal' => [
                '<?php

class Test extends TestCase
{
}
',
                null,
                [
                    'types' => ['abstract'],
                ],
            ],
            'It works correctly with multiple classes in one file, even when one of them is not allowed' => [
                '<?php

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

class Test extends TestCase
{
}
',
            ],
        ];
    }
}
