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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\ConstantCaseFixer
 */
final class ConstantCaseFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            [
                '<?php if (true) if (false) if (null) {}',
                '<?php if (TRUE) if (FALSE) if (NULL) {}',
            ],
            [
                '<?php if (!true) if (!false) if (!null) {}',
                '<?php if (!TRUE) if (!FALSE) if (!NULL) {}',
            ],
            [
                '<?php if ($a == true) if ($a == false) if ($a == null) {}',
                '<?php if ($a == TRUE) if ($a == FALSE) if ($a == NULL) {}',
            ],
            [
                '<?php if ($a === true) if ($a === false) if ($a === null) {}',
                '<?php if ($a === TRUE) if ($a === FALSE) if ($a === NULL) {}',
            ],
            [
                '<?php if ($a != true) if ($a != false) if ($a != null) {}',
                '<?php if ($a != TRUE) if ($a != FALSE) if ($a != NULL) {}',
            ],
            [
                '<?php if ($a !== true) if ($a !== false) if ($a !== null) {}',
                '<?php if ($a !== TRUE) if ($a !== FALSE) if ($a !== NULL) {}',
            ],
            [
                '<?php if (true && true and true AND true || false or false OR false xor null XOR null) {}',
                '<?php if (TRUE && TRUE and TRUE AND TRUE || FALSE or FALSE OR FALSE xor NULL XOR NULL) {}',
            ],
            [
                '<?php /* foo */ true; /** bar */ false;',
                '<?php /* foo */ TRUE; /** bar */ FALSE;',
            ],
            ['<?php echo $null;'],
            ['<?php $x = False::foo();'],
            ['<?php namespace Foo\Null;'],
            ['<?php class Foo extends True {}'],
            ['<?php class Foo implements False {}'],
            ['<?php $foo instanceof True; $foo instanceof False; $foo instanceof Null;'],
            [
                '<?php
    class Foo
    {
        const TRUE = 1;
        const FALSE = true;
        const NULL = null;
    }',
                '<?php
    class Foo
    {
        const TRUE = 1;
        const FALSE = TRUE;
        const NULL = NULL;
    }',
            ],
            ['<?php $x = new /**/False?>'],
            ['<?php Null/**/::test();'],
            ['<?php True//
                                ::test();'],
            ['<?php class Foo { public function Bar() { $this->False = 1; $this->True = 2; $this->Null = 3; } }'],
        ];
    }

    /**
     * @dataProvider provideLowerGeneratedCases
     */
    public function testFixLowerGeneratedCases(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['case' => 'lower']);
        $this->doTest($expected, $input);
    }

    public static function provideLowerGeneratedCases(): iterable
    {
        foreach (['true', 'false', 'null'] as $case) {
            yield [
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', strtoupper($case)),
            ];

            yield [
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', ucfirst($case)),
            ];

            yield [sprintf('<?php $x = new %s;', ucfirst($case))];

            yield [sprintf('<?php $x = new %s;', strtoupper($case))];

            yield [sprintf('<?php $x = "%s story";', $case)];

            yield [sprintf('<?php $x = "%s";', $case)];
        }
    }

    /**
     * @dataProvider provideUpperGeneratedCases
     */
    public function testFixUpperGeneratedCases(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['case' => 'upper']);
        $this->doTest($expected, $input);
    }

    public static function provideUpperGeneratedCases(): iterable
    {
        foreach (['true', 'false', 'null'] as $case) {
            yield [
                sprintf('<?php $x = %s;', strtoupper($case)),
                sprintf('<?php $x = %s;', $case),
            ];

            yield [
                sprintf('<?php $x = %s;', strtoupper($case)),
                sprintf('<?php $x = %s;', ucfirst($case)),
            ];

            yield [sprintf('<?php $x = new %s;', ucfirst($case))];

            yield [sprintf('<?php $x = new %s;', strtoupper($case))];

            yield [sprintf('<?php $x = "%s story";', $case)];

            yield [sprintf('<?php $x = "%s";', $case)];
        }
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

    public static function provideFix80Cases(): iterable
    {
        yield ' nullsafe operator' => ['<?php class Foo { public function Bar() { return $this?->False; } }'];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'final class const' => [
            '<?php
                class Foo
                {
                    final const TRUE = 1;
                    public final const FALSE = true;
                    final public const NULL = null;
                }
            ',
            '<?php
                class Foo
                {
                    final const TRUE = 1;
                    public final const FALSE = TRUE;
                    final public const NULL = NULL;
                }
            ',
        ];

        yield 'enum and switch' => [
            '<?php
                enum Foo
                {
                    case True;
                    case False;
                    case Null;

                    public function methodWithSwitch(mixed $value): void
                    {
                        switch ($value) {
                            case true:
                            case false:
                            case null:
                                break;
                        }
                    }
                }
            ',
            '<?php
                enum Foo
                {
                    case True;
                    case False;
                    case Null;

                    public function methodWithSwitch(mixed $value): void
                    {
                        switch ($value) {
                            case TRUE:
                            case FALSE:
                            case NULL:
                                break;
                        }
                    }
                }
            ',
        ];

        yield 'enum' => [
            '<?php
                $y = false;
                enum Foo: string { case FALSE = "false"; }
                $x = true;
            ',
            '<?php
                $y = FALSE;
                enum Foo: string { case FALSE = "false"; }
                $x = TRUE;
            ',
        ];
    }
}
