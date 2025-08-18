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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineMultilineCommentFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineMultilineCommentFixer>
 */
final class PhpdocSingleLineMultilineCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string|null}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'single @return tag' => [
            '<?php
/** @return string */
function foo()
{
    return "bar";
}
',
            '<?php
/**
 * @return string
 */
function foo()
{
    return "bar";
}
',
        ];

        yield 'single @param tag' => [
            '<?php
/** @param string $name */
function setName($name)
{
    $this->name = $name;
}
',
            '<?php
/**
 * @param string $name
 */
function setName($name)
{
    $this->name = $name;
}
',
        ];

        yield 'single property @var tag' => [
            '<?php
class Test {
    /** @var string */
    public $name;
}
',
            '<?php
class Test {
    /**
     * @var string
     */
    public $name;
}
',
        ];

        yield 'single variable @var tag' => [
            '<?php
/** @var string */
$name = "Foo";
',
            '<?php
/**
 * @var string
 */
$name = "Foo";
',
        ];

        yield 'multiline with description should not be changed' => [
            '<?php
/**
 * This is a function description.
 *
 * @return string
 */
function foo()
{
    return "bar";
}
',
        ];

        yield 'multiline with multiple tags should not be changed' => [
            '<?php
/**
 * @param string $name
 * @return void
 */
function setName($name)
{
    $this->name = $name;
}
',
        ];

        yield 'already single line should not be changed' => [
            '<?php
/** @return string */
function foo()
{
    return "bar";
}
',
        ];

        yield 'single tag with extra whitespace' => [
            '<?php
/** @return string */
function foo()
{
    return "bar";
}
',
            '<?php
/**
 *
 * @return string
 *
 */
function foo()
{
    return "bar";
}
',
        ];

        yield 'single tag with complex type' => [
            '<?php
/** @param array<string, int> $data */
function process($data)
{
    // ...
}
',
            '<?php
/**
 * @param array<string, int> $data
 */
function process($data)
{
    // ...
}
',
        ];
    }
}
