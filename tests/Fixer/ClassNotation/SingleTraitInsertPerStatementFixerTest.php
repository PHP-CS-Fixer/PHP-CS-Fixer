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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer
 */
final class SingleTraitInsertPerStatementFixerTest extends AbstractFixerTestCase
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
        yield 'simple' => [
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo;use Bar;
                }

                EOD,
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo, Bar;
                }

                EOD,
        ];

        yield 'simple I' => [
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo;use Bar;
                }

                EOD,
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo,Bar;
                }

                EOD,
        ];

        yield 'simple II' => [
            <<<'EOD'
                <?php
                use Foo\Bar, Foo\Bar2; // do not touch

                final class Example
                {
                    use Foo;use Bar ;
                }

                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar, Foo\Bar2; // do not touch

                final class Example
                {
                    use Foo, Bar ;
                }

                EOD,
        ];

        yield 'simple III' => [
            <<<'EOD'
                <?php
                class Example
                {
                    use Foo;use Bar;

                    public function baz() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class Example
                {
                    use Foo, Bar;

                    public function baz() {}
                }

                EOD,
        ];

        yield 'multiple' => [
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo;
                    use Foo00;use Bar01;
                    use Foo10;use Bar11;use Bar110;
                    use Foo20;use Bar20;use Bar200;use Bar201;
                }

                EOD,
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo;
                    use Foo00, Bar01;
                    use Foo10, Bar11, Bar110;
                    use Foo20, Bar20, Bar200, Bar201;
                }

                EOD,
        ];

        yield 'multiple_multiline' => [
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo;
                    use Bar;
                    use Baz;
                }

                EOD,
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo,
                        Bar,
                        Baz;
                }

                EOD,
        ];

        yield 'multiple_multiline_with_comment' => [
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo;
                    use Bar;
                //        Bazz,
                    use Baz;
                }

                EOD,
            <<<'EOD'
                <?php
                final class Example
                {
                    use Foo,
                        Bar,
                //        Bazz,
                        Baz;
                }

                EOD,
        ];

        yield 'namespaces' => [
            <<<'EOD'
                <?php
                class Z
                {
                    use X\Y\Z0;use X\Y\Z0;use M;
                    use X\Y\Z1;use X\Y\Z1;
                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                class Z
                {
                    use X\Y\Z0, X\Y\Z0, M;
                    use X\Y\Z1, X\Y\Z1;
                }
                EOD."\n                ",
        ];

        yield 'comments' => [
            <<<'EOD'
                <?php
                class ZZ
                {#1
                use#2
                Z/* 2 */ #3
                #4
                ;#5
                #6
                use T#7
                #8
                ;#9
                #10
                }

                EOD,
            <<<'EOD'
                <?php
                class ZZ
                {#1
                use#2
                Z/* 2 */ #3
                #4
                ,#5
                #6
                T#7
                #8
                ;#9
                #10
                }

                EOD,
        ];

        yield 'two classes. same file' => [
            <<<'EOD'
                <?php
                namespace Foo;

                class Test1
                {
                    use A;use B; /** use A2, B2; */
                }

                ?>
                <?php

                class Test2
                {
                    use A1;use B1; # use A2, B2;
                }

                EOD,
            <<<'EOD'
                <?php
                namespace Foo;

                class Test1
                {
                    use A, B; /** use A2, B2; */
                }

                ?>
                <?php

                class Test2
                {
                    use A1, B1; # use A2, B2;
                }

                EOD,
        ];

        yield 'do not fix group' => [
            <<<'EOD'
                <?php
                                class Talker {
                    use A, B {
                        B::smallTalk insteadof A;
                        A::bigTalk insteadof B;
                    }
                }
                EOD,
        ];

        yield 'anonymous class' => [
            '<?php new class { use A;use B;}?>',
            '<?php new class { use A, B;}?>',
        ];
    }
}
