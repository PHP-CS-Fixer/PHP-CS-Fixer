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
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer>
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitFqcnAnnotationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOF'
                <?php
                /**
                 * @covers \Foo
                 * @covers ::fooMethod
                 * @coversDefaultClass \Bar
                 */
                class FooTest extends TestCase {
                    /**
                     * @ExpectedException Value
                     * @expectedException \X
                     * @expectedException
                     * @expectedException \Exception
                         * @expectedException \Some\Exception\ClassName
                 * @expectedExceptionCode 123
                     * @expectedExceptionMessage Foo bar
                     *
                     * @uses \Baz
                     * @uses \selfieGenerator
                     * @uses self::someFunction
                     * @uses static::someOtherFunction
                     */
                }
                EOF,
            <<<'EOF'
                <?php
                /**
                 * @covers Foo
                 * @covers ::fooMethod
                 * @coversDefaultClass Bar
                 */
                class FooTest extends TestCase {
                    /**
                     * @ExpectedException Value
                     * @expectedException X
                     * @expectedException
                     * @expectedException \Exception
                         * @expectedException Some\Exception\ClassName
                 * @expectedExceptionCode 123
                     * @expectedExceptionMessage Foo bar
                     *
                     * @uses Baz
                     * @uses selfieGenerator
                     * @uses self::someFunction
                     * @uses static::someOtherFunction
                     */
                }
                EOF,
        ];

        yield [
            '<?php
class Foo {
    /**
     * @expectedException Some\Exception\ClassName
     * @covers Foo
     * @uses Baz
     * @uses self::someFunction
     */
}
',
        ];
    }
}
