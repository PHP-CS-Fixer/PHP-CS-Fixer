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
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractPhpUnitFixer
 * @covers \PhpCsFixer\Fixer\DocBlockAnnotationTrait
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitTestClassRequiresCoversFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'already with annotation: @covers' => [
            '<?php
                    /**
                     * @covers Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'already with annotation: @coversDefaultClass' => [
            '<?php
                    /**
                     * @coversDefaultClass
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'without docblock #1' => [
            '<?php

                    /**
                     * @coversNothing
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php

                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'without docblock #2 (class is final)' => [
            '<?php

                    /**
                     * @coversNothing
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php

                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'without docblock #2 (class is abstract)' => [
            '<?php
                    abstract class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'with docblock but annotation is missing' => [
            '<?php

                    /**
                     * Description.
                     *
                     * @since v2.2
                     * @coversNothing
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php

                    /**
                     * Description.
                     *
                     * @since v2.2
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'with one-line docblock but annotation is missing' => [
            '<?php

                    /**
                     * Description.
                     * @coversNothing
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php

                    /** Description. */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'with 2-lines docblock but annotation is missing #1' => [
            '<?php

                    /** Description.
                     * @coversNothing
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php

                    /** Description.
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'with 2-lines docblock but annotation is missing #2' => [
            '<?php

                    /**
                     * @coversNothing
                     * Description. */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php

                    /**
                     * Description. */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'with comment instead of docblock' => [
            '<?php
                    /*
                     * @covers Foo
                     */
                    /**
                     * @coversNothing
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php
                    /*
                     * @covers Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];

        yield 'not a test class' => [
            '<?php

                    class Foo {}
                ',
        ];

        yield 'multiple classes in one file' => [
            '<?php /** */

                    use \PHPUnit\Framework\TestCase;

                    /**
                     * Foo
                     * @coversNothing
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}

                    class Bar {}

                    /**
                     * @coversNothing
                     */
                    class Baz1 extends PHPUnit_Framework_TestCase {}

                    /**
                     * @coversNothing
                     */
                    class Baz2 extends \PHPUnit_Framework_TestCase {}

                    /**
                     * @coversNothing
                     */
                    class Baz3 extends \PHPUnit\Framework\TestCase {}

                    /**
                     * @coversNothing
                     */
                    class Baz4 extends TestCase {}
                ',
            '<?php /** */

                    use \PHPUnit\Framework\TestCase;

                    /**
                     * Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}

                    class Bar {}

                    class Baz1 extends PHPUnit_Framework_TestCase {}

                    class Baz2 extends \PHPUnit_Framework_TestCase {}

                    class Baz3 extends \PHPUnit\Framework\TestCase {}

                    class Baz4 extends TestCase {}
                ',
        ];

        yield [
            '<?php /* comment */

/**
 * @coversNothing
 */
class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php /* comment */class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
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
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            '<?php

                    /**
                     * @coversNothing
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            '<?php

                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'already with attribute CoversClass' => [
            <<<'PHP'
                <?php
                #[PHPUnit\Framework\Attributes\CoversClass(Foo::class)]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with attribute CoversNothing' => [
            <<<'PHP'
                <?php
                #[PHPUnit\Framework\Attributes\CoversNothing]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with attribute CoversNothing with leading slash' => [
            <<<'PHP'
                <?php
                #[\PHPUnit\Framework\Attributes\CoversNothing]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with imported attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\TestCase;
                use PHPUnit\Framework\Attributes\CoversClass;
                #[CoversClass(Foo::class)]
                class FooTest extends TestCase {}
                PHP,
        ];

        yield 'already with partially imported attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes;
                #[Attributes\CoversClass(Foo::class)]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with aliased attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes\CoversClass as PHPUnitCoversClass;
                #[PHPUnitCoversClass(Foo::class)]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with partially aliased attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes as PHPUnitAttributes;
                #[PHPUnitAttributes\CoversClass(Foo::class)]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'with attribute from different namespace' => [
            <<<'PHP'
                <?php
                use Foo\CoversClass;
                use PHPUnit\Framework\Attributes\CoversClass as PHPUnitCoversClass;
                /**
                 * @coversNothing
                 */
                #[CoversClass(Foo::class)]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
            <<<'PHP'
                <?php
                use Foo\CoversClass;
                use PHPUnit\Framework\Attributes\CoversClass as PHPUnitCoversClass;
                #[CoversClass(Foo::class)]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'with attribute on final class' => [
            <<<'PHP'
                <?php
                #[PHPUnit\Framework\Attributes\CoversNothing]
                final class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with attribute CoversMethod' => [
            <<<'PHP'
                <?php
                #[PHPUnit\Framework\Attributes\CoversMethod(Foo::class, 'bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with imported CoversMethod attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\TestCase;
                use PHPUnit\Framework\Attributes\CoversMethod;
                #[CoversMethod(Foo::class, 'bar')]
                class FooTest extends TestCase {}
                PHP,
        ];

        yield 'already with partially imported CoversMethod attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes;
                #[Attributes\CoversMethod(Foo::class, 'bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with aliased CoversMethod attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes\CoversMethod as PHPUnitCoversMethod;
                #[PHPUnitCoversMethod(Foo::class, 'bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with partially aliased CoversMethod attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes as PHPUnitAttributes;
                #[PHPUnitAttributes\CoversMethod(Foo::class, 'bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with attribute CoversFunction' => [
            <<<'PHP'
                <?php
                #[PHPUnit\Framework\Attributes\CoversFunction('bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with imported CoversFunction attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\TestCase;
                use PHPUnit\Framework\Attributes\CoversFunction;
                #[CoversFunction('bar')]
                class FooTest extends TestCase {}
                PHP,
        ];

        yield 'already with partially imported CoversFunction attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes;
                #[Attributes\CoversFunction('bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with aliased CoversFunction attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes\CoversFunction as PHPUnitCoversFunction;
                #[PHPUnitCoversFunction('bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with partially aliased CoversFunction attribute' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes as PHPUnitAttributes;
                #[PHPUnitAttributes\CoversFunction('bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'already with attribute CoversTrait' => [
            <<<'PHP'
                <?php
                use PHPUnit\Framework\Attributes\CoversTrait;
                #[CoversTrait('Bar')]
                class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield 'without docblock #2 (class is final)' => [
            '<?php

                /**
                 * @coversNothing
                 */
                readonly final class BarTest extends \PHPUnit_Framework_TestCase {}
            ',
            '<?php

                readonly final class BarTest extends \PHPUnit_Framework_TestCase {}
            ',
        ];

        yield 'without docblock #2 (class is abstract)' => [
            '<?php
                    abstract readonly class FooTest extends \PHPUnit_Framework_TestCase {}
            ',
            null,
        ];

        yield 'with attribute on readonly class' => [
            <<<'PHP'
                <?php
                #[PHPUnit\Framework\Attributes\CoversNothing]
                readonly class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];

        yield 'with attribute on final readonly class' => [
            <<<'PHP'
                <?php
                #[PHPUnit\Framework\Attributes\CoversNothing]
                final readonly class FooTest extends \PHPUnit_Framework_TestCase {}
                PHP,
        ];
    }
}
