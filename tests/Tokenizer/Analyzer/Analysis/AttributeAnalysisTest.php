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
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\AttributeAnalysis
 */
final class AttributeAnalysisTest extends TestCase
{
    public function testCreating(): void
    {
        $analysis = new AttributeAnalysis(7, 22);

        self::assertSame(7, $analysis->getStartIndex());
        self::assertSame(22, $analysis->getEndIndex());
    }
}
