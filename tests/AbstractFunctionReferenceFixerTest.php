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
    private $fixer;

    protected function setUp(): void
    {
        $this->fixer = new FunctionReferenceTestFixer();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->fixer = null;

        parent::tearDown();
    }

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
        static::assertTrue($this->fixer->isRisky());

        $tokens = Tokens::fromCode($source);

        static::assertSame(
            $expected,
            $this->fixer->findTest(
                $functionNameToSearch,
                $tokens,
                $start,
                $end
            )
        );

        static::assertFalse($tokens->isChanged());
    }

    public function provideAbstractFunctionReferenceFixerCases(): array
    {
        return [
            'simple case I' => [
                [1, 2, 3],
                '<?php foo();',
                'foo',
            ],
            'simple case II' => [
                [2, 3, 4],
                '<?php \foo();',
                'foo',
            ],
            'test start offset' => [
                null,
                '<?php
                    foo();
                    bar();
                ',
                'foo',
                5,
            ],
            'test returns only the first candidate' => [
                [2, 3, 4],
                '<?php
                    foo();
                    foo();
                    foo();
                    foo();
                    foo();
                ',
                'foo',
            ],
            'not found I' => [
                null,
                '<?php foo();',
                'bar',
            ],
            'not found II' => [
                null,
                '<?php $foo();',
                'foo',
            ],
            'not found III' => [
                null,
                '<?php function foo(){}',
                'foo',
            ],
            'not found IIIb' => [
                null,
                '<?php function foo($a){}',
                'foo',
            ],
            'not found IV' => [
                null,
                '<?php \A\foo();',
                'foo',
            ],
        ];
    }
}
