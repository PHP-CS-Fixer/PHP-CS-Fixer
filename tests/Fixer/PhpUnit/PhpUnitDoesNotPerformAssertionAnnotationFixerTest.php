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
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDoesNotPerformAssertionAnnotationFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitDoesNotPerformAssertionAnnotationFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitDoesNotPerformAssertionAnnotationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'default fix' => [
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Function does stuff
     */
    public function testFix1(): void
    {
        $this->expectNotToPerformAssertions();

        foo();
    }

    /** */
    public function testFix2(): void
    {
        $this->expectNotToPerformAssertions();

        foo();
    }
}',
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Function does stuff
     * @doesNotPerformAssertions
     */
    public function testFix1(): void
    {
        foo();
    }

    /** @doesNotPerformAssertions */
    public function testFix2(): void
    {
        foo();
    }
}',
        ];

        yield 'no fix' => [
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Function does stuff
     */
    public function testFix(): void
    {
        foo();
    }
}',
        ];

        yield 'removing comment' => [
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testFix(): void
    {
        $this->expectNotToPerformAssertions();

        foo();
    }
}',
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testFix(): void
    {
        foo();
    }
}',
        ];

        yield 'empty body' => [
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testFix(): void
    {
        $this->expectNotToPerformAssertions();

    }
}',
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testFix(): void
    {
    }
}',
        ];

        yield 'complex body' => [
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testFix(): void
    {
        $this->expectNotToPerformAssertions();

        // aaa
        foo();
        // bbb
    }
}',
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testFix(): void
    {
        // aaa
        foo();
        // bbb
    }
}',
        ];
    }
}
