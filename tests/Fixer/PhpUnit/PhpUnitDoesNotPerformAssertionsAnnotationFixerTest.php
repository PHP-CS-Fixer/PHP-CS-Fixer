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

use PhpCsFixer\Fixer\PhpUnit\PhpUnitDoesNotPerformAssertionsAnnotationFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDoesNotPerformAssertionsAnnotationFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitDoesNotPerformAssertionsAnnotationFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(PhpUnitDoesNotPerformAssertionsAnnotationFixer::class)]
final class PhpUnitDoesNotPerformAssertionsAnnotationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'annotation alone' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    public function testFoo(): void
                    {
                        $this->expectNotToPerformAssertions();

                        foo();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];

        yield 'other annotations and description are kept' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * Checks that foo does not throw.
                     *
                     * @group foo
                     */
                    public function testFoo(): void
                    {
                        $this->expectNotToPerformAssertions();

                        foo();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * Checks that foo does not throw.
                     *
                     * @doesNotPerformAssertions
                     * @group foo
                     */
                    public function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];

        yield 'single line docblock' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /** */
                    public function testFoo(): void
                    {
                        $this->expectNotToPerformAssertions();

                        foo();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /** @doesNotPerformAssertions */
                    public function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];

        yield 'empty body' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    public function testFoo(): void
                    {
                        $this->expectNotToPerformAssertions();
                    }

                    /**
                     */
                    public function testBar(): void {
                        $this->expectNotToPerformAssertions();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testFoo(): void
                    {
                    }

                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testBar(): void {}
                }
                PHP,
        ];

        yield 'body on the line of the brace' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    public function testFoo(): void {
                        $this->expectNotToPerformAssertions();

                        foo(); }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testFoo(): void { foo(); }
                }
                PHP,
        ];

        yield 'several methods and modifiers' => [
            <<<'PHP'
                <?php
                class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    final public function testFoo()
                    {
                        $this->expectNotToPerformAssertions();

                        foo();
                    }

                    /**
                     * @group bar
                     */
                    public function testBar()
                    {
                        bar();
                    }

                    /**
                     */
                    function testBaz()
                    {
                        $this->expectNotToPerformAssertions();

                        baz();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    final public function testFoo()
                    {
                        foo();
                    }

                    /**
                     * @group bar
                     */
                    public function testBar()
                    {
                        bar();
                    }

                    /**
                     * @doesNotPerformAssertions
                     */
                    function testBaz()
                    {
                        baz();
                    }
                }
                PHP,
        ];

        yield 'annotation repeated' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    public function testFoo(): void
                    {
                        $this->expectNotToPerformAssertions();

                        foo();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     * @doesNotPerformAssertions
                     */
                    public function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];

        yield 'method already calling expectNotToPerformAssertions' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    public function testFoo(): void
                    {
                        foo();
                        $this->expectNotToPerformAssertions();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testFoo(): void
                    {
                        foo();
                        $this->expectNotToPerformAssertions();
                    }
                }
                PHP,
        ];

        yield 'closure inside the test is not a method' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    public function testFoo(): void
                    {
                        $this->expectNotToPerformAssertions();

                        array_map(function ($item) { return $item; }, []);
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testFoo(): void
                    {
                        array_map(function ($item) { return $item; }, []);
                    }
                }
                PHP,
        ];

        yield 'abstract method is left untouched' => [
            <<<'PHP'
                <?php
                abstract class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    abstract public function testFoo(): void;
                }
                PHP,
        ];

        yield 'static method is left untouched' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public static function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];

        yield 'method without docblock is left untouched' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    // @doesNotPerformAssertions
                    public function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];

        yield 'class level annotation is left untouched' => [
            <<<'PHP'
                <?php
                /**
                 * @doesNotPerformAssertions
                 */
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    public function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];

        yield 'class that is not a test case is left untouched' => [
            <<<'PHP'
                <?php
                final class Foo
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testFoo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP >= 8.0.0
     */
    #[DataProvider('provideFix80Cases')]
    #[RequiresPhp('>= 8.0.0')]
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'attributes between the docblock and the method' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    #[\PHPUnit\Framework\Attributes\Test]
                    #[\PHPUnit\Framework\Attributes\Group('foo')]
                    public function foo(): void
                    {
                        $this->expectNotToPerformAssertions();

                        foo();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    #[\PHPUnit\Framework\Attributes\Test]
                    #[\PHPUnit\Framework\Attributes\Group('foo')]
                    public function foo(): void
                    {
                        foo();
                    }
                }
                PHP,
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    #[DataProvider('provideWithWhitespacesConfigCases')]
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $expected = str_replace(['    ', "\n"], ["\t", "\r\n"], $expected);
        if (null !== $input) {
            $input = str_replace(['    ', "\n"], ["\t", "\r\n"], $input);
        }

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield 'tabs and CRLF' => [
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     */
                    public function testFoo()
                    {
                        $this->expectNotToPerformAssertions();

                        foo();
                    }

                    /**
                     */
                    public function testBar() {
                        $this->expectNotToPerformAssertions();
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                final class MyTest extends \PHPUnit\Framework\TestCase
                {
                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testFoo()
                    {
                        foo();
                    }

                    /**
                     * @doesNotPerformAssertions
                     */
                    public function testBar() {}
                }
                PHP,
        ];
    }
}
