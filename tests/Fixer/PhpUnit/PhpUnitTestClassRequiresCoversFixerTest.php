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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractPhpUnitFixer
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer
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

    public static function provideFixCases(): iterable
    {
        yield 'already with annotation: @covers' => [
            <<<'EOD'
                <?php
                                    /**
                                     * @covers Foo
                                     */
                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'already with annotation: @coversDefaultClass' => [
            <<<'EOD'
                <?php
                                    /**
                                     * @coversDefaultClass
                                     */
                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'without docblock #1' => [
            <<<'EOD'
                <?php

                                    /**
                                     * @coversNothing
                                     */
                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php

                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'without docblock #2 (class is final)' => [
            <<<'EOD'
                <?php

                                    /**
                                     * @coversNothing
                                     */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php

                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'without docblock #2 (class is abstract)' => [
            <<<'EOD'
                <?php
                                    abstract class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'with docblock but annotation is missing' => [
            <<<'EOD'
                <?php

                                    /**
                                     * Description.
                                     *
                                     * @since v2.2
                                     * @coversNothing
                                     */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php

                                    /**
                                     * Description.
                                     *
                                     * @since v2.2
                                     */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'with one-line docblock but annotation is missing' => [
            <<<'EOD'
                <?php

                                    /**
                                     * Description.
                                     * @coversNothing
                                     */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php

                                    /** Description. */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'with 2-lines docblock but annotation is missing #1' => [
            <<<'EOD'
                <?php

                                    /** Description.
                                     * @coversNothing
                                     */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php

                                    /** Description.
                                     */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'with 2-lines docblock but annotation is missing #2' => [
            <<<'EOD'
                <?php

                                    /**
                                     * @coversNothing
                                     * Description. */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php

                                    /**
                                     * Description. */
                                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'with comment instead of docblock' => [
            <<<'EOD'
                <?php
                                    /*
                                     * @covers Foo
                                     */
                                    /**
                                     * @coversNothing
                                     */
                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    /*
                                     * @covers Foo
                                     */
                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];

        yield 'not a test class' => [
            <<<'EOD'
                <?php

                                    class Foo {}
                EOD."\n                ",
        ];

        yield 'multiple classes in one file' => [
            <<<'EOD'
                <?php /** */

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
                EOD."\n                ",
            <<<'EOD'
                <?php /** */

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
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php /* comment */

                /**
                 * @coversNothing
                 */
                class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            '<?php /* comment */class FooTest extends \PHPUnit_Framework_TestCase {}'."\n                ",
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

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php

                                    /**
                                     * @coversNothing
                                     */
                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
            <<<'EOD'
                <?php

                                    class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield 'without docblock #2 (class is final)' => [
            <<<'EOD'
                <?php

                                /**
                                 * @coversNothing
                                 */
                                readonly final class BarTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n            ",
            <<<'EOD'
                <?php

                                readonly final class BarTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n            ",
        ];

        yield 'without docblock #2 (class is abstract)' => [
            <<<'EOD'
                <?php
                                    abstract readonly class FooTest extends \PHPUnit_Framework_TestCase {}
                EOD."\n            ",
            null,
        ];
    }
}
