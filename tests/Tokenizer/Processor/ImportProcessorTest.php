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

namespace PhpCsFixer\Tests\Tokenizer\Processor;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Processor\ImportProcessor;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Processor\ImportProcessor
 */
final class ImportProcessorTest extends TestCase
{
    /**
     * @param class-string $symbol
     *
     * @dataProvider provideTokenizeNameCases
     */
    public function testTokenizeName(string $symbol): void
    {
        self::assertSame(
            $symbol,
            implode(
                '',
                array_map(
                    static fn (Token $token): string => $token->getContent(),
                    ImportProcessor::tokenizeName($symbol)
                )
            )
        );
    }

    /**
     * @return iterable<array{0: string}>
     */
    public static function provideTokenizeNameCases(): iterable
    {
        yield [__CLASS__];

        yield ['Foo\\Bar'];

        yield ['\\Foo\\Bar'];

        yield ['FooBar'];

        yield ['\\FooBar'];

        yield ['\\Foo\\Bar\\Baz\\Buzz'];

        yield ['\\Foo1\\Bar_\\baz\\buzz'];
    }

    /**
     * @param array{
     *      const?: array<int|string, class-string>,
     *      class?: array<int|string, class-string>,
     *      function?: array<int|string, class-string>
     *  } $imports
     *
     * @dataProvider provideInsertImportsCases
     */
    public function testInsertImports(string $expected, string $input, array $imports, int $atIndex): void
    {
        $processor = new ImportProcessor(new WhitespacesFixerConfig());
        $tokens = Tokens::fromCode($input);
        $processor->insertImports($tokens, $imports, $atIndex);

        self::assertSame($expected, $tokens->generateCode());
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: array{class?: list<string>, const?: list<string>, function?: list<string>}, 3: int}>
     */
    public static function provideInsertImportsCases(): iterable
    {
        yield 'class import in single namespace' => [
            '<?php

namespace Foo;
use Other\A;
use Other\B;
',
            '<?php

namespace Foo;
',
            [
                'class' => ['Other\\A', 'Other\\B'],
            ],
            6,
        ];

        yield 'class import in single {} namespace' => [
            '<?php

namespace Foo {
use Other\A;
use Other\B;
}
',
            '<?php

namespace Foo {
}
',
            [
                'class' => ['Other\\A', 'Other\\B'],
            ],
            7,
        ];
    }
}
