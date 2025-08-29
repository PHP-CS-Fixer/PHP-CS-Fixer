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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\AttributeTransformer
 *
 * @phpstan-import-type _TransformerTestExpectedKindsUnderIndex from AbstractTransformerTestCase
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AttributeTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcessCases
     *
     * @requires PHP 8.0
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens);
    }

    /**
     * @return iterable<int, array{string, _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcessCases(): iterable
    {
        yield ['<?php class Foo {
    #[Listens(ProductCreatedEvent::class)]
    public $foo;
}
',
            [
                14 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield ['<?php class Foo {
    #[Required]
    public $bar;
}',
            [
                9 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield [
            '<?php function foo(
    #[MyAttr([1, 2])] Type $myParam,
) {}',
            [
                16 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield [
            '<?php class Foo {
                #[ORM\Column("string", ORM\Column::UNIQUE)]
                #[Assert\Email(["message" => "The email {{ value }} is not a valid email."])]
                private $email;
            }',
            [
                21 => CT::T_ATTRIBUTE_CLOSE,
                36 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield [
            '<?php
#[ORM\Id]

#[ConditionalDeclare(PHP_VERSION_ID < 70000+1**2-1>>9+foo(a)+foo((bool)$b))] // gets removed from AST when >= 7.0
#[IgnoreRedeclaration] // throws no error when already declared, removes the redeclared thing
function intdiv(int $numerator, int $divisor) {
}

#[
Attr1("foo"),Attr2("bar"),
]

#[PhpAttribute(self::IS_REPEATABLE)]
class Route
{
}
',
            [
                5 => CT::T_ATTRIBUTE_CLOSE,
                35 => CT::T_ATTRIBUTE_CLOSE,
                41 => CT::T_ATTRIBUTE_CLOSE,
                76 => CT::T_ATTRIBUTE_CLOSE,
                85 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield [
            '<?php
#[Jit]
function foo() {}

class Foo
{
    #[ExampleAttribute]
    public const FOO = "foo";

    #[ExampleAttribute]
    public function foo(#[ExampleAttribute] Type $bar) {}
}

$object = new #[ExampleAttribute] class () {};

$f1 = #[ExampleAttribute] function () {};

$f2 = #[ExampleAttribute] fn() => 1;
',
            [
                3 => CT::T_ATTRIBUTE_CLOSE,
                22 => CT::T_ATTRIBUTE_CLOSE,
                37 => CT::T_ATTRIBUTE_CLOSE,
                47 => CT::T_ATTRIBUTE_CLOSE,
                67 => CT::T_ATTRIBUTE_CLOSE,
                84 => CT::T_ATTRIBUTE_CLOSE,
                101 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];

        yield [
            '<?php
#[
    ORM\Entity,
    ORM\Table("user")
]
class User
{
    #[ORM\Id, ORM\Column("integer"), ORM\GeneratedValue]
    private $id;

    #[ORM\Column("string", ORM\Column::UNIQUE)]
    #[Assert\Email(["message" => "The email \'{{ value }}\' is not a valid email."])]
    private $email;

    #[\Doctrine\ORM\ManyToMany(
        targetEntity: User::class,
        joinColumn: "group_id",
        inverseJoinColumn: "user_id",
        cascade: array("persist", "remove")
    )]
    #[Assert\Valid]
    #[JMSSerializer\XmlList(inline: true, entry: "user")]
    public $users;
}
',
            [
                15 => CT::T_ATTRIBUTE_CLOSE,
                40 => CT::T_ATTRIBUTE_CLOSE,
                61 => CT::T_ATTRIBUTE_CLOSE,
                76 => CT::T_ATTRIBUTE_CLOSE,
                124 => CT::T_ATTRIBUTE_CLOSE,
                130 => CT::T_ATTRIBUTE_CLOSE,
                148 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];
    }

    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcess85Cases
     *
     * @requires PHP 8.5
     */
    public function testProcess85(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens);
    }

    /**
     * @return iterable<int, array{string, _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcess85Cases(): iterable
    {
        yield [
            <<<'PHP'
                <?php
                #[Foo([static function (#[SensitiveParameter] $a) {
                    return [fn (#[Bar([1, 2])] $b) => [$b[1]]];
                }])]
                class Baz {}
                PHP,
            [
                12 => CT::T_ATTRIBUTE_CLOSE,
                35 => CT::T_ATTRIBUTE_CLOSE,
                54 => CT::T_ATTRIBUTE_CLOSE,
            ],
        ];
    }

    /**
     * @dataProvider provideNotChangeCases
     */
    public function testNotChange(string $source): void
    {
        Tokens::clearCache();

        foreach (Tokens::fromCode($source) as $token) {
            self::assertFalse($token->isGivenKind([
                CT::T_ATTRIBUTE_CLOSE,
            ]));
        }
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideNotChangeCases(): iterable
    {
        yield [
            '<?php
                $foo = [];
                $a[] = $b[1];
                $c = $d[2];
                // [$e] = $f;',
        ];

        yield [
            '<?php [$e] = $f;',
        ];
    }
}
