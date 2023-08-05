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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tests\Fixtures\FunctionReferenceTestFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 */
final class AbstractFunctionReferenceFixerTest extends TestCase
{
    /**
     * @param null|int[] $expected
     *
     * @dataProvider provideAbstractFunctionReferenceFixerCases
     */
    public function testAbstractFunctionReferenceFixer(
        ?array $expected,
        string $source,
        string $functionNameToSearch,
        int $start = 0,
        ?int $end = null
    ): void {
        $fixer = new FunctionReferenceTestFixer();

        self::assertTrue($fixer->isRisky());

        $tokens = Tokens::fromCode($source);

        self::assertSame(
            $expected,
            $fixer->findTest(
                $functionNameToSearch,
                $tokens,
                $start,
                $end
            )
        );

        self::assertFalse($tokens->isChanged());
    }

    public static function provideAbstractFunctionReferenceFixerCases(): iterable
    {
        yield 'simple case I' => [
            [1, 2, 3],
            '<?php foo();',
            'foo',
        ];

        yield 'simple case II' => [
            [2, 3, 4],
            '<?php \foo();',
            'foo',
        ];

        yield 'test start offset' => [
            null,
            '<?php
                    foo();
                    bar();
                ',
            'foo',
            5,
        ];

        yield 'test returns only the first candidate' => [
            [2, 3, 4],
            '<?php
                    foo();
                    foo();
                    foo();
                    foo();
                    foo();
                ',
            'foo',
        ];

        yield 'not found I' => [
            null,
            '<?php foo();',
            'bar',
        ];

        yield 'not found II' => [
            null,
            '<?php $foo();',
            'foo',
        ];

        yield 'not found III' => [
            null,
            '<?php function foo(){}',
            'foo',
        ];

        yield 'not found IIIb' => [
            null,
            '<?php function foo($a){}',
            'foo',
        ];

        yield 'not found IV' => [
            null,
            '<?php \A\foo();',
            'foo',
        ];
    }
}
