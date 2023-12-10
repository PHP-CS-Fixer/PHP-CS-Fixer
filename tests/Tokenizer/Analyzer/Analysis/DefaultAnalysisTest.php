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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis
 *
 * @internal
 */
final class DefaultAnalysisTest extends TestCase
{
    public function testDefault(): void
    {
        $analysis = new DefaultAnalysis(15, 16);

        self::assertSame(15, $analysis->getIndex());
        self::assertSame(16, $analysis->getColonIndex());
    }
}
