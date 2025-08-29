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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer\Analysis;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\AttributeAnalysis;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\AttributeAnalysis
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AttributeAnalysisTest extends TestCase
{
    public function testAttribute(): void
    {
        $attributes = [
            ['start' => 3, 'end' => 12, 'name' => 'AB\Baz'],
            ['start' => 14, 'end' => 32, 'name' => '\A\B\Qux'],
        ];
        $analysis = new AttributeAnalysis(2, 34, 3, 34, $attributes);

        self::assertSame(2, $analysis->getStartIndex());
        self::assertSame(34, $analysis->getEndIndex());
        self::assertSame(3, $analysis->getOpeningBracketIndex());
        self::assertSame(34, $analysis->getClosingBracketIndex());
        self::assertSame($attributes, $analysis->getAttributes());
    }
}
