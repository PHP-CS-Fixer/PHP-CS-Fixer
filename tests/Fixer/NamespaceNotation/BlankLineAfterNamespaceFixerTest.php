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

namespace PhpCsFixer\Tests\Fixer\NamespaceNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer
 */
final class BlankLineAfterNamespaceFixerTest extends AbstractFixerTestCase
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
        yield [
            <<<'EOD'
                <?php namespace A\B?>
                                <?php
                                    for($i=0; $i<10; ++$i) {echo $i;}
                EOD,
        ];

        yield [
            '<?php namespace A\B?>',
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                class C {}

                EOD,
            <<<'EOD'
                <?php
                namespace A\B;



                class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                class C {}

                EOD,
            <<<'EOD'
                <?php
                namespace A\B;
                class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                class C {}

                EOD,
            <<<'EOD'
                <?php
                namespace A\B;  class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                class C {}

                EOD,
            <<<'EOD'
                <?php
                namespace A\B;class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B {
                    class C {
                        public $foo;
                        private $bar;
                    }
                }

                EOD,
        ];

        yield [
            <<<EOD
                <?php\rnamespace A\\B;

                class C {}\r
                EOD,
            "<?php\rnamespace A\\B;\r\r\r\r\r\rclass C {}\r",
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                namespace\C\func();
                foo();

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Foo;

                EOD,
            <<<'EOD'
                <?php
                namespace Foo;




                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Foo;

                EOD,
            <<<'EOD'
                <?php
                namespace Foo;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Foo;

                ?>
                EOD,
            <<<'EOD'
                <?php
                namespace Foo;



                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    namespace Foo;

                    class Bar {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                  namespace Foo;

                      class Bar {}
                EOD,
            <<<'EOD'
                <?php
                  namespace Foo;
                      class Bar {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    namespace My\NS;

                    class X extends Y {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace My\NS; // comment

                class X extends Y {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace My\NS; /* comment */

                class X extends Y {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace My\NS; /*
                comment 1
                comment 2
                */

                class X extends Y {}
                EOD,
            <<<'EOD'
                <?php
                namespace My\NS; /*
                comment 1
                comment 2
                */
                class X extends Y {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace My\NS; /** comment */

                class X extends Y {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace My\NS; /**
                comment 1
                comment 2
                */

                class X extends Y {}
                EOD,
            <<<'EOD'
                <?php
                namespace My\NS; /**
                comment 1
                comment 2
                */
                class X extends Y {}
                EOD,
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php namespace A\\B;\r\n\r\nclass C {}",
            '<?php namespace A\\B;  class C {}',
        ];

        yield [
            "<?php namespace A\\B;\r\n\r\nclass C {}",
            "<?php namespace A\\B;\r\n\r\n\r\n\r\n\r\n\r\nclass C {}",
        ];
    }
}
