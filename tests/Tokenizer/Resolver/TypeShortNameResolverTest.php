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

namespace PhpCsFixer\Tests\Tokenizer\Resolver;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver
 */
final class TypeShortNameResolverTest extends TestCase
{
    /**
     * @dataProvider provideResolverCases
     */
    public function testResolver(string $code, string $type, string $expected): void
    {
        $resolver = new TypeShortNameResolver();
        $tokens = Tokens::fromCode($code);

        static::assertSame($expected, $resolver->resolve($tokens, $type));
    }

    public function provideResolverCases(): array
    {
        return [
            [
                '<?php ',
                'SomeType',
                'SomeType',
            ],
            [
                '<?php ',
                'string',
                'string',
            ],
            [
                '<?php namespace Foo;',
                'Foo\\Bar',
                'Bar',
            ],
            [
                '<?php namespace Foo;',
                'Foo\\Bar\\Baz',
                'Bar\\Baz',
            ],
            [
                '<?php use Foo\\SomeUse;',
                'Foo\\SomeUse',
                'SomeUse',
            ],
            [
                '<?php use Foo\\SomeUse as SomeAlias;',
                'Foo\\SomeUse',
                'SomeAlias',
            ],
            [
                '<?php use Foo\\SomeUse;',
                'Foo\\SomeUse\\Bar',
                'SomeUse\\Bar',
            ],
            [
                '<?php use Foo\\SomeUse as SomeAlias;',
                'Foo\\SomeUse\\Bar',
                'SomeAlias\\Bar',
            ],
        ];
    }
}
