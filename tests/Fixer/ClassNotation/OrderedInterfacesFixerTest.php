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

use PhpCsFixer\Fixer\ClassNotation\OrderedInterfacesFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\OrderedInterfacesFixer
 */
final class OrderedInterfacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param array<string, mixed> $configuration
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'single' => [
            '<?php class T implements A {}',
        ];

        yield 'multiple' => [
            '<?php class T implements A, B, C {}',
            '<?php class T implements C, A, B {}',
        ];

        yield 'newlines' => [
            "<?php class T implements\nB,\nC\n{}",
            "<?php class T implements\nC,\nB\n{}",
        ];

        yield 'newlines and comments' => [
            "<?php class T implements\n// Here's A\nA,\n// Here's B\nB\n{}",
            "<?php class T implements\n// Here's B\nB,\n// Here's A\nA\n{}",
        ];

        yield 'no whitespace' => [
            '<?php class T implements/*An*/AnInterface,/*Second*/SecondInterface/*end*/{}',
            '<?php class T implements/*Second*/SecondInterface,/*An*/AnInterface/*end*/{}',
        ];

        yield 'FQCN' => [
            '<?php class T implements \F\Q\C\N, \F\Q\I\N {}',
            '<?php class T implements \F\Q\I\N, \F\Q\C\N {}',
        ];

        yield 'mixed' => [
            '<?php class T implements \F\Q\C\N, Partially\Q\C\N, /* Who mixes these? */ UnNamespaced {}',
            '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
        ];

        yield 'multiple in file' => [
            '<?php
                    class A1 implements A\B\C, Z\X\Y {}
                    class B2 implements A\B, Z\X {}
                    class C3 implements A, Z\X {}
                    class D4 implements A\B, B\V, Z\X\V {}
                    class E5 implements U\B, X\B, Y\V, Z\X\V {}
                ',
            '<?php
                    class A1 implements Z\X\Y, A\B\C {}
                    class B2 implements Z\X, A\B {}
                    class C3 implements Z\X, A {}
                    class D4 implements Z\X\V, B\V, A\B {}
                    class E5 implements Z\X\V, Y\V, X\B, U\B {}
                ',
        ];

        yield 'interface extends' => [
            '<?php interface T extends A, B, C {}',
            '<?php interface T extends C, A, B {}',
        ];

        yield 'nested anonymous classes' => [
            '<?php
                    class T implements A, B, C
                    {
                        public function getAnonymousClassObject()
                        {
                            return new class() implements C, D, E
                            {
                                public function getNestedAnonymousClassObject()
                                {
                                    return new class() implements E, F, G {};
                                }
                            };
                        }
                    }
                ',
            '<?php
                    class T implements C, A, B
                    {
                        public function getAnonymousClassObject()
                        {
                            return new class() implements E, C, D
                            {
                                public function getNestedAnonymousClassObject()
                                {
                                    return new class() implements F, G, E {};
                                }
                            };
                        }
                    }
                ',
        ];

        yield 'single line after interfaces' => [
            '<?php
                class Foo implements B, C //, A
                {}
            ',
            '<?php
                class Foo implements C, B //, A
                {}
            ',
        ];

        yield 'descend single' => [
            '<?php class T implements A {}',
            null,
            [OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND],
        ];

        yield 'descend multiple' => [
            '<?php class T implements C, B, A {}',
            '<?php class T implements C, A, B {}',
            [OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND],
        ];

        yield 'descend mixed' => [
            '<?php class T implements /* Who mixes these? */ UnNamespaced, Partially\Q\C\N, \F\Q\C\N {}',
            '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            [OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND],
        ];

        yield 'length single' => [
            '<?php class A implements A {}',
            null,
            [OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH],
        ];

        yield 'length multiple' => [
            '<?php class A implements Short, Longer, MuchLonger {}',
            '<?php class A implements MuchLonger, Short, Longer {}',
            [OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH],
        ];

        yield 'length mixed' => [
            '<?php class T implements \F\Q\C\N, /* Who mixes these? */ UnNamespaced, Partially\Q\C\N {}',
            '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            [OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH],
        ];

        yield 'length normalized' => [
            '<?php
                    class A implements
                         ABCDE,
                         A\B\C\D
                    { /* */ }
                ',
            '<?php
                    class A implements
                         A\B\C\D,
                         ABCDE
                    { /* */ }
                ',
            [OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH],
        ];

        yield 'length, descend single' => [
            '<?php class A implements A {}',
            null,
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH,
                OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND,
            ],
        ];

        yield 'length, descend multiple' => [
            '<?php class A implements MuchLonger, Longer, Short {}',
            '<?php class A implements MuchLonger, Short, Longer {}',
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH,
                OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND,
            ],
        ];

        yield 'length, descend mixed' => [
            '<?php class T implements Partially\Q\C\N, /* Who mixes these? */ UnNamespaced, \F\Q\C\N {}',
            '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH,
                OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND,
            ],
        ];

        yield 'length, descend normalized' => [
            '<?php
                    class A implements
                         A\B\C\D,
                         ABCDE
                    { /* */ }
                ',
            '<?php
                    class A implements
                         ABCDE,
                         A\B\C\D
                    { /* */ }
                ',
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH,
                OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND,
            ],
        ];

        yield 'case sensitive single' => [
            '<?php class A implements A {}',
            null,
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_ALPHA,
                'case_sensitive' => true,
            ],
        ];

        yield 'alpha multiple' => [
            '<?php class A implements AA, Aaa, FF, Fff {}',
            '<?php class A implements Fff, Aaa, FF, AA {}',
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_ALPHA,
                'case_sensitive' => true,
            ],
        ];

        yield 'alpha mixed' => [
            '<?php class T implements \F\Q\C\N, Partially\Q\C\N, /* Who mixes these? */ UnNamespaced {}',
            '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_ALPHA,
                'case_sensitive' => true,
            ],
        ];

        yield 'alpha normalized' => [
            '<?php
                    class A implements
                         A\B\C\D,
                         AAa\B\C\D,
                         ABCDE,
                         Aaa\B\C\D
                    { /* */ }
                ',
            '<?php
                    class A implements
                         Aaa\B\C\D,
                         AAa\B\C\D,
                         ABCDE,
                         A\B\C\D
                    { /* */ }
                ',
            [
                OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_ALPHA,
                'case_sensitive' => true,
            ],
        ];
    }
}
