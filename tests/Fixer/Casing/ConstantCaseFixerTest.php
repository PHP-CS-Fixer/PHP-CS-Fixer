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
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: null|string, 2?: array{syntax?: string}}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php if (true) if (false) if (null) {}',
            '<?php if (TRUE) if (FALSE) if (NULL) {}',
        ];

        yield [
            '<?php if (!true) if (!false) if (!null) {}',
            '<?php if (!TRUE) if (!FALSE) if (!NULL) {}',
        ];

        yield [
            '<?php if ($a == true) if ($a == false) if ($a == null) {}',
            '<?php if ($a == TRUE) if ($a == FALSE) if ($a == NULL) {}',
        ];

        yield [
            '<?php if ($a === true) if ($a === false) if ($a === null) {}',
            '<?php if ($a === TRUE) if ($a === FALSE) if ($a === NULL) {}',
        ];

        yield [
            '<?php if ($a != true) if ($a != false) if ($a != null) {}',
            '<?php if ($a != TRUE) if ($a != FALSE) if ($a != NULL) {}',
        ];

        yield [
            '<?php if ($a !== true) if ($a !== false) if ($a !== null) {}',
            '<?php if ($a !== TRUE) if ($a !== FALSE) if ($a !== NULL) {}',
        ];

        yield [
            '<?php if (true && true and true AND true || false or false OR false xor null XOR null) {}',
            '<?php if (TRUE && TRUE and TRUE AND TRUE || FALSE or FALSE OR FALSE xor NULL XOR NULL) {}',
        ];

        yield [
            '<?php /* foo */ true; /** bar */ false;',
            '<?php /* foo */ TRUE; /** bar */ FALSE;',
        ];

        yield ['<?php echo $null;'];

        yield ['<?php $x = False::foo();'];

        yield ['<?php namespace Foo\Null;'];

        yield ['<?php class Foo extends True {}'];

        yield ['<?php class Foo implements False {}'];

        yield ['<?php $foo instanceof True; $foo instanceof False; $foo instanceof Null;'];

        yield [
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
        ];

        yield ['<?php $x = new /**/False?>'];

        yield ['<?php Null/**/::test();'];

        yield ['<?php True//
                                ::test();'];

        yield ['<?php class Foo { public function Bar() { $this->False = 1; $this->True = 2; $this->Null = 3; } }'];

        foreach (['true', 'false', 'null'] as $case) {
            yield [
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', strtoupper($case)),
                ['case' => 'lower'],
            ];

            yield [
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', ucfirst($case)),
                ['case' => 'lower'],
            ];

            yield [
                sprintf('<?php $x = new %s;', ucfirst($case)),
                null,
                ['case' => 'lower'],
            ];

            yield [
                sprintf('<?php $x = new %s;', strtoupper($case)),
                null,
                ['case' => 'lower'],
            ];

            yield [
                sprintf('<?php $x = "%s story";', $case),
                null,
                ['case' => 'lower'],
            ];

            yield [
                sprintf('<?php $x = "%s";', $case),
                null,
                ['case' => 'lower'],
            ];
        }

        foreach (['true', 'false', 'null'] as $case) {
            yield [
                sprintf('<?php $x = %s;', strtoupper($case)),
                sprintf('<?php $x = %s;', $case),
                ['case' => 'upper'],
            ];

            yield [
                sprintf('<?php $x = %s;', strtoupper($case)),
                sprintf('<?php $x = %s;', ucfirst($case)),
                ['case' => 'upper'],
            ];

            yield [
                sprintf('<?php $x = new %s;', ucfirst($case)),
                null,
                ['case' => 'upper'],
            ];

            yield [
                sprintf('<?php $x = new %s;', strtoupper($case)),
                null,
                ['case' => 'upper'],
            ];

            yield [
                sprintf('<?php $x = "%s story";', $case),
                null,
                ['case' => 'upper'],
            ];

            yield [
                sprintf('<?php $x = "%s";', $case),
                null,
                ['case' => 'upper'],
            ];
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

    /**
     * @dataProvider provideFix83Cases
     *
     * @requires PHP 8.3
     */
    public function testFix83(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideFix83Cases(): iterable
    {
        yield 'typed constant' => [
            <<<'PHP'
                <?php
                class Foo1  { const array          NULL  = [];    }
                class Foo2  { const int            NULL  = 0;     }
                class Foo3  { const mixed          NULL  = 0;     }
                class Foo4  { const string         NULL  = '';    }
                class Foo5  { const Foo|null       NULL  = null;  }
                class Foo6  { const null|Foo       NULL  = null;  }
                class Foo7  { const null|(Foo&Bar) NULL  = null;  }
                class Foo8  { const bool           TRUE  = true;  }
                class Foo9  { const false          FALSE = false; }
                class Foo10 { const true           TRUE  = true;  }
                PHP,
            <<<'PHP'
                <?php
                class Foo1  { const array          NULL  = [];    }
                class Foo2  { const int            NULL  = 0;     }
                class Foo3  { const mixed          NULL  = 0;     }
                class Foo4  { const string         NULL  = '';    }
                class Foo5  { const Foo|NULL       NULL  = NULL;  }
                class Foo6  { const NULL|Foo       NULL  = NULL;  }
                class Foo7  { const NULL|(Foo&Bar) NULL  = NULL;  }
                class Foo8  { const bool           TRUE  = TRUE;  }
                class Foo9  { const FALSE          FALSE = FALSE; }
                class Foo10 { const TRUE           TRUE  = TRUE;  }
                PHP,
        ];
    }
}
