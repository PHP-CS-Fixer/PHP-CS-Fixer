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
     * @dataProvider provideFixAlphaCases
     */
    public function testFixAlpha(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixAlphaCases(): array
    {
        return [
            'single' => [
                '<?php class T implements A {}',
            ],
            'multiple' => [
                '<?php class T implements A, B, C {}',
                '<?php class T implements C, A, B {}',
            ],
            'newlines' => [
                "<?php class T implements\nB,\nC\n{}",
                "<?php class T implements\nC,\nB\n{}",
            ],
            'newlines and comments' => [
                "<?php class T implements\n// Here's A\nA,\n// Here's B\nB\n{}",
                "<?php class T implements\n// Here's B\nB,\n// Here's A\nA\n{}",
            ],
            'no whitespace' => [
                '<?php class T implements/*An*/AnInterface/*end*/,/*Second*/SecondInterface{}',
                '<?php class T implements/*Second*/SecondInterface,/*An*/AnInterface/*end*/{}',
            ],
            'FQCN' => [
                '<?php class T implements \F\Q\C\N, \F\Q\I\N {}',
                '<?php class T implements \F\Q\I\N, \F\Q\C\N {}',
            ],
            'mixed' => [
                '<?php class T implements \F\Q\C\N, Partially\Q\C\N, /* Who mixes these? */ UnNamespaced {}',
                '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            ],
            'multiple in file' => [
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
            ],
            'interface extends' => [
                '<?php interface T extends A, B, C {}',
                '<?php interface T extends C, A, B {}',
            ],
            'nested anonymous classes' => [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixAlphaDescendCases
     */
    public function testFixAlphaDescend(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND]);
        $this->doTest($expected, $input);
    }

    public static function provideFixAlphaDescendCases(): array
    {
        return [
            'single' => [
                '<?php class T implements A {}',
            ],
            'multiple' => [
                '<?php class T implements C, B, A {}',
                '<?php class T implements C, A, B {}',
            ],
            'mixed' => [
                '<?php class T implements /* Who mixes these? */ UnNamespaced, Partially\Q\C\N, \F\Q\C\N {}',
                '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixLengthCases
     */
    public function testFixLength(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH]);
        $this->doTest($expected, $input);
    }

    public static function provideFixLengthCases(): array
    {
        return [
            'single' => [
                '<?php class A implements A {}',
            ],
            'multiple' => [
                '<?php class A implements Short, Longer, MuchLonger {}',
                '<?php class A implements MuchLonger, Short, Longer {}',
            ],
            'mixed' => [
                '<?php class T implements \F\Q\C\N, /* Who mixes these? */ UnNamespaced, Partially\Q\C\N {}',
                '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            ],
            'normalized' => [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixLengthDescendCases
     */
    public function testFixLengthDescend(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            OrderedInterfacesFixer::OPTION_ORDER => OrderedInterfacesFixer::ORDER_LENGTH,
            OrderedInterfacesFixer::OPTION_DIRECTION => OrderedInterfacesFixer::DIRECTION_DESCEND,
        ]);
        $this->doTest($expected, $input);
    }

    public static function provideFixLengthDescendCases(): array
    {
        return [
            'single' => [
                '<?php class A implements A {}',
            ],
            'multiple' => [
                '<?php class A implements MuchLonger, Longer, Short {}',
                '<?php class A implements MuchLonger, Short, Longer {}',
            ],
            'mixed' => [
                '<?php class T implements Partially\Q\C\N, /* Who mixes these? */ UnNamespaced, \F\Q\C\N {}',
                '<?php class T implements /* Who mixes these? */ UnNamespaced, \F\Q\C\N, Partially\Q\C\N {}',
            ],
            'normalized' => [
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
            ],
        ];
    }
}
