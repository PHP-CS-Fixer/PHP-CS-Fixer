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
 * @covers \PhpCsFixer\Tokenizer\Transformer\ConstructorPromotionTransformer
 */
final class ConstructorPromotionTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     * @requires PHP 8.0
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
            ]
        );
    }

    public function provideProcessCases(): \Generator
    {
        yield [
            '<?php
class Point {
    public function __construct(
        public float $x = 0.0,
        protected float $y = 0.0,
        private float $z = 0.0,
    ) {}
}
',
            [
                14 => CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
                25 => CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
                36 => CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
            ],
        ];

        yield [
            '<?php $a = new class {function/* 1 */__CONSTRUCT/* 2 */(/* 3 */public float $x,protected float $y,private float $z) {}};',
            [
                16 => CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
                22 => CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
                28 => CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
            ],
        ];
    }

    public function testNotChange(): void
    {
        $code = '<?php
            // class Foo1 {
            //     function __construct(
            //         private float $z = new class {
            //             public function __construct() {}
            //         }
            //     ) {}
            // }

            // class Foo2 {
            //     function __construct(
            //         private array $z = [new class {}],
            //     ) {}
            // }

            // class Foo3 {
            //     public function __construct(
            //         public float $x = 0.0,
            //         protected float $y = 0.0,
            //         private float $z = 0.0,
            //     ) {}
            // }

            function __construct(/* public */ $foo){}

            class Foo4 {
                public function construct(/* public */ $foo)
                {}
            }

            class Foo5 {
                public $foo1;
                protected $foo2;
                private $foo3;

                public function __construct(/* public */ $foo){} public $foo4;
            }
        ';

        Tokens::clearCache();

        foreach (Tokens::fromCode($code) as $token) {
            static::assertFalse($token->isGivenKind([
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
                CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
            ]));
        }
    }
}
