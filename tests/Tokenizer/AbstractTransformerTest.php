<?php

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

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\AbstractTransformer
 */
final class AbstractTransformerTest extends TestCase
{
    public function testAbstractTransformer()
    {
        $transformer = new FooTransformer();

        static::assertSame(0, $transformer->getPriority());
        static::assertSame('foo', $transformer->getName());
    }
}
