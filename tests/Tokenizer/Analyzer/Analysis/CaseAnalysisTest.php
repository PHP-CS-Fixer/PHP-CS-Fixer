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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(CaseAnalysis::class)]
final class CaseAnalysisTest extends TestCase
{
    public function testCase(): void
    {
        $analysis = new CaseAnalysis(12, 20);

        self::assertSame(12, $analysis->getIndex());
        self::assertSame(20, $analysis->getColonIndex());
    }
}
