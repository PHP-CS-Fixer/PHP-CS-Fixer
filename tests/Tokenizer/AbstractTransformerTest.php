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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\Fixtures\Test\AbstractTransformerTest\FooTransformer;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\AbstractTransformer;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\AbstractTransformer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(AbstractTransformer::class)]
final class AbstractTransformerTest extends TestCase
{
    public function testNameAndPriorityDefault(): void
    {
        $transformer = new FooTransformer();

        self::assertSame(0, $transformer->getPriority());
        self::assertSame('foo', $transformer->getName());
    }

    public function testCustomTokens(): void
    {
        $transformer = new FooTransformer();

        self::assertSame([], $transformer->getCustomTokens());
    }
}
