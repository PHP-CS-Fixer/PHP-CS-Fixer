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

namespace PhpCsFixer\Tests\RuleSet\Sets;

use PhpCsFixer\RuleSet\RuleSets;

/**
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\Sets\PERCSRiskySet
 */
final class PERCSRiskySetTest extends AbstractSetTest
{
    function testPointsToLatestPERCSRiskySet(): void
    {
        $percsSets =array_filter(RuleSets::getSetDefinitionNames(), fn (string $s): bool =>
            strpos($s, '@PER-CS') === 0
            && strpos($s, 'risky') !== false
            && $s !== '@PER-CS:risky'
        );
        $latest = array_pop($percsSets);

        $set = self::getSet();

        self::assertTrue($set->isRisky());
        self::assertSame([$latest => true], $set->getRules());
    }
}
